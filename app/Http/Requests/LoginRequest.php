<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'registration_number' => 'required',
            'password' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'registration_number.required' => "Le champ matricule est obligatoire",
            'password.required' => "Le champ mot de passe est obligatoire",
        ];
    }
}
