<?php

namespace Database\Seeders;

use App\Models\Routing;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $user = UserFactory::new()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        dump($user->createToken('google-access' , ['/google*'])->plainTextToken);

        $routing = new Routing();

        $routing->path = '/google';
        $routing->endpoint = 'https://www.google.de';
        $routing->save();


        $routing = new Routing();
        $routing->path = '/status';
        $routing->endpoint = 'https://httpstat.us';
        $routing->skip_auth = true;

        $routing->save();

    }
}
