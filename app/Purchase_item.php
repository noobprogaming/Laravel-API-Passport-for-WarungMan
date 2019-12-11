<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase_item extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "purchase_item";

    public function purchase() {
        return $this->belongsTo(Purchase::class);
    }

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'amount', 'purchasing_price', 'selling_price', 'purchase_id', 'item_id', 'seller_id', 'buyer_id',   
    ];

}
