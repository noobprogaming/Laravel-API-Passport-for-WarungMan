<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "category";

    public function item() {
        return $this->hasMany(Item::class);
    }

    protected $fillable = [
        'category_id', 'explanation',
    ];
}
