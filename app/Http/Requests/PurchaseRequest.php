<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'method' => 'required',
            'purchase_date' => 'required'
        ];
    }

    public function withValidator($validator)
    {
        $this->checkItemRowInvalid();
        if ($validator->fails()) {
            die(json_encode(["success" => false, "validation" => true,  "message" => $validator->errors()->all()]));
        }
    }
    private function checkItemRowInvalid()
    {
        $unitPrices = request('unit_price');
        $quantity = request('quantity');
        $itemTypeID = request('item_type_id');
        $unitItemID = request('unit_item_id');
        /** The first in array a fake data no use it */ 
        array_shift($unitPrices );
        array_shift( $quantity ) ;
        array_shift($itemTypeID);
        array_shift( $unitItemID );
        $i = 1;
        $rowInValideValue = [null , 0 , false , "0" , "null" , "NULL" , "Null"];
        array_map(function ($unitPrices, $quantity , $itemTypeID ,  $unitItemID) use (&$i ,  $rowInValideValue) {
            if (in_array($unitPrices  , $rowInValideValue )) {
                die(json_encode(["success" => false, "validation" => true,  "message" =>"Vous avez oublié le prix à la line article  n° $i ."]));
            }
            if (in_array($quantity  , $rowInValideValue )) {
                die(json_encode(["success" => false, "validation" => true,  "message" =>"Vous avez envoyé zéro quantité à la line article  n° $i ."]));
            }
            
            if (in_array($itemTypeID  , $rowInValideValue )) {
                die(json_encode(["success" => false, "validation" => true,  "message" =>"Vous n'avez pas selectionné l'article a acheté  à la line article  n° $i ."]));
            }
            if (in_array($unitItemID  , $rowInValideValue )) {
                die(json_encode(["success" => false, "validation" => true,  "message" =>"Vous n'avez pas selectionné l'unité à la line article  n° $i ."]));
            }
            $i++;
        }, $unitPrices, $quantity , $itemTypeID ,  $unitItemID);
    }

}
