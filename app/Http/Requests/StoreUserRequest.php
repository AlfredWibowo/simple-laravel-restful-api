<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'max:100', 'unique:users'],
            'password' => ['required', 'max:100',],
            'name' => ['required', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Username is required',
            'username.max' => 'Username cannot be longer than 100 characters',
            'username.unique' => 'Username must be unique',
            'password.required' => 'Password is required',
            'password.max' => 'Password cannot be longer than 100 characters',
            'name.required' => 'Name is required',
            'name.max' => 'Name cannot be longer than 100 characters',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 400);

        throw new HttpResponseException($response);
    }
}
