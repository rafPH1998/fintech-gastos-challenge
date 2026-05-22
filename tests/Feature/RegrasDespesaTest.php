<?php

namespace Tests\Feature;

use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RegrasDespesaTest extends TestCase
{
    use RefreshDatabase;

    private User $usuario;

    private ExpenseCategory $categoria;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuario = User::factory()->create();
        $this->categoria = ExpenseCategory::create([
            'user_id' => $this->usuario->id,
            'name' => 'Alimentação',
        ]);

        Sanctum::actingAs($this->usuario);
    }

    public function testValorDespesaDeveSerMaiorQueZero(): void
    {
        $resposta = $this->postJson('/api/despesas', [
            'description' => 'Teste valor zero',
            'amount' => 0,
            'date' => now()->format('Y-m-d'),
            'expenseCategoryId' => $this->categoria->id,
        ]);

        $resposta->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }

    public function testDataNaoPodeSerFuturaEmMaisDeUmDia(): void
    {
        $resposta = $this->postJson('/api/despesas', [
            'description' => 'Teste data futura',
            'amount' => 50,
            'date' => now()->addDays(2)->format('Y-m-d'),
            'expenseCategoryId' => $this->categoria->id,
        ]);

        $resposta->assertStatus(422)
            ->assertJsonValidationErrors(['date']);
    }

    public function testCategoriaDevePertencerAoUsuarioAutenticado(): void
    {
        $outroUsuario = User::factory()->create();
        $categoriaOutroUsuario = ExpenseCategory::create([
            'user_id' => $outroUsuario->id,
            'name' => 'Transporte',
        ]);

        $resposta = $this->postJson('/api/despesas', [
            'description' => 'Teste categoria alheia',
            'amount' => 80,
            'date' => now()->format('Y-m-d'),
            'expenseCategoryId' => $categoriaOutroUsuario->id,
        ]);

        $resposta->assertStatus(422)
            ->assertJsonValidationErrors(['expenseCategoryId']);
    }
}
