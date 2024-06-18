<?php

namespace App\Http\Requests;

use App\Models\ItemType;
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
        $rules = [
            'name' => 'required',
        ];
        if (request("sub_category") ==  ItemType::IMMOBILISATION) {
            $rules["code"] = 'required';
            $rules["category_id"] = 'required';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Le champ nom est requis',
            'code.required' => "Le champ code article  est requis si c'est un article d'" .ItemType::IMMOBILISATION,
            'category_id.required' => "Le champ catégorie article est requis si c'est un article d'" .ItemType::IMMOBILISATION,
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->all()]));
        }
    }
}
