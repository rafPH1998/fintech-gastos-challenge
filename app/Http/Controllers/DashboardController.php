<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $totalMonthAmount = (float) $user->expenses()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $latestExpenses = $user->expenses()
            ->with('expenseCategory')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(fn ($expense) => [
                'id' => $expense->id,
                'description' => $expense->description,
                'amount' => (float) $expense->amount,
                'date' => $expense->date->format('Y-m-d'),
                'categoryName' => $expense->expenseCategory?->name,
            ]);

        $amountByCategory = $user->expenses()
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->whereBetween('expenses.date', [$startOfMonth, $endOfMonth])
            ->select('expense_categories.name', DB::raw('SUM(expenses.amount) as total'))
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($item) => [
                'categoryName' => $item->name,
                'total' => (float) $item->total,
            ]);

        return response()->json([
            'totalMonthAmount' => $totalMonthAmount,
            'latestExpenses' => $latestExpenses,
            'amountByCategory' => $amountByCategory,
        ]);
    }
}
