<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Item;
use App\Models\ItemDetail;
use App\Models\RequestItem;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;

class ItemHistory extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'history_type',
        'item_id',
        'item_detail_id',
        'user_id',
        'quantity',
        'stock',
        'remain',
        'warehouse_id',
        'request_item_id',
        'deleted_remarks',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function item_detail()
    {
        return $this->belongsTo(ItemDetail::class)->withTrashed();
    }
    
    public function request_item()
    {
        return $this->belongsTo(RequestItem::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
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
}
