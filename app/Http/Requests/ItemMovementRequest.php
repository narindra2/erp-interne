<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemMovementRequest extends FormRequest
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
            'item_id' => 'required',
            'location_id' => 'required',
            'item_status_id' => 'required'
        ];
    }

    public function messages() {
        return [
            'item_id.required' => 'Veuillez selectionner un article',
            'location_id.required' => 'Veuillez specifier le lieu',
            'item_status_id.required' => "Veuillez choisir le statut de l'article"
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->all()]));
        }
    }
}
