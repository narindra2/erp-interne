<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemTypeRequest extends FormRequest
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
            'name' => 'required',
            'brand' => 'required',
            'item_category_id' => 'required',
            'unit_price' => 'required|numeric|min:0'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le champ nom est requis',
            'brand.required' => 'Le champ marque est requis',
            'item_category_id.required' => 'Veuillez choisir une catégorie',
            'unit_price.required' => 'Le champ prix est requis',
            'unit_price.numeric' => 'Le champ prix doit être un nombre',
            'unit_price.min' => 'Le prix doit être positif'
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->all()]));
        }
    }
}
