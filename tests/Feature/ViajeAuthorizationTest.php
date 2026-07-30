<?php

namespace Tests\Feature;

use App\Models\Ciudad;
use App\Models\User;
use App\Models\Viaje;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViajeAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function viajeData(array $overrides = []): array
    {
        return array_merge([
            'ciudad_id' => Ciudad::create(['nombre' => 'Ciudad de prueba '.uniqid()])->id,
            'titulo' => 'Viaje de prueba',
            'descripcion' => 'Descripción de prueba',
            'fecha_salida' => now()->addDays(10)->toDateString(),
            'duracion_dias' => 5,
            'precio' => 199.99,
            'contacto' => 'test@agencia.com',
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login_from_create_form(): void
    {
        $this->get(route('viajes.create'))->assertRedirect(route('login'));
    }

    public function test_normal_user_cannot_view_create_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('viajes.create'))
            ->assertForbidden();
    }

    public function test_normal_user_cannot_store_a_viaje(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('viajes.store'), $this->viajeData())
            ->assertForbidden();

        $this->assertDatabaseCount('viajes', 0);
    }

    public function test_agencia_can_view_create_form(): void
    {
        $agencia = User::factory()->create();
        $agencia->forceFill(['is_agencia' => true])->save();

        $this->actingAs($agencia)
            ->get(route('viajes.create'))
            ->assertOk();
    }

    public function test_agencia_can_store_a_viaje(): void
    {
        $agencia = User::factory()->create();
        $agencia->forceFill(['is_agencia' => true])->save();

        $this->actingAs($agencia)
            ->post(route('viajes.store'), $this->viajeData())
            ->assertRedirect();

        $this->assertDatabaseHas('viajes', [
            'titulo' => 'Viaje de prueba',
            'user_id' => $agencia->id,
        ]);
    }

    public function test_owner_can_update_their_own_viaje(): void
    {
        $agencia = User::factory()->create();
        $agencia->forceFill(['is_agencia' => true])->save();
        $viaje = Viaje::create($this->viajeData(['user_id' => $agencia->id, 'activo' => true]));

        $this->actingAs($agencia)
            ->put(route('viajes.update', $viaje), $this->viajeData(['titulo' => 'Actualizado']))
            ->assertRedirect(route('viajes.show', $viaje));

        $this->assertSame('Actualizado', $viaje->fresh()->titulo);
    }

    public function test_admin_can_update_a_viaje_they_do_not_own(): void
    {
        $agencia = User::factory()->create();
        $agencia->forceFill(['is_agencia' => true])->save();
        $viaje = Viaje::create($this->viajeData(['user_id' => $agencia->id, 'activo' => true]));

        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($admin)
            ->put(route('viajes.update', $viaje), $this->viajeData(['titulo' => 'Editado por admin']))
            ->assertRedirect(route('viajes.show', $viaje));

        $this->assertSame('Editado por admin', $viaje->fresh()->titulo);
    }

    public function test_another_agencia_cannot_update_someone_elses_viaje(): void
    {
        $owner = User::factory()->create();
        $owner->forceFill(['is_agencia' => true])->save();
        $viaje = Viaje::create($this->viajeData(['user_id' => $owner->id, 'activo' => true]));

        $otherAgencia = User::factory()->create();
        $otherAgencia->forceFill(['is_agencia' => true])->save();

        $this->actingAs($otherAgencia)
            ->put(route('viajes.update', $viaje), $this->viajeData(['titulo' => 'Intento ajeno']))
            ->assertForbidden();

        $this->assertNotSame('Intento ajeno', $viaje->fresh()->titulo);
    }

    public function test_another_agencia_cannot_delete_someone_elses_viaje(): void
    {
        $owner = User::factory()->create();
        $owner->forceFill(['is_agencia' => true])->save();
        $viaje = Viaje::create($this->viajeData(['user_id' => $owner->id, 'activo' => true]));

        $otherAgencia = User::factory()->create();
        $otherAgencia->forceFill(['is_agencia' => true])->save();

        $this->actingAs($otherAgencia)
            ->delete(route('viajes.destroy', $viaje))
            ->assertForbidden();

        $this->assertDatabaseHas('viajes', ['id' => $viaje->id]);
    }
}
