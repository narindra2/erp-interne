<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicHolidayRequest extends FormRequest
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
            'day' => 'required|date_format:d/m/Y'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => sprintf(trans("lang.is_required"), trans("lang.name")),
            'day.required' => sprintf(trans("lang.is_required"), trans("lang.day")),
            'day.date' => sprintf(trans("lang.is_date"), trans("lang.day"))
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->all()]));
        }
    }
}
