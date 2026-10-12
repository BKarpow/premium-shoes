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

            $message = "📦 <b>Нове замовлення №{$order->id}!</b>\n\n"
                     . "👤 <b>Клієнт:</b> {$order->first_name} {$order->last_name}\n"
                     . "📞 <b>Телефон:</b> {$order->phone}\n"
                     . "💰 <b>Сума:</b> {$order->total_price} грн\n"
                     . "🚚 <b>Доставка:</b> {$order->shipping_type}\n\n"
                     . "<a href=\"{$mainUrl}/admin/orders/{$order->id}\">Дивитися заказік</a>";
                 Log::info($message);


            try {
                $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]);

                // 1. Перевірка статусу HTTP (успішний діапазон 200-299)
                if ($response->successful()) {
                    // Запит виконано успішно (наприклад, статус 200 OK)
                    $data = $response->json(); // Отримати відповідь у вигляді масиву

                    // Telegram може повернути HTTP 200, але з помилкою в тілі (наприклад, невірний токен або заблокований бот)
                    if (isset($data['ok']) && $data['ok'] === true) {
                        // Повідомлення успішно надіслано

                        Log::info("Запит успішний до телеграм \n" . var_dump($data, true));
                    } else {
                        Log::error("Помилка при запиті до телеграм \n" . var_dump($data, true));
                    }
                } else {
                    // 2. Обробка помилок (статуси 4xx, 5xx)
                    $statusCode = $response->status(); // Наприклад, 400 або 401
                    $errorBody = $response->body();   // Сирий текст відповіді від сервера Telegram

                    // Логування або обробка помилки
                    Log::error("Telegram API Error: {$statusCode} - {$errorBody}");
                }
            } catch (\Exception $e) {
                Log::error('Помилка відправки в Telegram: ' . $e->getMessage());
            }
        }
}
