<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetailNeedRequest extends FormRequest
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
            'qty' => 'required|numeric|min:1',
            'status' => 'required',
            'item_type_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'qty.required' => 'Veuillez remplir le champ quantité',
            'qty.numeric' => 'Veuillez entrer entrer un nombre',
            'qty.min' => 'Erreur sur la quantité',
            'status.required' => 'Veuillez remplir le champ status',
            'item_type_id.required' => ''
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->first()]));
        }
    }
}
