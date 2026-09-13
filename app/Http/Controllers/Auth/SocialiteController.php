<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Перенаправлення користувача на сторінку авторизації Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Обробка відповіді від Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Шукаємо користувача за google_id або за email
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                // Якщо користувач вже є, прив'язуємо google_id (якщо ще не прив'язаний)
                if (! $user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            } else {
                // Створюємо нового користувача
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => null, // Пароль відсутній, вхід лише через Google/скидання
                ]);

                // Якщо використовується пакет spatie/laravel-permission, призначаємо роль за замовчуванням
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('customer');
                }
            }

            // Авторизуємо користувача в системі
            Auth::login($user, remember: true);

            return redirect()->intended(route('dashboard'));

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Помилка авторизації через Google.');
        }
    }
}
