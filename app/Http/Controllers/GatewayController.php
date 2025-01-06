<?php

namespace App\Http\Controllers;

use App\Models\Routing;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\UnauthorizedException;
use Laravel\Sanctum\HasApiTokens;
use Psr\Http\Message\ResponseInterface;

class GatewayController
{
    /**
     * @throws AuthenticationException
     * @throws UnauthorizedException
     * @throws GuzzleException
     */
    public function proxy(Request $request, string $path = '/'): Response
    {
        $routing = $this->getMatchingRoute($request);
        $this->checkAuth($request, $routing, $path);
        $headers = $this->prepareHeaders($request);
        $response = $this->sendRequest($routing, $request, $path, $headers);

        if (!$response) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return new Response(
            $response->getBody(),
            $response->getStatusCode(),
            array_merge($response->getHeaders(), [
                'authenticable' => json_encode($request->user()?->toArray()),
                'authenticable_type' => $request->user()?->getMorphClass(),
            ])
        );
    }

    protected function getMatchingRoute(Request $request): Routing
    {
        $extractSubStringBeforeSecondSlash = fn(string $s) => substr($s, 0, strpos($s, '/', strpos($s, '/') + 1) ?: strlen($s));

        $basePath = $extractSubStringBeforeSecondSlash($request->getPathInfo());

        return Cache::store('apc')->remember(
            "route-lookup:$basePath",
            3600,
            fn() => Cache::remember(
                "route-lookup:$basePath",
                3600,
                fn() => Routing::query()
                    ->where('path', $basePath)
                    ->firstOrFail()
            )
        );
    }

    /**
     * @throws AuthenticationException
     * @throws UnauthorizedException
     */
    protected function checkAuth(Request $request, Routing $routing, string $path): void
    {
        if ($routing->skip_auth) {
            return;
        }

        /** @var HasApiTokens $user */
        $user = $request->user();

        if (!$user) {
            throw new AuthenticationException();
        }

        $token = $user->currentAccessToken();

        $fullPath = $routing->path . '/' . $path;


        foreach ($token->abilities ?? [] as $ability) {
            if ($this->isAbilityMatched($ability, $fullPath)){
                return;
            }
        }

        throw new UnauthorizedException();
    }

    protected function isAbilityMatched(string $ability, string $path): bool
    {
        if ($ability === $path) {
            return true;
        }

        if ($ability === '*') {
            return true;
        }

        if (!str_ends_with($ability,'*')) {
            return false;
        }

        return str_starts_with($path, rtrim($ability, '*'));
    }

    public function prepareHeaders(Request $request): array
    {
        // add authenticated
        $headers = $request->headers->all();

        // clear old target
        unset($headers['host']);

        // prevent spoofing
        unset($headers['authenticable']);
        unset($headers['authenticable_type']);

        $headers['authenticable'] = json_encode($request->user()?->toArray());
        $headers['authenticable_type'] = $request->user()?->getMorphClass();

        return $headers;
    }

    public function sendRequest(Routing $routing, Request $request, string $path, array $headers): ?ResponseInterface
    {
        try {
            return $this->getGuzzleClient($routing)
                ->request(
                $request->method(),
                $path,
                ['headers' => $headers]
            );
        } catch (RequestException $exception) {
            return $exception->getResponse();
        } catch (GuzzleException $exception) {
            return null;
        }
    }

    protected function getGuzzleClient(Routing $routing): Client
    {
        $serviceName = $routing->path;
        $app = App::getInstance();

        if ($app->has("$serviceName-client")) {
            return $app->get("$serviceName-client");
        }

        $client = new Client([
            'base_uri' => $routing->endpoint,
            'headers' => [
                'Connection' => 'keep-alive',
            ],
        ]);

        $app->instance("$serviceName-client", $client);

        return $client;
    }
}
