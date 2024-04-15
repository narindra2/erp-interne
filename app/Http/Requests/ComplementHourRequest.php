<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComplementHourRequest extends FormRequest
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
            'registration_number' => 'required',
            'additional_hour_type_id' => 'required',
            'minute_worked' => 'required',
            'day' => 'required|date'
        ];
    }

    public function messages() {
        return [
            'registration_number.required' => 'Veuillez selectionner un(e) employé(e1)',
            'additional_hour_type_id.required' => 'Veuillez selectionner le type',
            'minute_worked.required' => 'Veuillez saisir la durée',
            'day.required' => 'Veuillez choisir une date',
            'day.date' => 'Veuillez écrire une date valide'
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->first()]));
        }
    }
}
