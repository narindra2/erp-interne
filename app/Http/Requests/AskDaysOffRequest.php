<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AskDaysOffRequest extends FormRequest
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
            'start_date' => ['required', 'date'],
            'start_date_is_morning' => ['required'],
            'return_date' => ['required', 'date'],
            'return_date_is_morning' => ['required'],
            'reason' => ['required']
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'start_date.required' => "La date de début est obligatoire",
    //         'start_date.date' => "La date de début doit être une date valide",
    //         'start_date_is_morning' => "Veuillez specifier le type de "
    //     ];
    // }
}
