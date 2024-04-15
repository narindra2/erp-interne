<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuiviItemNote extends Model
{
    use HasFactory;
    protected $table = "suivi_items_note";
    protected $fillable = [
        "suivi_item_id",
        "note",
        "creator_id",
        "deleted",
        "created_at",
        "updated_at",
    ];
    protected $casts = [
        'created_at'  => 'date:d-m-Y',
        'note'  => 'float',
    ];
    public function creator()
    {
        return $this->belongsTo(User::class, "creator_id")->withOut(["userJob"]);
    }
    public function suiviItem()
    {
        return $this->belongsTo(User::class, "suivi_item_id");
    }
}
