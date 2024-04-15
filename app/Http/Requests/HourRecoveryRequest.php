<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HourRecoveryRequest extends FormRequest
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
            'user_id' => 'required',
            'job_id' => 'required',
            'date_of_absence' => 'required|date_format:d/m/Y',
            'duration_of_absence' => 'required|numeric',
            'recovery_start_date' => 'required|date_format:d/m/Y',
            'recovery_end_date' => 'required|date_format:d/m/Y',
            'description' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'date_of_absence' => trans('lang.date_of_absence'),
            'recovery_start_date.required' => trans("lang.recovery_date-required"),
            'recovery_end_date.required' => trans("lang.recovery_date-required"),
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->first()]));
        }
    }
}
