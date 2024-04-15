<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavePointLevelTypeProject extends FormRequest
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
            'client_type_id' => 'required|min:1',
            'project_type_id' => 'required|min:1',
            'version_id' => 'required|min:1',
            'niveau' => 'required|min:1',
            'point' => 'required',
            'point_sup' => 'required',
        ];
    }

    public function messages() 
    {
        return [
           "client_type_id.required" => "Le champ type de client est obligatoire.",
           "client_type_id.required" => "Le champ type de client est obligatoire.",
           "version_id.required" => "Le champ type de version est obligatoire.",
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->all()]));
        }
    }
}
