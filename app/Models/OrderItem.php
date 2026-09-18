<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $guarded = [];

    protected $fillable = [
            'order_id',
            'orders_id', // Додано для сумісності з різними варіантами назв зовнішнього ключа в міграціях
            'product_variant_id',
            'product_name',
            'size_value',
            'price',
            'quantity',
        ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
