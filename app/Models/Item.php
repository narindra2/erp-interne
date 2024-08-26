<?php

namespace App\Models;

use Exception;
use Carbon\Carbon;
use App\Models\ItemCategory;
use FontLib\Table\Type\loca;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
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
        'code'  => 'integer',
    ];
    protected $appends = [
        'codeDetail',
        'qrCode',
        'placeInfo',
    ];
    const SEPARATOR_CODE = "/";

    public function getCodeDetailAttribute() {
        return $this->get_code_detail_item();
    }
    public function getQrCodeAttribute() {
        return $this->get_qrcode_detail_item(true);
    }
    public function getPlaceInfoAttribute() {
        return $this->get_actualy_place_info();
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
    public function mouvements() {
        return $this->belongsToMany(Location::class,"item_movements", "item_id","location_id")->withPivot(["place","item_id","user_id","deleted","created_at"]);
    }
    public function get_actualy_place_info() {
        return DB::table("item_movements")->where("item_id", $this->id)->orderBy("id","DESC")->first();
    }
    public function get_actualy_place() {
        $actuel_place = $this->placeInfo;
        if ($actuel_place) {
            $location = Location::find($actuel_place->location_id);
            return  $actuel_place->place . "" . ($location->code_location ?? $location->name);
        }else{
            $location = Location::find(Location::STOCK_ID);
           return  $location->code_location ??  $location->name;
        }
    }
    public function get_user_use_it() {
       $actuel_place = $this->placeInfo;
       if ($actuel_place && $actuel_place->user_id) {
        return User::findMany(explode(",",$actuel_place->user_id))->implode("sortname", ", ");
       }
    }
    public function get_disponible() {
        $actuel_place = $this->placeInfo;
        if (in_array($this->etat , ["perdu","detruit"])) {
            return  "Hors d'usage";
        }
        
        if (!$actuel_place) {
            return  "Libre";
        }elseif ($actuel_place->location_id == Location::STOCK_ID && !$actuel_place->place) {
            return  "Libre";
        }elseif ($actuel_place->location_id != Location::STOCK_ID && !$actuel_place->user_id) {
            return  "Libre";
        }elseif ($actuel_place->location_id != Location::STOCK_ID && $actuel_place->user_id) {
            return  "En usage";
        }else{
            return  "Libre";
        }
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
        }else if ($this->etat == "en_stock") {
            return ["text" => "En stock" , "color" => "info"];
        }else{
            return ["text" => $this->etat , "color" => "dark"];
        }
    }
    /** Identité + order chronologique +date d'aquisation + nature */
    public function get_code_detail_item() {
        $separator = self::SEPARATOR_CODE;
        if (!$this->article) {
            $this->load("article.category");
        }
        $code_article =  $this->article->code ? $this->article->code : "-";
        $code_category = ($this->article && isset($this->article->category) ) ? $this->article->category->code : "-";
        $code_item = $this->code ?  sprintf("%04d", $this->code) : "-"; // make alwayse  4 digit the code ex : code = 9 => 0009
        $date_item = Carbon::parse($this->date)->format("dmY");
        return $code_article.$separator.$code_item.$separator.$date_item.$separator.$code_category;
    }
    public function get_qrcode_detail_item($typeRedirectionUrl = false) {
       
        if ($this->article->sub_category != ItemType::IMMOBILISATION) {
            return null;
        }
        if ($typeRedirectionUrl) {
            return Cache::rememberForever("item_$this->id", function () {
                return QrCode::size(130)->color(82, 27, 195)->generate(url("/item/$this->id"));
            });
        }
        /** Type info details */
        $data = collect();
        if (!$this->article) {
            $this->load("article.category");
        }
        
        $data[] = $this->codeDetail;
        $data[] = $this->article->name;
        $data[] = Carbon::parse($this->date)->format("d/m/Y");
        if (isset($this->article->category)) {
            $data[] = $this->article->category->name;
        }
        $data[] = $this->propriety;
        return QrCode::size(130)->color(82, 27, 195)->generate(($data)->implode(", "));
    }
    public static function generateCodeItemForNew($item_type_id) {
       try {
            $last_item =  DB::table('items')->where("item_type_id", "=",$item_type_id)->orderBy('code', 'desc')->whereDeleted(0)->first();
            /** First record  */
            if (!$last_item) {
                return 1;
            }
            /** For n-ème record  */
            return  (int) $last_item->code + 1;
       } catch (Exception $e) {
            die("Impossible de generer le code article , Erreur : "  . $e->getMessage());
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
        // $filters[] = [
        //     "label" => "Sous-cat", 
        //     "name" =>"sub_cat",
        //     "type" => "select",
        //     "width"  =>"w-200px",
        //     'attributes' => [
        //         "data-hide-search" => "true",
        //         "data-allow-clear" => "true",
        //     ],
        //     "options" =>  [
        //         ["value" => null , "text" => "Sous-catégorie"],
        //         ["value" => "immobilisation" , "text" => "Immobilisation"],
        //         ["value" => "consommable" , "text" => "Consommable"],
        //     ],
        // ];
        $filters[] = [
            "label" => "Etat", 
            "name" =>"etat_item",
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
