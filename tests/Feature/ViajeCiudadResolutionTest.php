<?php

namespace Tests\Feature;

use App\Models\Ciudad;
use App\Models\User;
use App\Http\Controllers\ViajeController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ViajeCiudadResolutionTest extends TestCase
{
    use RefreshDatabase;

    private function agencia(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['is_agencia' => true])->save();

        return $user;
    }

    private function storeViaje(User $agencia, string $ciudad, string $pais, string $titulo): void
    {
        Auth::loginUsingId($agencia->id);

        $request = Request::create('/viajes', 'POST', [
            'ciudad_nombre' => $ciudad,
            'pais' => $pais,
            'titulo' => $titulo,
            'descripcion' => 'x',
            'fecha_salida' => now()->addDays(5)->toDateString(),
            'duracion_dias' => 2,
            'precio' => 50,
            'contacto' => 'x',
        ]);
        $request->setUserResolver(fn () => Auth::user());

        app(ViajeController::class)->store($request);
    }

    public function test_same_city_name_with_different_countries_creates_separate_ciudades(): void
    {
        $agencia = $this->agencia();

        $this->storeViaje($agencia, 'Córdoba', 'España', 'Viaje a Córdoba España');
        $this->storeViaje($agencia, 'Córdoba', 'Argentina', 'Viaje a Córdoba Argentina');

        $this->assertSame(2, Ciudad::where('nombre', 'Córdoba')->count());
        $this->assertDatabaseHas('ciudades', ['nombre' => 'Córdoba', 'pais' => 'España']);
        $this->assertDatabaseHas('ciudades', ['nombre' => 'Córdoba', 'pais' => 'Argentina']);
    }

    public function test_same_city_and_country_reuses_the_existing_ciudad_regardless_of_casing(): void
    {
        $agencia = $this->agencia();

        $this->storeViaje($agencia, 'MARRAKECH', 'marruecos', 'Primer viaje');
        $this->storeViaje($agencia, '  marrakech  ', 'MARRUECOS', 'Segundo viaje');

        $this->assertSame(1, Ciudad::where('nombre', 'Marrakech')->count());
    }

    public function test_ciudad_show_route_uses_the_numeric_id_not_the_name(): void
    {
        $ciudad = Ciudad::create(['nombre' => 'Ciudad de prueba', 'pais' => 'País de prueba']);

        $this->assertSame(
            url('/ciudades/'.$ciudad->id),
            route('ciudades.show', $ciudad)
        );

        $this->get(route('ciudades.show', $ciudad))->assertOk();
    }
}
