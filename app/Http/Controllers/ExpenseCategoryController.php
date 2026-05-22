<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalvarCategoriaRequest;
use App\Models\ExpenseCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function listar(Request $request): JsonResponse
    {
        $categories = $request->user()
            ->expenseCategories()
            ->orderBy('name')
            ->get()
            ->map(fn (ExpenseCategory $category) => $this->formatarCategoria($category));

        return response()->json(['categories' => $categories]);
    }

    public function criar(SalvarCategoriaRequest $request): JsonResponse
    {
        $category = $request->user()->expenseCategories()->create([
            'name' => $request->validated()['name'],
        ]);

        return response()->json([
            'mensagem' => 'Categoria criada com sucesso.',
            'category' => $this->formatarCategoria($category),
        ], 201);
    }

    public function atualizar(SalvarCategoriaRequest $request, int $category): JsonResponse
    {
        $expenseCategory = $this->buscarCategoriaDoUsuario($request, $category);

        $expenseCategory->update([
            'name' => $request->validated()['name'],
        ]);

        return response()->json([
            'mensagem' => 'Categoria atualizada com sucesso.',
            'category' => $this->formatarCategoria($expenseCategory->fresh()),
        ]);
    }

    public function excluir(Request $request, int $category): JsonResponse
    {
        $expenseCategory = $this->buscarCategoriaDoUsuario($request, $category);
        $expenseCategory->delete();

        return response()->json([
            'mensagem' => 'Categoria excluída com sucesso.',
        ]);
    }

    private function buscarCategoriaDoUsuario(Request $request, int $categoryId): ExpenseCategory
    {
        return $request->user()
            ->expenseCategories()
            ->where('id', $categoryId)
            ->firstOrFail();
    }

    private function formatarCategoria(ExpenseCategory $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
        ];
    }
}
