<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class RequestItemDetail extends Model
{
    use HasFactory;

    protected $appends = array(
        'requested_quantity',
    );

    protected $fillable = [
        'item_id',
        'request_item_id',
        'per_piece',
        'quantity',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function getRequestedQuantityAttribute()
    {
        return $this->quantity * $this->per_piece;
    }
}
