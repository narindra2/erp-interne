<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NeedToBuyRequest extends FormRequest
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
            'item_type_id' => 'required',
            'nb' => 'required|numeric|min:1'
        ];
    }

    public function messages()
    {
        return [
            'item_type_id.required' => 'Veuillez choisir un article',
            'nb.required' => 'Veuillez remplir le champ quantité',
            'nb.numeric' => 'Le champ quantité  doit être un nombre',
            'nb.min' => 'Le champ quantité  doit être un nombre positif'
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->all()]));
        }
    }
}
