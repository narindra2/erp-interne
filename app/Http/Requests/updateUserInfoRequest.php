<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateUserInfoRequest extends FormRequest
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
        $rules = [];
        $rules['name'] = 'required';
        $rules['sex'] = 'required';
        $rules['birthdate'] = 'required|date_format:d/m/Y|before_or_equal:'.now()->format('d/m/Y');
        $rules['place_of_birth'] = 'required';
        $rules['marital_status_id'] = 'required';
        $rules['address'] = 'required';
        $rules['phone_number'] = 'required';
        $rules['father_fullname'] = 'required';
        $rules['mother_fullname'] = 'required';
        if(request("cin_delivered")) $rules['cin_delivered'] = 'date_format:d/m/Y|after:'.request('birthdate');
        if(request("marry_email")) $rules['marry_email'] = 'email|max:191';
        if(request("marry_birthdate")) $rules['marry_birthdate'] = 'date_format:d/m/Y|before_or_equal:'.now()->format('d/m/Y');
        if(request("marry_cin_delivered")) $rules['marry_cin_delivered'] = 'date_format:d/m/Y|after:'.request('marry_birthdate');

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => "Le nom est obligatoire",
            'sex.required' => 'Le champ sexe est introuvable',
            'address.required' => "L'adresse est obligatoire",
            'birthdate.required' => "La date de naissance est obligatoire",
            'birthdate.date' => "le  champ lieu de naissance doit être une date valide",
            'cin.required' => "Le CIN est obligatoire",
            'cin.numeric' => "Le CIN doit être un nombre",
            'email.required' => "L'adresse mail est obligatoire",
            'email.email' => "L'adresse mail  doit être un mail",
            'marital_status_id.required' => "Ce champ est obligatoire",
        ];
    }
    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->first()]));
        }
    }
}
