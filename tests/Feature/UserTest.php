<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $body = [
            'username' => 'alfred_wibowo',
            'password' => 'password',
            'name' => 'Alfred Wibowo',
        ];

        $this->post('/api/users', $body)
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'username' => 'alfred_wibowo',
                    'name' => 'Alfred Wibowo',
                ],
            ]);
    }

    public function testRegisterFailed()
    {
        $body = [
            'username' => '',
            'password' => '',
            'name' => '',
        ];

        $this->post('/api/users', $body)
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => ['Username is required'],
                    'password' => ['Password is required'],
                    'name' => ['Name is required'],
                ],
            ]);
    }

    public function testRegisterUsernameAlreadyExist()
    {
        $this->testRegisterSuccess();

        $body = [
            'username' => 'alfred_wibowo',
            'password' => 'password',
            'name' => 'Alfred Wibowo',
        ];

        $this->post('/api/users', $body)
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => ['Username must be unique'],
                ],
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([
            UserSeeder::class
        ]);

        $body = [
            'username' => 'test',
            'password' => 'test',
        ];

        $this->post('/api/users/login', $body)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test',
                ],
            ]);

        $user = User::where('username', 'test')->first();

        self::assertNotNull($user->token);
    }

    public function testLoginFailedUsernameNotFound()
    {
        $body = [
            'username' => 'test',
            'password' => 'test',
        ];

        $this->post('/api/users/login', $body)
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => ['username or password is wrong'],
                ],
            ]);
    }

    public function testLoginFailedPasswordWrong()
    {
        $body = [
            'username' => 'test',
            'password' => 'salah',
        ];

        $this->post('/api/users/login', $body)
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => ['username or password is wrong'],
                ],
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([
            UserSeeder::class
        ]);

        $header = [
            'Authorization' => 'test'
        ];

        $this->get('/api/users/current', [], $header)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test',
                ],
            ]);
    }

    public function testGetUnauthorize()
    {
        $this->seed([
            UserSeeder::class
        ]);

        $this->get('/api/users/current')
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => ['Unauthorized'],
                ],
            ]);
    }

    public function testGetInvalidToken()
    {
        $this->seed([
            UserSeeder::class
        ]);

        $header = [
            'Authorization' => 'salah'
        ];

        $this->get('/api/users/current', [], $header)
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => ['Unauthorized'],
                ],
            ]);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed([
            UserSeeder::class
        ]);

        $body = [
            'name' => 'Alfred Wibowo',
        ];

        $header = [
            'Authorization' => 'test'
        ];

        $this->patch('/api/users/current', $body, $header)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'Alfred Wibowo',
                ],
            ]);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed([
            UserSeeder::class
        ]);

        $oldUser = User::where('username', 'test')->first();

        $body = [
            'password' => 'newpassword',
        ];

        $header = [
            'Authorization' => 'test',
        ];

        $this->patch('/api/users/current', $body, $header)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test',
                ],
            ]);

        $newUser = User::where('username', 'test')->first();

        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateFailed()
    {
        $this->seed([
            UserSeeder::class
        ]);

        $body = [
            'name' => Str::random(101),
        ];

        $header = [
            'Authorization' => 'test',
        ];

        $this->patch('/api/users/current', $body, $header)
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => ['The name field must not be greater than 100 characters.'],
                ],
            ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed([
            UserSeeder::class
        ]);

        $header = [
            'Authorization' => 'test'
        ];

        $this->delete('/api/users/logout', [], $header)
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $user = User::where('username', 'test')->first();

        self::assertNull($user->token);
    }

    public function testLogoutFailed()
    {
        $this->seed([
            UserSeeder::class
        ]);

        $header = [
            'Authorization' => 'salah'
        ];

        $this->delete('api/users/logout', [], $header)
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => ['Unauthorized'],
                ]
            ]);
    }
}
