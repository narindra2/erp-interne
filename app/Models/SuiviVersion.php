<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuiviVersion extends Model
{
    use HasFactory;
    protected $table = "suivi_versions";
    public $timestamps = false;
    protected $guarded = [];
    public static function getVersions($options = [])
    {
        $departement = get_array_value($options, "departement");
        if (Auth::user()->isAdmin()) {
            return self::whereDeleted(0);
        }
        return self::where('belongs', "all")->orWhere(function ($q2) use ($departement) {
            if ($departement) {
                $q2->whereRaw('FIND_IN_SET(?,belongs)', [$departement]);
            }
        })->whereDeleted(0);
    }
    public function levelsPoint()
    {
        return $this->hasMany(SuiviVersionLevelPoint::class , "version_id");
    }
    public function creator()
    {
        return $this->belongsTo(User::class , "creator_id");
    }
    public function base_calcul()
    {
        return $this->belongsTo(SuiviVersion::class , "version_id_base");
    }
    public function items()
    {
        return $this->hasMany(SuiviItem::class , "version_id")->whereDeleted(0);
    }
    public function montage_1_point()
    {
        // return $this->hasOne(SuiviVersionPointMontage::class , "version_id")->whereDeleted(0)->where("montage", 1);
        return $this->hasMany(SuiviVersionPointMontage::class , "version_id")->whereDeleted(0)->where("montage", 1);
    }
    public function montage_2_point()
    {
        // return $this->hasOne(SuiviVersionPointMontage::class , "version_id")->whereDeleted(0)->where("montage", 2);
        return $this->hasMany(SuiviVersionPointMontage::class , "version_id")->whereDeleted(0)->where("montage", 2);
    }
    public function montage_3_point()
    {
        // return $this->hasOne(SuiviVersionPointMontage::class , "version_id")->whereDeleted(0)->where("montage", 3);
        return $this->hasMany(SuiviVersionPointMontage::class , "version_id")->whereDeleted(0)->where("montage", 3);
    }

    public static function drop($departement = ["departement" => "m2p"])
    {
        $versions = SuiviVersion::getVersions($departement)->get();
        $version_options = [];
        $i = 0;
        foreach ($versions as $version) {
            $i++;
            $version_options[] = ["value" => $version->id, "text" => $version->title, "name" => $version->title, "id" => $version->id ,"label" => $version->title,];
        }
        return $version_options;
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
