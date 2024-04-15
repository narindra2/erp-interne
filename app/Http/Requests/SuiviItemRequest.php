<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class SuiviItemRequest extends FormRequest
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
        $auth = Auth::user();
        $rules = [
            // "user_id" =>  request("clon_of")  ? "required" : "nullable",
            "user_id" => ($auth->isM2pOrAdmin() || $auth->isCp())  ? "required" : "nullable",
            "folder_name" => "required",
            // "ref" => "required|integer|unique:suivis,ref",
            "ref" => ["required","integer" ,Rule::unique('suivis')->where(function ($query)  {
                return $query->whereDeleted(0);
            })],
            "poles" => "required",
            "follower" => "required",
            "version_id" => "required",
            "montage" => "required",
            "types" => "required",
            "folder_location" => "nullable",
        ];
        if (request("clon_of") || request("item_id")) {
            unset($rules["folder_name"]);
            $rules["ref"] = "required";
            // $rules["level_id"] = "required|min:1";
        }
        return $rules;
    }
    public function messages()
    {
        return [
            'user_id.required' => 'Veuillez selectinner la personne à "assigné" </br> ',
            'folder_name.required' => 'Veuillez inserer le nom du dossier </br> ',
            'ref.required' => 'Veuillez inserer le reference </br>',
            'ref.unique' => 'Cette reference dossier exite déjà, Clonner simplement !</br>',
            'ref.integer' => 'Le reference dossier doit de type numerique!</br>',
            'type_client.required' => 'Veuillez selectionner le type client !</br>',
            'types.required' => 'Veuillez selectionner le type de projet</br>',
            'follower.required' => 'Veuillez selectionner un MDP </br>',
            'version_id.required' => 'Veuillez selectionner un version </br>  ',
            'montage.required' => 'Veuillez selectionner le montage </br> ',
            'poles.required' => 'Veuillez selectionner le pôle </br> ',
            'level_id.required' => 'Veuillez selectionner la difficulté </br>',
            'level_id.min' => 'Veuillez insérerla difficulté </br>',
        ];
    }
    public function withValidator($validator)
    {
        $requests= collect(request()->all())->keys()->all();
        $invalid_colones = collect($validator->invalid())->keys()->all() ?? [];
      
        $validated_colones = array_values(array_diff( $requests,$invalid_colones))  ??  [];
        $errors = $validator->errors()->all();
        if (!request("types")) {
            $invalid_colones[]= "types";
            $errors[] = "Veuillez selectionner le type de projet.";
        }
        if (request("types")) {
            $request_type =  is_array(request("types")) ? request("types") : [request("types")];
            $request_type =  array_filter($request_type);
            if (!count($request_type)) {
                $invalid_colones[]= "types";
                $errors[] = "Veuillez selectionner le type de projet.";
            }
        }
        // else{
        //     foreach (request("types") as $id) {
        //         if (!(int) $id) {
        //             $invalid_colones[]= "types";
        //             $errors[] = "Veuillez selectionner le type de projet.";
        //         }
        //     }
        // }
        if ($validator->fails() || count($invalid_colones) ) {
            die(json_encode(["success" => false, "validation" => true,"invalid_colones" => $invalid_colones ,"validated_colones" =>$validated_colones , "message" => $validator->errors()->all() + $errors]));
        }
        
    }
}
