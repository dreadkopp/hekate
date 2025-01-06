<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Routing;
use Database\Factories\UserFactory;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class ProxyTest extends TestCase
{
   use DatabaseTruncation;
   
   protected Process $pseudoServer;

   protected function setUp(): void
   {
       parent::setUp();
       Cache::store('apc')->flush();
       Cache::flush();


       $this->pseudoServer = new Process(['php', '-S', '0.0.0.0:7000', base_path('/tests/Fixtures/mockServer.php')],'/tmp');
       $this->pseudoServer->start();
       
       usleep(100);

   }
   
   protected function tearDown(): void
   {
       $this->pseudoServer->stop();
       parent::tearDown();
   }

    public function testRoutingMatchesCorrectPath(): void
    {
        Routing::query()
            ->create([
                'path' => '/test-server',
                'endpoint' => 'http://127.0.0.1:7000',
                    'skip_auth' => true
            ]
            );

        $this->artisan('route:list')
            ->expectsOutputToContain('/test-server')
            ->assertSuccessful()
            ->run();


        $this->get('/test-server')
            ->assertOk()
            ->assertSee('Requested Route: /');

    }

    public function testRoutingMatchesCorrectSubPath(): void
    {

        Routing::query()
            ->create([
                    'path' => '/test-server',
                    'endpoint' => 'http://127.0.0.1:7000',
                    'skip_auth' => true
                ]
            );

        $this->get('/test-server/foo')
            ->assertOk()
            ->assertSee('Requested Route: /foo');

        $this->get('/test-server/foo/bar')
            ->assertOk()
            ->assertSee('Requested Route: /foo/bar');

        $this->get('/test-server/foo/bar/baz-route')
            ->assertOk()
            ->assertSee('Requested Route: /foo/bar/baz-route');



    }

    public function testRoutingNeedToken(): void
    {
        Routing::query()
            ->create([
                    'path' => '/test-server',
                    'endpoint' => 'http://127.0.0.1:7000',
                    'skip_auth' => false
                ]
            );

        $this->get('/test-server/')
            ->assertUnauthorized();

    }

    public function testRoutingAllowsProxyForUser(): void
    {
        Routing::query()
            ->create([
                    'path' => '/test-server',
                    'endpoint' => 'http://127.0.0.1:7000',
                    'skip_auth' => false
                ]
            );

        $user = UserFactory::new()->createOne();
        $token = $user->createToken('test-token');

        Sanctum::$accessTokenRetrievalCallback = null;
        $user->withAccessToken($token->accessToken);
        $this->actingAs($user,'sanctum');

        $this
            ->withHeaders(['Authorization' => 'Bearer ' . $token->plainTextToken])
            ->get('/test-server/')
            ->assertSee('Requested Route: /')
            ->assertSee('x-authenticable-type: hekate-user')
            ->assertSee('x-authenticable: ');

        // TODO: check that x-authenticable has correct data

    }


    public function testRoutingAllowsProxyForClient(): void
    {
        Routing::query()
            ->create([
                    'path' => '/test-server',
                    'endpoint' => 'http://127.0.0.1:7000',
                    'skip_auth' => false
                ]
            );

        $client = new Client();
        $client->name = 'test-client';
        $client->save();
        $token = $client->createToken('test-token');

        Sanctum::$accessTokenRetrievalCallback = null;
        $client->withAccessToken($token->accessToken);
        $this->actingAs($client,'sanctum');

        $this
            ->withHeaders(['Authorization' => 'Bearer ' . $token->plainTextToken])
            ->get('/test-server/')
            ->assertSee('Requested Route: /')
            ->assertSee('x-authenticable-type: hekate-client')
            ->assertSee('x-authenticable: ');

        // TODO: check that x-authenticable has correct data
    }
}
