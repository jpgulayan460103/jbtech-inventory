<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Item;
use App\Models\ItemDetail;
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
        'stock',
        'remain',
        'warehouse_id',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function item_detail()
    {
        return $this->belongsTo(ItemDetail::class);
    }
}
