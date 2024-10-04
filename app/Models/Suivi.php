<?php

namespace App\Models;

use App\Models\SuiviPoint;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Suivi est un dossier
class Suivi extends Model
{
    use HasFactory;

    protected $table = "suivis";
    // protected $with = ["types"];
    protected $with = ["points","points.project_type"];
    protected $fillable = [
        "folder_name",
        "category",
        "folder_location",
        "ref",
        "level_id",
        "creator_id",
        "deleted",
    ];
    public static $POINT_ADDITIONAL_DESSI = 0.00;
    public static $POINT_ADDITIONAL_URBA = 0.70;
    protected $appends = ['totalPointBase' , "addedAt"]; // V1
    // Calcul total point pour un dossier  avec  ses type choisi
    public function getTotalPointBaseAttribute()
    {
        $total_point = 0;
        if (!isset( $this->points)) {
            return  $total_point;

        }
        $points = $this->points;
        //Pour un dossier d'un seul type son  point est juste le point sans le point suplementaire
        if ($this->points->count() == 1) {
            $total_point = $points[0]->point; 
        }else{
            //Pour un dossier plusieur  type son  point 
            $i = 0;
            // $points  = $points->sortByDesc('niveau'); // old concept
            $points  = $points->sortByDesc('point');
            foreach ($points as $detail) {
                // Le premeir point par niveau elevé est la base ,
               if ($i == 0) {
                    $total_point = $detail->point; 
               }else{
                    // Et les autres sont  ses points suplementaires en sommant par celui ci.
                   $total_point =  $total_point + ($detail->point_sup); 
               }
               $i++;
            }
        }
        return  round($total_point + self::$POINT_ADDITIONAL_DESSI , 3) ;
    }
    public function getAddedAtAttribute()
    {
        return  convert_to_real_time_humains($this->created_at) ;
    }
    public function items(){
        return $this->hasMany(SuiviItem::class,"suivi_id");
    }
    public function members(){
        return $this->hasMany(SuiviItem::class,"suivi_id")->distinct()->get(["user_id","follower"]);
    }

    // Old concept code note used or don't remove
    public function types()
    {
        return $this->belongsToMany(SuiviType::class , "suivi_and_types","suivi_id","type_id");
    }
    public function points()
    {
        return $this->belongsToMany(SuiviPoint::class , "suivi_and_points","suivi_id","point_id");
    }
    // End old concept
    
    public static function search_folder( $term)
    {
        $data = [];
        $folders = self::where(function ($query) use ($term) {
            if (!in_array(strtolower($term), ["tous", "all" ,"toutes","tout","listes","list", "***"])) {
                $query->where("folder_name", 'like', '%' . $term . '%');
                $query->orWhere("category", 'like', '%' . $term . '%');
                $query->orWhere("ref", 'like', '%' . $term . '%');
            }
        })->whereDeleted(0)->latest("updated_at")->orderBy("folder_name","asc")->get();
        foreach ($folders as $folder) {
            $data[] = ["id" => $folder->id,"text" => $folder->folder_name . " (réf : $folder->ref )" ];
        }
        return ["results" => $data];
    }
}
