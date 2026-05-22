<?php

namespace Tests\Feature;

use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RegrasCategoriaTest extends TestCase
{
    use RefreshDatabase;

    public function testNomeCategoriaDeveSerUnicoPorUsuario(): void
    {
        $usuario = User::factory()->create();
        Sanctum::actingAs($usuario);

        ExpenseCategory::create([
            'user_id' => $usuario->id,
            'name' => 'Lazer',
        ]);

        $resposta = $this->postJson('/api/categorias', [
            'name' => 'Lazer',
        ]);

        $resposta->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}
