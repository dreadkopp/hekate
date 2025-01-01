<?php

namespace App\Http\Controllers;

use App\Models\Routing;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Psr\Http\Message\ResponseInterface;

class GatewayController
{
    /**
     * @throws AuthenticationException
     * @throws UnauthorizedException
     * @throws GuzzleException
     */
    public function proxy(Request $request, string $path = '/') :ResponseInterface
    {
        $routing = $this->getMatchingRoute($request);

        $this->checkAuth($request, $routing, $path);

        $headers = $request->headers->all();
        unset($headers['host']);

        try {
            return $this
                ->getGuzzleClient($routing)
                ->request(
                    $request->method(),
                    $path,
                    [
                        'headers' => $headers
                    ]
                );
        } catch (RequestException $exception) {
            return $exception->getResponse();
        }

    }

    /**
     * @throws AuthenticationException
     * @throws UnauthorizedException
     */
    protected function checkAuth(Request $request, Routing $routing, string $path) : void
    {
        if ($routing->skip_auth) {
            return;
        }


        if (!$request->user()) {
            throw new AuthenticationException();
        }

        /** @var HasApiTokens $user */
        $user = $request->user();
        /** @var PersonalAccessToken $token */
        $token = $user->currentAccessToken();
        $path = $routing->path . '/' . $path;

        Log::debug("tokens", $token->abilities);

        foreach ($token->abilities as $match) {

            Log::debug("match", [$path,substr($match, 0, -1)]);
            if (str_ends_with($match, '*') && str_starts_with($path, substr($match, 0, -1))) {
                return;
            }

            if ($match === $path) {
                return;
            }
        }

        throw new UnauthorizedException();

    }

    protected function getGuzzleClient(Routing $routing): Client
    {
        $serviceName = $routing->path;
        if (App::has($serviceName . "-client")) {
            return App::get($serviceName . '-client');
        }

        $client = new Client(
            [
                'base_uri' => $routing->endpoint,
            ]
        );

        App::instance($serviceName . '-client', $client);

        return $client;
    }

    protected function getMatchingRoute(Request $request): Routing
    {
        $host = $request->getHost();
        $port = $request->getPort();
        $scheme = $request->getScheme();
        $gateway = $scheme . '://' . $host . ':' . $port . '/';

        $basePath =
            Str::before(
                Str::after($request->getUri(), $gateway),
                '/'
            );

        return Cache::store('apc')
            ->remember(
                'route-lookup:' . $basePath,
                3600,
                fn () => Cache::remember(
                    'route-lookup:' . $basePath,
                    3600,
                    fn() => Routing::query()->where('path', '/' . $basePath)->firstOrFail()
                )
            );

    }
}
