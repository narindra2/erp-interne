<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpgradeEmployeeDaysOffBalanceRequest extends FormRequest
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
            'nb' => ['required', 'numeric', 'min:0']
        ];
    }

    public function messages()
    {
        return [
            'nb.required' => "Ce champ est requis",
            'nb.numeric' => "Ce champ doit être un nombre",
            'nb.min' => 'Nombre inférieur à 0'
        ];
    }
    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->first()]));
        }
    }
}
