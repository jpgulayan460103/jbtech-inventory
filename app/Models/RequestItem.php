<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\RequestItemDetail;
use Carbon\Carbon;
use App\Models\User;

class RequestItem extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'customer_name',
        'requester_id',
        'processor_id',
        'warehouse_id',
        'status',
        'remarks',
    ];
    protected $appends = array(
        'request_number',
    );

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s A',
    ];
    public function items()
    {
        return $this->hasMany(RequestItemDetail::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
    public function processor()
    {
        return $this->belongsTo(User::class, 'processor_id');
    }

    public function getRequestNumberAttribute()
    {
        return Carbon::parse($this->created_at)->format("Y-").str_pad($this->id,4,"0",STR_PAD_LEFT);
    }
}
