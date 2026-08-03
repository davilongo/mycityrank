<?php

namespace Tests\Feature;

use App\Mail\AgenciaBienvenida;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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
            ])
            ->assertRedirect(route('admin.usuarios.index'));

        $nuevo = User::where('email', 'nueva-agencia@example.com')->first();
        $this->assertNotNull($nuevo);
        $this->assertTrue($nuevo->isAgencia());
        $this->assertFalse($nuevo->isAdmin());
    }

    public function test_creating_a_new_agencia_sends_the_welcome_email_with_a_working_password_link(): void
    {
        Mail::fake();

        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($admin)->post(route('admin.usuarios.store-agencia'), [
            'name' => 'Nueva Agencia',
            'email' => 'nueva-agencia@example.com',
        ]);

        Mail::assertQueued(AgenciaBienvenida::class, fn ($mail) => $mail->hasTo('nueva-agencia@example.com'));

        $queued = Mail::queued(AgenciaBienvenida::class)->first();
        $token = basename(parse_url($queued->resetUrl, PHP_URL_PATH));

        $this->post(route('logout'));

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'nueva-agencia@example.com',
            'password' => 'nueva-contrasena',
            'password_confirmation' => 'nueva-contrasena',
        ]);

        $response->assertRedirect(route('login'));

        $nuevo = User::where('email', 'nueva-agencia@example.com')->first();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('nueva-contrasena', $nuevo->password));
    }

    public function test_normal_user_cannot_delete_a_user(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($user)
            ->delete(route('admin.usuarios.destroy', $target))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    public function test_admin_can_delete_a_user(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $target = User::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.usuarios.destroy', $target))
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($admin)
            ->delete(route('admin.usuarios.destroy', $admin))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
