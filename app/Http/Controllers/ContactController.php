<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    private function getContact(User $user, int $id)
    {
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();

        if (!$contact) {
            $response = response()->json([
                'success' => false,
                'errors' => [
                    'message' => ['Contact not found']
                ]
            ], 404);

            throw new HttpResponseException($response);
        }

        return $contact;
    }

    public function store(StoreContactRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return response()->json([
            'success' => true,
            'data' => new ContactResource($contact),
        ], 201);
    }

    public function get(Request $request, int $id)
    {
        $user = Auth::user();

        // $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();

        // if (!$contact) {
        //     $response = response()->json([
        //         'success' => false,
        //         'errors' => [
        //             'message' => ['Contact not found']
        //         ]
        //     ], 404);

        //     throw new HttpResponseException($response);
        // }

        $contact = $this->getContact($user, $id);

        return response()->json([
            'success' => true,
            'data' => new ContactResource($contact),
        ], 200);
    }

    public function update(UpdateContactRequest $request, int $id)
    {
        $data = $request->validated();
        $user = Auth::user();

        // $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();

        // if (!$contact) {
        //     $response = response()->json([
        //         'success' => false,
        //         'errors' => [
        //             'message' => ['Contact not found']
        //         ]
        //     ], 404);

        //     throw new HttpResponseException($response);
        // }

        $contact = $this->getContact($user, $id);

        $contact->fill($data);
        $contact->save();

        return response()->json([
            'success' => true,
            'data' => new ContactResource($contact),
        ], 200);
    }

    public function destory(Request $request, int $id)
    {
        $user = Auth::user();

        // $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();

        // if (!$contact) {
        //     $response = response()->json([
        //         'success' => false,
        //         'errors' => [
        //             'message' => ['Contact not found']
        //         ]
        //     ], 404);

        //     throw new HttpResponseException($response);
        // }

        $contact = $this->getContact($user, $id);

        $contact->delete();

        return response()->json([
            'success' => true,
            // 'data' => new ContactResource($contact),
        ], 200);
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);

        $contacts = Contact::query()->where('user_id', $user->id);

        $contacts = $contacts->where(function (Builder $builder) use ($request) {
            $name = $request->input('name');
            if ($name) {
                $builder->where(function (Builder $builder) use ($name) {
                    $builder->orWhere('first_name', 'like', '%' . $name . '%');
                    $builder->orWhere('last_name', 'like', '%' . $name . '%');
                });
            }

            $email = $request->input('email');
            if ($email) {
                $builder->where('email', 'like', "%$email%");
            }

            $phone = $request->input('phone');
            if ($phone) {
                $builder->where('phone', 'like', "%$phone%");
            }
        });

        $contacts = $contacts->paginate(perPage: $size, page: $page);

        return response()->json([
            'success' => true,
            'data' => new ContactCollection($contacts),
            'meta' => [
                'total' => $contacts->total(),
                'page' => $contacts->currentPage(),
                'size' => $contacts->perPage(),
            ]
        ], 200);

        // return new ContactCollection($contacts);
    }
}
