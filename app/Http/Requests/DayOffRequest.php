<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class DayOffRequest extends FormRequest
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
        // dd(request()->all());
        $rules = [];
        $rules['request_type'] = "required|in:daysoff,permission";
        $rules['type_id'] = "required";
        if (auth()->user()->isRhOrAdmin()) {
            $rules['start_date'] =  'required|date_format:d/m/Y';
        }else{
            $rules['start_date'] =  'required|date_format:d/m/Y|after_or_equal:'. Carbon::parse(Carbon::now()->addDay(7))->format("d/m/Y") ;
        }
        $rules['return_date'] = 'required|date_format:d/m/Y|after_or_equal:'. request('start_date');
        $rules['nature_id'] = 'required|integer|min:1';
        $rules['reason'] = 'required';
        return $rules;
    }

    public function messages()
    {
      return [
          'return_date.after_or_equal' => 'Le champ return date doit être une date postérieure ou egale '. request('start_date') .'.',
          'start_date.after_or_equal' => 'Il faut sept (07) jours avant pour une demande de congé.',
          'request_type.in' => 'Le type de demande doit être congé ou permission.',
          'nature_id.required' => 'La nature de la demande est obligatoire.',
          'nature_id.min' => 'La nature de l\' absence est obligatoire.',
          'reason.required' => 'La descirption de la demande est obligatoire.',
          'applicant_id.required' => 'L employé(e) est obligatoire.',
      ]  ;
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->all()]));
        }
    }
}
