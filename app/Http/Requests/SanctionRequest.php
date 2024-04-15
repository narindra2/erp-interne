<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SanctionRequest extends FormRequest
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
            'reason' => 'required',
            'date' => 'required|date',
            'duration' => 'required|min:0',
            'user_id' => 'required'
        ];
    }

    public function messages() 
    {
        return [
            'reason.required' => 'Motif requis',
            'date.required' => 'Date requis',
            'date.date' => 'Date erronée',
            'duration.required' => 'Durée requis',
            'duration.min' => 'Durée de 0 jour minimum',
            'user_id.required' => 'Erreur de paramètre'
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->first()]));
        }
    }
}
