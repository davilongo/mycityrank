<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.usuarios.index'))->assertRedirect(route('login'));
    }

    public function test_normal_user_cannot_view_the_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.usuarios.index'))
            ->assertForbidden();
    }

    public function test_agencia_that_is_not_admin_cannot_view_the_panel(): void
    {
        $agencia = User::factory()->create();
        $agencia->forceFill(['is_agencia' => true])->save();

        $this->actingAs($agencia)
            ->get(route('admin.usuarios.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_the_panel(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($admin)
            ->get(route('admin.usuarios.index'))
            ->assertOk();
    }

    public function test_normal_user_cannot_toggle_agencia_status(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.usuarios.toggle-agencia', $target))
            ->assertForbidden();

        $this->assertFalse($target->fresh()->isAgencia());
    }

    public function test_admin_can_declare_an_existing_user_as_agencia(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $target = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.usuarios.toggle-agencia', $target))
            ->assertRedirect();

        $this->assertTrue($target->fresh()->isAgencia());
    }

    public function test_normal_user_cannot_create_a_new_agencia(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.usuarios.store-agencia'), [
                'name' => 'Nueva Agencia',
                'email' => 'nueva-agencia@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'nueva-agencia@example.com']);
    }

    public function test_admin_can_create_a_new_agencia_from_scratch(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($admin)
            ->post(route('admin.usuarios.store-agencia'), [
                'name' => 'Nueva Agencia',
                'email' => 'nueva-agencia@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertRedirect(route('admin.usuarios.index'));

        $nuevo = User::where('email', 'nueva-agencia@example.com')->first();
        $this->assertNotNull($nuevo);
        $this->assertTrue($nuevo->isAgencia());
        $this->assertFalse($nuevo->isAdmin());
    }
}
