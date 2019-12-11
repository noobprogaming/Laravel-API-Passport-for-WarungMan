<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "rating";

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'rating', 'review', 'item_id', 'id', 'time', 
    ];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'time' => 'datetime',
    ];
}
