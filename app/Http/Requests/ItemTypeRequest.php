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
        $id = request("id") ? ",".request("id") : "";
        $rules["code"] = 'unique:item_types,code' . $id;
        if (request("sub_category") ==  ItemType::IMMOBILISATION) {
            $rules["category_id"] = 'required';
            $rules["code"] = 'required|unique:item_types,code' . $id;
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => "Le champ nom de l'article est requis",
            'code.required' => "Le champ code article est requis si c'est un article d'" .ItemType::IMMOBILISATION,
            'code.unique' => "Le code article a déjà été pris, c'est unique.",
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
