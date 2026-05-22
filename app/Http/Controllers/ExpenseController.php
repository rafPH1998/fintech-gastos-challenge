<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalvarDespesaRequest;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function listar(Request $request): JsonResponse
    {
        $expenses = $request->user()
            ->expenses()
            ->with('expenseCategory')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Expense $expense) => $this->formatarDespesa($expense));

        return response()->json(['expenses' => $expenses]);
    }

    public function criar(SalvarDespesaRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $expense = $request->user()->expenses()->create([
            'expense_category_id' => $validated['expenseCategoryId'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
        ]);

        $expense->load('expenseCategory');

        return response()->json([
            'mensagem' => 'Despesa criada com sucesso.',
            'expense' => $this->formatarDespesa($expense),
        ], 201);
    }

    public function atualizar(SalvarDespesaRequest $request, int $expense): JsonResponse
    {
        $expenseModel = $this->buscarDespesaDoUsuario($request, $expense);
        $validated = $request->validated();

        $expenseModel->update([
            'expense_category_id' => $validated['expenseCategoryId'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
        ]);

        $expenseModel->load('expenseCategory');

        return response()->json([
            'mensagem' => 'Despesa atualizada com sucesso.',
            'expense' => $this->formatarDespesa($expenseModel->fresh()),
        ]);
    }

    public function excluir(Request $request, int $expense): JsonResponse
    {
        $expenseModel = $this->buscarDespesaDoUsuario($request, $expense);
        $expenseModel->delete();

        return response()->json([
            'mensagem' => 'Despesa excluída com sucesso.',
        ]);
    }

    private function buscarDespesaDoUsuario(Request $request, int $expenseId): Expense
    {
        return $request->user()
            ->expenses()
            ->where('id', $expenseId)
            ->firstOrFail();
    }

    private function formatarDespesa(Expense $expense): array
    {
        return [
            'id' => $expense->id,
            'description' => $expense->description,
            'amount' => (float) $expense->amount,
            'date' => $expense->date->format('Y-m-d'),
            'expenseCategoryId' => $expense->expense_category_id,
            'categoryName' => $expense->expenseCategory?->name,
        ];
    }
}
