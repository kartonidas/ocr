<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'ocr@cashcode.pl';
        $password = env('APP_ENV') == 'local' ? 'test' : Str::random(20);

        User::create([
            'name' => 'Operator',
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        echo "$email / $password" . PHP_EOL;
    }
}
