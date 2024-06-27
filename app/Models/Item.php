<?php

namespace App\Models;

use Exception;
use Carbon\Carbon;
use App\Models\ItemCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
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
        'created_from',
        'deleted',
        'code'
    ];
    protected $casts = [
        'date'  => 'date:d/m/Y',
    ];
    protected $appends = [
        'codeDetail',
        'qrCode',
    ];
    const SEPARATOR_CODE = "/";

    public function getCodeDetailAttribute() {
        return $this->get_code_detail_item();
    }
    public function getQrCodeAttribute() {
        return QrCode::size(130) ->color(82, 27, 195)->generate($this->codeDetail);;
    }
    /** Identité + order chronologique +date d'aquisation + nature */
    public function get_code_detail_item() {
        $separator = self::SEPARATOR_CODE;
        if (!$this->article) {
            $this->load("article.category");
        }
        $code_article = $this->article ? $this->article->code : "non-defiie";
        $code_category = $this->article ? $this->article->category->code : "non-defiie";
        $code_item = $this->code ?? "code-item";
        $date_item = Carbon::parse($this->date)->format("dmY");
        return $code_article.$separator.$code_item.$separator.$date_item.$separator.$code_category;
    }
    public static function generateCodeItemForNew($item_type_id) {
       try {
            $last_item =  DB::table('items')->where("item_type_id", "=",$item_type_id)->orderBy('code', 'desc')->first();
            /** First record  */
            if (!$last_item) {
                return 1;
            }
            /** For n-ème record  */
            return  (int) $last_item->code + 1;
       } catch (Exception $e) {
            die("Impossible de generer le code article error : "  . $e->getMessage());
       }
    }
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
                ["value" => "fonctionnel" , "text" => "Fonctionnel"],
                ["value" => "en_panne" , "text" => "En panne"],
                ["value" => "detruit" , "text" => "Détruit"],
                ["value" => "perdu" , "text" => "Perdu"],
            ],
        ];
        
        return $filters;
    }
}
