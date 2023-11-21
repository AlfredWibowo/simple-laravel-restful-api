<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertJson;

class AddressTest extends TestCase
{
    public function testStoreSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $header = [
            'Authorization' => 'test'
        ];

        $body = [
            'street' => 'Jl. Raya Bogor',
            'city' => 'Bogor',
            'province' => 'Jawa Barat',
            'country' => 'Indonesia',
            'postal_code' => '16111',
        ];

        $this->post('/api/contacts/' . $contact->id . '/addresses', $body, $header)
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'street' => 'Jl. Raya Bogor',
                    'city' => 'Bogor',
                    'province' => 'Jawa Barat',
                    'country' => 'Indonesia',
                    'postal_code' => '16111',
                ]
            ]);
    }

    public function testStoreFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $header = [
            'Authorization' => 'test'
        ];

        $body = [
            'street' => 'Jl. Raya Bogor',
            'city' => 'Bogor',
            'province' => 'Jawa Barat',
            'country' => '',
            'postal_code' => '16111',
        ];

        $this->post('/api/contacts/' . $contact->id . '/addresses', $body, $header)
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => [
                        'The country field is required.'
                    ]
                ]
            ]);
    }

    public function testStoreContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $header = [
            'Authorization' => 'test'
        ];

        $body = [
            'street' => 'Jl. Raya Bogor',
            'city' => 'Bogor',
            'province' => 'Jawa Barat',
            'country' => 'Indonesia',
            'postal_code' => '16111',
        ];

        $this->post('/api/contacts/' . $contact->id + 1 . '/addresses', $body, $header)
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['Contact not found']
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->limit(1)->first();

        $header = [
            'Authorization' => 'test'
        ];

        $this->get('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, $header)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => $address->street,
                    'city' => $address->city,
                    'province' => $address->province,
                    'country' => $address->country,
                    'postal_code' => $address->postal_code,
                ]
            ]);
    }

    public function testGetAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->limit(1)->first();

        $header = [
            'Authorization' => 'test'
        ];

        $this->get('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id + 1, $header)
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['Address not found']
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $header = [
            'Authorization' => 'test'
        ];

        $body = [
            'street' => 'update',
            'city' => 'update',
            'province' => 'update',
            'country' => 'update',
            'postal_code' => '11111',
        ];

        $this->put('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, $body, $header)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'update',
                    'city' => 'update',
                    'province' => 'update',
                    'country' => 'update',
                    'postal_code' => '11111',
                ]
            ]);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $header = [
            'Authorization' => 'test'
        ];

        $body = [
            'street' => 'update',
            'city' => 'update',
            'province' => 'update',
            'country' => '',
            'postal_code' => '11111',
        ];

        $this->put('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, $body, $header)
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => [
                        'The country field is required.'
                    ]
                ]
            ]);
    }

    public function testUpdateNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $header = [
            'Authorization' => 'test'
        ];

        $body = [
            'street' => 'update',
            'city' => 'update',
            'province' => 'update',
            'country' => 'update',
            'postal_code' => '11111',
        ];

        $this->put('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id + 1, $body, $header)
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Address not found'
                    ]
                ]
            ]);
    }

    public function testDestroySuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $header = [
            'Authorization' => 'test'
        ];

        $this->delete('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, [], $header)
            ->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);
    }

    public function testDestroyFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $header = [
            'Authorization' => 'test'
        ];

        $this->delete('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id + 1, [], $header)
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Address not found'
                    ]
                ]
            ]);
    }

    public function testListSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $header = [
            'Authorization' => 'test'
        ];

        $this->get('/api/contacts/' . $contact->id . '/addresses', $header)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'street' => 'Jl. Raya Bogor',
                        'city' => 'Bogor',
                        'province' => 'Jawa Barat',
                        'country' => 'Indonesia',
                        'postal_code' => '16111',
                    ]
                ]
            ]);
    }

    public function testListContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $header = [
            'Authorization' => 'test'
        ];

        $this->get('/api/contacts/' . $contact->id + 1 . '/addresses', $header)
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['Contact not found']
                ]

            ]);
    }
}
