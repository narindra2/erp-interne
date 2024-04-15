<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
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
        $rules['registration_number'] = ['required', 'numeric',Rule::unique('users')->where(function ($query) {
            $query = $query->whereDeleted(0);
            if (request("user_id")) {
                $query = $query->where("id","<>", request("user_id"));
            }
            return $query;
        })];
        $rules['name'] = 'required';
        $rules['sex'] = 'required';
        $rules['birthdate'] = 'required|date_format:d/m/Y|before_or_equal:'.now()->format('d/m/Y');
        $rules['place_of_birth'] = 'required';
        $rules['jobs_id'] = 'required';
        $rules['marital_status_id'] = 'required';
        $rules['address'] = 'required';
        $rules['phone_number'] = 'required';
        $rules['email'] = ['required', 'string', 'email', 'max:191',Rule::unique('users')->where(function ($query) {
            $query = $query->whereDeleted(0);
            if (request("user_id")) {
                $query = $query->where("id","<>", request("user_id"));
            }
            return $query;
        })];

        $rules['father_fullname'] = 'required';
        $rules['mother_fullname'] = 'required';
        $rules['contract_type_id'] = 'required';
        $rules['hiring_date'] = 'required|date_format:d/m/Y';
        $rules['salary'] = 'required|numeric|min:0';
        $rules['contract_type_id'] = 'required';
        $rules['user_type_id'] = 'required';
        $rules['local'] = 'required';
        $rules['category'] = 'required';
        $rules['group'] = 'required';
        $rules['nb_days_off_remaining'] = 'required|numeric';

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
            'birthdate.date' => "Ce champ doit être une date valide",
            'cin.required' => "Le CIN est obligatoire",
            'cin.numeric' => "Le CIN doit être un nombre",
            'email.required' => "L'adresse mail est obligatoire",
            'email.email' => "Ce Champ doit être un mail",

            'marital_status_id.required' => "Ce champ est obligatoire",
            "hiring_date.required" => "La date d'embauche est obligatoire",
            "salary.required" => "Le salaire est obligatoire",
            "nb_days_off_remaining.required" => "Champ obligatoire",
            "local.required" => "champ local obligatoire",
            "category.required" => "Champ catégorie obligatoire",
            "group.required" => "Champ groupe obligatoire",

        ];
    }
    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->first()]));
        }
    }
}
