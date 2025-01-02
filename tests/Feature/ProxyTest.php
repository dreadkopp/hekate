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

       try {
           mkdir('/tmp/test-server',recursive: true);
       }catch (Exception ) {
           // dont care
       }
       file_put_contents('/tmp/test-server/index.html', 'hello');
       $this->pseudoServer = new Process(['php', '-S', '0.0.0.0:7000'], '/tmp/test-server');
       $this->pseudoServer->start();

   }
   
   protected function tearDown(): void
   {
       unlink('/tmp/test-server/index.html');
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


        $this->get('/test-server/')
            ->assertOk()
            ->assertSee('hello');

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

        $headers = $this
            ->withHeaders(['Authorization' => 'Bearer ' . $token->plainTextToken])
            ->get('/test-server/')
            ->assertSee('hello')
            ->headers;

        self::assertEquals('kerberos-user', $headers->get('authenticable_type'));

        $authenticatedEntity = json_decode($headers->get('authenticable'), true);

        self::assertEquals($user->toArray(), $authenticatedEntity);
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

        $headers = $this
            ->withHeaders(['Authorization' => 'Bearer ' . $token->plainTextToken])
            ->get('/test-server/')
            ->assertSee('hello')
            ->headers;

        self::assertEquals('kerberos-client', $headers->get('authenticable_type'));

        $authenticatedEntity = json_decode($headers->get('authenticable'), true);

        self::assertEquals($client->toArray(), $authenticatedEntity);
    }
}
