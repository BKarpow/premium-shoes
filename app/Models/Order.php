<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $guarded = [];

    protected $fillable = [
            'user_id',
            'first_name',
            'last_name',
            'phone',
            'email',
            'shipping_type',
            'city_ref',
            'city_name',
            'warehouse_ref',
            'warehouse_address',
            'total_price',
            'status',
            'payment_status',
        ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
