<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\ItemDetail;

class Item extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $dates = ['deleted_at'];
    protected $appends = array(
        'item_type_string',
        'total_quantity_1',
        'total_quantity_2',
    );
    protected $fillable = [
        'name',
        'category',
        'reorder_level',
        'is_active',
        'item_type',
    ];

    public function getItemTypeStringAttribute()
    {
        return Str::headline($this->item_type);
    }

    public function details()
    {
        return $this->hasMany(ItemDetail::class, 'item_id');
    }

    public function getTotalQuantity1Attribute()
    {
        return $this->details()->where('warehouse_id', 1)->sum('quantity');
    }
    public function getTotalQuantity2Attribute()
    {
        return $this->details()->where('warehouse_id', 2)->sum('quantity');
    }


}
