<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
         * Відображення сторінки профілю
         */
        public function edit()
        {
            $user = Auth::user()->load('profile');

            return Inertia::render('Profile/Edit', [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'profile' => $user->profile ? [
                    'first_name' => $user->profile->first_name,
                    'last_name' => $user->profile->last_name,
                    'middle_name' => $user->profile->middle_name,
                    'phone' => $user->profile->phone,
                    'np_city_ref' => $user->profile->np_city_ref,
                    'np_city_name' => $user->profile->np_city_name,
                    'np_warehouse_ref' => $user->profile->np_warehouse_ref,
                    'np_warehouse_name' => $user->profile->np_warehouse_name,
                    'birth_date' => $user->profile->birth_date,
                    'gender' => $user->profile->gender,
                ] : null,
            ]);
        }

        public function update(Request $request)
        {
            $user = Auth::user();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'np_city_ref' => 'nullable|string|max:255',
                'np_city_name' => 'nullable|string|max:255',
                'np_warehouse_ref' => 'nullable|string|max:255',
                'np_warehouse_name' => 'nullable|string|max:255',
                'birth_date' => 'nullable|date',
                'gender' => 'nullable|string|in:male,female',
            ]);

            // 1. Оновлюємо ім'я у базовій таблиці users
            $user->update([
                'name' => $validated['name'],
            ]);

            // 2. Створюємо або оновлюємо пов'язаний профіль user_profiles
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'middle_name' => $validated['middle_name'],
                    'phone' => $validated['phone'],
                    'np_city_ref' => $validated['np_city_ref'],
                    'np_city_name' => $validated['np_city_name'],
                    'np_warehouse_ref' => $validated['np_warehouse_ref'],
                    'np_warehouse_name' => $validated['np_warehouse_name'],
                    'birth_date' => $validated['birth_date'],
                    'gender' => $validated['gender'],
                ]
            );

            return back()->with('success', 'Ваші дані успішно оновлено!');
        }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
