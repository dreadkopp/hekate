<?php

namespace App\Http\Controllers;

use App\Models\Routing;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

class GatewayController
{
    public function proxy(Request $request, string $path = '/') :ResponseInterface
    {
        $routing = $this->getMatchingRoute($request);
//        if(!$routing->skip_auth) {
//            $this->checkAuth();
//        }

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
