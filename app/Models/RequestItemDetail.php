<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use Carbon\Carbon;

class RequestItemDetail extends Model
{
    use HasFactory;
    protected $dates = ['stock_month'];
    protected $casts = [
        // 'created_at' => 'datetime:Y-m-d h:i:s A',
        'is_rejected' => 'boolean',
    ];

    protected $appends = array(
        'requested_quantity',
    );

    protected $fillable = [
        'item_id',
        'request_item_id',
        'per_piece',
        'quantity',
        'fulfilled_quantity',
        'stock_month',
        'remarks',
        'is_rejected',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function getRequestedQuantityAttribute()
    {
        return $this->quantity * $this->per_piece;
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
        ->timezone(config('app.timezone'))
        ->format('Y-m-d h:i:s A');
    }

    public function getStockMonthAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
        ->timezone(config('app.timezone'))
        ->format('F Y');
    }
}
