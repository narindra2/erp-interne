<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VersionSuivi extends Model
{
    use HasFactory;
    protected $table = "suivi_versions";
    protected $fillable = ["title" ,"belongs" , "version_id_base","percentage","point",  "creator_id","deleted"];
    public $timestamps = false;

    public function creator()
    {
        return $this->belongsTo(User::class , "creator_id");
    }
    public function base_calcul()
    {
        return $this->belongsTo(VersionSuivi::class , "version_id_base");
    }
    public function items()
    {
        return $this->hasMany(SuiviItem::class , "version_id")->whereDeleted(0);
    }
    public function scopeGetDetail($query , $options = [])
    {       
        $versions = self::whereDeleted(0);
        $versions->with(["items" => function($suivi_item) use ($options){
            /** Basic filters */
            $user_id  = get_array_value($options, "user_id");
            if ($user_id) {
                $suivi_item->where("user_id",$user_id);
            }
            $suivi_id  = get_array_value($options, "suivi_id");
            if ($suivi_id) {
                $suivi_item->where("suivi_id",$suivi_id);
            }
            if (get_array_value($options, "interval")) {
                $interval = get_array_value($options, "interval") ;
            }else{
                $interval =  get_array_value($options, "interval_stats");
            }
            if ($interval) {
                $dates = explode("-", $interval);
                if (count($dates) > 1) {
                    $suivi_item->whereBetween("created_at", [to_date($dates[0]), to_date($dates[1])]);
                }
            }
             /** Custom filters */
            $custom_filter_id = $interval = get_array_value($options, "custom_filter_id");
            if ($custom_filter_id) {
                $filter = CustomerFilter::find($custom_filter_id);
                $custom_filter = unserialize($filter->filters);
                if ($custom_filter) {
                    $users = get_array_value( $custom_filter , "users");
                    if ($users) {
                        $suivi_item->whereIn("user_id",$users);
                    }
                    $suivis_custom = get_array_value( $custom_filter , "suivis");
                    if ($suivis_custom) {
                        $suivi_item->whereIn("suivi_id",$suivis_custom);
                    }
                    $versions_custom = get_array_value( $custom_filter , "versions");
                    if ($versions_custom) {
                        $suivi_item->whereIn("version_id",$versions_custom);
                    }
                    $montages_custom = get_array_value( $custom_filter , "montages");
                    if ($montages_custom) {
                        $suivi_item->whereIn("montage",$montages_custom);
                    }
                    $status_custom = get_array_value( $custom_filter , "status");
                    if ($status_custom) {
                        $suivi_item->whereIn("status_id",$status_custom);
                    }
                    $poles_custom = get_array_value( $custom_filter , "poles");
                    if($poles_custom){
                        $suivi_item->whereIn("poles",$poles_custom );
                    }
                }
            }
            $suivi_item->whereDeleted(0);
        }]);
        return  $versions;
    }
}