<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DaysOffTypeRequest extends FormRequest
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
            "name" =>'required',
            "type" =>'required',
            "description" =>'required',
            "nb_days" =>'required|numeric|min:0',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => sprintf(trans("lang.is_required") ,trans("lang.title")) ,
            'type.required' => sprintf(trans("lang.is_required") ,trans("lang.type")) ,
            'nb_days.required' => sprintf(trans("lang.is_required") ,trans("lang.nb_days")) ,
            'nb_days.numeric' => sprintf(trans("lang.is_numeric") ,trans("lang.nb_days")) ,
            'nb_days.min' => sprintf(trans("lang.dayOffMin"), trans("lang.nb_days"))
        ];
    }
    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->all()]));
        }
    }
}
