<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testStoreSuccess()
    {
        $this->seed(UserSeeder::class);

        $body = [
            'first_name' => 'Alfred',
            'last_name' => 'Wibowo',
            'email' => 'wibowoalfred@gmail.com',
            'phone' => '081234567890',
        ];

        $header = [
            'Authorization' => 'test'
        ];

        $this->post('/api/contacts', $body, $header)
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'first_name' => 'Alfred',
                    'last_name' => 'Wibowo',
                    'email' => 'wibowoalfred@gmail.com',
                    'phone' => '081234567890',
                ]
            ]);
    }
    public function testStoreFailed()
    {
        $this->seed(UserSeeder::class);

        $body = [
            'first_name' => '',
            'last_name' => 'Wibowo',
            'email' => 'wibowoalfred',
            'phone' => '081234567890',
        ];

        $header = [
            'Authorization' => 'test'
        ];

        $this->post('/api/contacts', $body, $header)
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'The first name field is required.'
                    ],
                    'email' => [
                        'The email field must be a valid email address.'
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $header = [
            'Authorization' => 'test'
        ];

        $this->get('api/contacts/' . $contact->id, $header)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => $contact->first_name,
                    'last_name' => $contact->last_name,
                    'email' => $contact->email,
                    'phone' => $contact->phone,
                ]
            ]);
    }

    public function testGetContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $header = [
            'Authorization' => 'test'
        ];

        $this->get('api/contacts/' . $contact->id + 1, $header)
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['Contact not found']
                ]
            ]);
    }

    public function testGetOtherUserContact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $header = [
            'Authorization' => 'test2'
        ];

        $this->get('api/contacts/' . $contact->id, $header)
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['Contact not found']
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $body = [
            'first_name' => 'Alfred',
            'last_name' => 'Wibowo',
            'email' => 'alfred@gmail.com',
            'phone' => '081234567890',
        ];

        $header = [
            'Authorization' => 'test'
        ];

        $this->put('/api/contacts/' . $contact->id, $body, $header)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'Alfred',
                    'last_name' => 'Wibowo',
                    'email' => 'alfred@gmail.com',
                    'phone' => '081234567890',
                ]
            ]);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $body = [
            'first_name' => '',
            'last_name' => 'Wibowo',
            'email' => 'alfred@gmail.com',
            'phone' => '081234567890',
        ];

        $header = [
            'Authorization' => 'test'
        ];

        $this->put('/api/contacts/' . $contact->id, $body, $header)
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'The first name field is required.'
                    ],
                ]
            ]);
    }

    public function testDestroySuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $header = [
            'Authorization' => 'test'
        ];

        $this->delete('/api/contacts/' . $contact->id, [], $header)
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function testDestroyFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $header = [
            'Authorization' => 'test'
        ];

        $this->delete('/api/contacts/' . $contact->id + 1, [], $header)
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['Contact not found']
                ]
            ]);
    }

    public function testSearchByName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $header = [
            'Authorization' => 'test'
        ];

        $response = $this->get('/api/contacts?name=first', $header)
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
    }

    public function testSearchByEmail()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $header = [
            'Authorization' => 'test'
        ];

        $response = $this->get('/api/contacts?email=test', $header)
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
    }

    public function testSearchByPhone()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $header = [
            'Authorization' => 'test'
        ];

        $response = $this->get('/api/contacts?phone=11111', $header)
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
    }

    public function testSearchNotFound()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $header = [
            'Authorization' => 'test'
        ];

        $response = $this->get('/api/contacts?phone=tidakada', $header)
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(0, count($response['data']));
    }

    public function testSearchWithPage()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $header = [
            'Authorization' => 'test'
        ];

        $response = $this->get('/api/contacts?size=5&page=2', $header)
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(5, count($response['data']));
    }
}
