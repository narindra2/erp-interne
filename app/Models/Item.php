<?php

namespace App\Models;

use App\Models\ItemCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_type_id',
        'purchase_id',
        'num_invoice_id',
        'price_htt',
        'price_ht',
        'propriety',
        'observation',
        'date',
        'etat',
        'deleted',
        'code'
    ];
    protected $casts = [
        'date'  => 'date:d/m/Y',
    ];
    const SEPARATOR_CODE = "/";

    public function article() {
        return $this->belongsTo(ItemType::class, "item_type_id");
    }
    public function purchase() {
        return $this->belongsTo(Purchase::class, "purchase_id");
    }
    public function num_invoice() {
        return $this->belongsTo(PurchaseNumInvoiceLine::class, "num_invoice_id");
    }
    public function getEtatInfo() {
        if($this->etat == "fonctionnel"){
             return ["text" => 'Fonctionnnel' , "color" => "success"];
        }elseif ($this->etat == "en_panne") {
            return ["text" => 'En panne' , "color" => "warning"];
        }elseif ($this->etat == "perdu") {
            return ["text" => 'Perdu' , "color" => "danger"];
        
        }elseif ($this->etat == "detruit") {
            return ["text" => 'Détruit' , "color" => "dark"];
        }else{
            return ["text" => 'Non definie' , "color" => "dark"];
        }
    }
    public static function createFilter($options = [])
    {
        $filters = [];
        $filters[] = [
            "label" => "Article", 
            "name" =>"article_id",
            "type" => "select",
            "width"  =>"w-200px",
            'attributes' => [
                "data-hide-search" => "false",
                "data-allow-clear" => "true",
            ],
            "options" => to_dropdown(ItemType::whereDeleted(0)->get(),"id","name"),
        ];
        $filters[] = [
            "label" => "Catégorie", 
            "name" =>"cat_id",
            "type" => "select",
            "width"  =>"w-200px",
            'attributes' => [
                "data-hide-search" => "true",
                "data-allow-clear" => "true",
            ],
            "options" => to_dropdown(ItemCategory::whereDeleted(0)->get(),"id","name"),
        ];
        $filters[] = [
            "label" => "Sous-cat", 
            "name" =>"sub_cat",
            "type" => "select",
            "width"  =>"w-200px",
            'attributes' => [
                "data-hide-search" => "true",
                "data-allow-clear" => "true",
            ],
            "options" =>  [
                ["value" => null , "text" => "Sous-catégorie"],
                ["value" => "immobilisation" , "text" => "Immobilisation"],
                ["value" => "consomable" , "text" => "Consomable"],
            ],
        ];
        $filters[] = [
            "label" => "Etat", 
            "name" =>"etat",
            "type" => "select",
            "width"  =>"w-200px",
            'attributes' => [
                "data-hide-search" => "true",
                "data-allow-clear" => "true",
            ],
            "options" =>  [
                ["value" => null , "text" => "Etat"],
                ["value" => "fonctionnel" , "text" => "Fonctionnel"],
                ["value" => "en_panne" , "text" => "En panne"],
                ["value" => "perdu" , "text" => "Perdu"],
                ["value" => "detruit" , "text" => "Détruit"],
            ],
        ];
        
        return $filters;
    }
}
