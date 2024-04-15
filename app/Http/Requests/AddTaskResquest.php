<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddTaskResquest extends FormRequest
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
            'title' => ['required'],
            'users' => ['required'],
            'label' => ['required'],
        ];
        if (request("task_id") ) {
            $rules['status_id'] = ['required'];
        }
        if (request("recurring_type")) {
            if (request("recurring_type") == "every_nb_day") {
                $rules["nb_days"] ='required|numeric|min:2|max:365'; /** if min = 1 mean task every_day  */
            }
            if (request("recurring_type") == "every_day_on") {
                $rules["day_of_week"] ='required|numeric|min:1|max:6'; /** lundi -> Samedi :  1-> 6 */
            }
        }
        if (request("start_deadline_date") || request("end_deadline_date")) {
            $rules["start_deadline_date"] ='date_format:d/m/Y|before_or_equal:end_deadline_date';
            $rules["end_deadline_date"] ='date_format:d/m/Y|after_or_equal:start_deadline_date';
        }
        if (request("start_date_recurring")) {
            $rules["start_date_recurring"] ="date_format:d/m/Y";
        }
        return $rules;
    }
    public function messages()
    {
        return [
            'users.required' => "Veuillez assigné un/plus utilisateur(s) sur ce tâche.",
            'nb_jr.required' => "Veuillez insérer le nb jour du repetion du tâche.",
            'day_of_week.required' => "Veuillez ajouter le jour de repetion du tâche .",
            'day_of_week.max' => trans("lang.error_occured"),
            'day_of_week.min' => trans("lang.error_occured"),
            'start_deadline_date.before_or_equal' => "La date de debut de deadline doit etre une date anterieur de fin deadline ",
            'end_deadline_date.after_or_equal' =>"La date fin de deadline doit etre une date posterieur ou égal de debut deadline ",
        ];
    }
    public function withValidator($validator)
    {
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->first()]));
        }
        if (request("start_deadline_date") && request("recurring_type") != "0"   ) {
            die(json_encode(["success" => false, "validation" => true,  "message" => "Il est impossible de gerer une tache recurente et à la fois une deadline."]));
        }
    }
}
