<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    private function getContact(User $user, int $contactId)
    {
        $contact = Contact::where('user_id', $user->id)->where('id', $contactId)->first();

        if (!$contact) {
            $response =  response()->json([
                'success' => false,
                'errors' => [
                    'message' => ['Contact not found']
                ]
            ], 404);

            throw new HttpResponseException($response);
        }

        return $contact;
    }

    private function getAddress(Contact $contact, int $addressId)
    {
        $address = Address::where('contact_id', $contact->id)->where('id', $addressId)->first();

        if (!$address) {
            $response =  response()->json([
                'success' => false,
                'errors' => [
                    'message' => ['Address not found']
                ]
            ], 404);

            throw new HttpResponseException($response);
        }

        return $address;
    }

    public function store(StoreAddressRequest $request, int $contactId)
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $contactId);

        $data = $request->validated();

        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return response()->json([
            'success' => true,
            'data' => new AddressResource($address),
        ], 201);
    }

    public function get(int $contactId, int $addressId)
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $contactId);
        $address = $this->getAddress($contact, $addressId);

        return response()->json([
            'success' => true,
            'data' => new AddressResource($address),
        ]);
    }

    public function update(UpdateAddressRequest $request, int $contactId, int $addressId)
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $contactId);
        $address = $this->getAddress($contact, $addressId);

        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return response()->json([
            'success' => true,
            'data' => new AddressResource($address),
        ]);
    }

    public function destroy(Request $request, int $contactId, int $addressId)
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $contactId);
        $address = $this->getAddress($contact, $addressId);

        $address->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function list(Request $request, int $contactId)
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $contactId);

        $addresses = Address::where('contact_id', $contact->id)->get();

        return response()->json([
            'success' => true,
            'data' => AddressResource::collection($addresses),
        ]);
    }
}
