<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendOrderTelegramNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
        {
            $order = $event->order;
            $mainUrl = env("APP_URL");
            $token = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_ADMIN_CHAT_ID');

            if (!$token || !$chatId) {
                Log::error("Помилка читання токену або ід чату");
                return;
            }

            $message = "📦 *Нове замовлення №{$order->id}!*\n\n"
                 . "👤 Клієнт: {$order->first_name}\n"
                 . "📞 Телефон: {$order->phone}\n"
                 . "💰 Сума: *{$order->total_price} грн*\n"
                 . "🚚 Доставка: {$order->shipping_type}\n"
                 . "☑️ [Дивитися заказік]({$mainUrl}/admin/orders/{$order->id})";
                 Log::info($message);

            try {
                Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'Markdown',
                ]);
            } catch (\Exception $e) {
                Log::error('Помилка відправки в Telegram: ' . $e->getMessage());
            }
        }
}
