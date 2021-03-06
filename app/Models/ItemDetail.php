<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Item;
use App\Models\Warehouse;
use Carbon\Carbon;

class ItemDetail extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $dates = ['deleted_at','stock_month','created_at'];
    protected $casts = [
        // 'created_at' => 'datetime:Y-m-d h:i:s A',
        // 'stock_month' => 'datetime:F Y',
    ];
    protected $fillable = [
        'item_id',
        'quantity',
        'serial_number',
        'item_id',
        'remarks',
        'warehouse_id',
        'stock_month',
        'carton_number'
    ];

    // public static function boot()
    // {
    //     parent::boot();
    //     self::creating(function ($model) {
    //         $model->stock_month = Carbon::now()->englishMonth;
    //     });
    //     self::updating(function($model) {

    //     });
    // }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
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
