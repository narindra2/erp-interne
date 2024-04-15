<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectCerfaRequest extends FormRequest
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
        $rules['customer_type'] = 'required|integer';
        $rules['civility'] = 'required|integer';
        $rules['lastname'] = 'required';
        $rules['birthday'] = 'required|date_format:d/m/Y|before_or_equal:'.now()->format('d/m/Y');
        $rules['birthday_place'] = 'required';
        $rules['email'] = 'required|string|email';
        // project address
        $rules['way_number'] = 'required';
        $rules['locality'] = 'required';
        $rules['postal_code'] = 'required';
        $rules['town'] = 'required';

        if(request("address")==1){
            // curstomer address
            $rules['c_way_number'] = 'required';
            $rules['c_locality'] = 'required';
            $rules['c_postal_code'] = 'required';
            $rules['c_town'] = 'required';
        }

        if(request("customer_type_id")==2){
            $rules['denomination'] = 'required';
            $rules['social_reason'] = 'required';
            $rules['society_type'] = 'required';
            $rules['siret_number'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'customer_type.required' => "Le type d'utilisateur est obligatoire",
            'lastname.required' => "Le nom est obligatoire",
            'birthday.required' => "La date de naissance est obligatoire",
            'birthday.date' => "Ce champ doit être une date valide",
            'email.required' => "L'adresse mail est obligatoire",
            'email.email' => "Ce Champ doit être un mail",
            'way_number.required' => "Le numéro de la voie est obligatoire",
            'locality.required' => "Le nom de la voie est obligatoire",
            'postal_code.required' => "Le code postal est obligatoire",
            'town.required' => "Le nom de la commune est obligatoire",
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->first()]));
        }
    }
}
