<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffInfo extends FormRequest
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
        $rules = [];
        $rules['registration_number'] = ['required', 'numeric',Rule::unique('users')->where(function ($query) {
            $query = $query->whereDeleted(0);
            if (request("user_id")) {
                $query = $query->where("id","<>", request("user_id"));
            }
            return $query;
        })];
        $rules['name'] = 'required';
        $rules['sex'] = 'required';
        $rules['birthdate'] = 'required|date_format:d/m/Y|before_or_equal:'.now()->format('d/m/Y');
        $rules['place_of_birth'] = 'required';
        $rules['marital_status_id'] = 'required';
        $rules['address'] = 'required';
        $rules['phone_number'] = 'required';
        $rules['email'] = ['required', 'string', 'email', 'max:191',Rule::unique('users')->where(function ($query) {
            $query = $query->whereDeleted(0);
            if (request("user_id")) {
                $query = $query->where("id","<>", request("user_id"));
            }
            return $query;
        })];

        $rules['father_fullname'] = 'required';
        $rules['mother_fullname'] = 'required';
        return $rules;
    }
}
