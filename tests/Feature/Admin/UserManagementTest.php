<?php
namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Створюємо базові ролі для тестів
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'customer']);
    }

    #[Test]
    public function guests_are_redirected_from_admin_dashboard()
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    #[Test]
    public function regular_users_cannot_access_admin_panel()
    {
        $user = User::factory()->withRole('customer')->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    #[Test]
    public function managers_can_access_dashboard_but_not_user_management()
    {
        $manager = User::factory()->withRole('manager')->create();

        // Доступ до дашборду є
        $this->actingAs($manager)->get('/admin')->assertStatus(200);

        // Доступ до керування користувачами заборонено (403)
        $this->actingAs($manager)->get('/admin/users')->assertStatus(403);
    }

    #[Test]
    public function admin_can_view_user_list()
    {
        $admin = User::factory()->withRole('admin')->create();

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_create_new_user_with_role()
    {
        $admin = User::factory()->withRole('admin')->create();

        $userData = [
            'name' => 'Тестовий Менеджер',
            'email' => 'manager@example.com',
            'password' => 'password123',
            'role' => 'manager',
        ];

        $response = $this->actingAs($admin)->post('/admin/users', $userData);

        $response->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', [
            'email' => 'manager@example.com',
            'name' => 'Тестовий Менеджер',
        ]);

        $newUser = User::where('email', 'manager@example.com')->first();
        $this->assertTrue($newUser->hasRole('manager'));
    }

    #[Test]
    public function admin_can_update_user_role()
    {
        $admin = User::factory()->withRole('admin')->create();
        $user = User::factory()->withRole('customer')->create();

        $response = $this->actingAs($admin)->put("/admin/users/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'manager',
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertTrue($user->fresh()->hasRole('manager'));
    }

    #[Test]
    public function admin_cannot_delete_their_own_account()
    {
        $admin = User::factory()->withRole('admin')->create();

        $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
