<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'phone',
        'np_city_ref',
        'np_city_name',
        'np_warehouse_ref',
        'np_warehouse_name',
        'np_warehouse_address',
        'birth_date',
        'gender',
        'telegram_chat_id',
        'discount_card',
        'bonus_balance',
        'notes',
    ];

    /**
     * Зв'язок з користувачем
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
