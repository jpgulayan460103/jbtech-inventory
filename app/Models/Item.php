<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\ItemDetail;
use Carbon\Carbon;

class Item extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $dates = ['deleted_at'];
    protected $appends = array(
        'item_type_string',
        'total_quantity_1',
        'total_quantity_2',
        'total_quantity',
    );
    protected $fillable = [
        'name',
        'category',
        'reorder_level',
        'is_active',
        'item_type',
        'allow_delete',
        'is_archived',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->allow_delete = 1;
            $model->is_active = 1;
            $model->is_archived = 0;
        });
        self::updating(function($model) {

        });
    }
    

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
    public function getTotalQuantityAttribute()
    {
        return $this->details()->sum('quantity');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
        ->timezone(config('app.timezone'))
        ->format('Y-m-d h:i:s A');
    }

}
