<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActorRequest extends FormRequest
{
    public function authorize(): bool
    {
        // A kliens oldali formot bárki beküldheti, de a controller
        // majd ellenőrzi, hogy van-e API token.
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'birth_date'  => ['nullable', 'date'],
            'gender'      => ['required', 'in:male,female,other'],
            'image'       => ['nullable', 'string', 'max:255'],
        ];
    }
}
