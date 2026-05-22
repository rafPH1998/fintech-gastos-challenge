<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'user1@teste.local'],
            [
                'name' => 'Usuário Demo',
                'password' => Hash::make('senha123'),
            ]
        );

        $categoryNames = [
            'Alimentação',
            'Transporte',
            'Lazer',
            'Moradia',
            'Saúde',
        ];

        $createdCategories = [];

        foreach ($categoryNames as $categoryName) {
            $createdCategories[] = ExpenseCategory::updateOrCreate(
                ['user_id' => $user->id, 'name' => $categoryName],
                ['name' => $categoryName]
            );
        }

        $demoExpenses = [
            ['description' => 'Supermercado', 'amount' => 245.90, 'daysAgo' => 2, 'category' => 0],
            ['description' => 'Uber trabalho', 'amount' => 32.50, 'daysAgo' => 1, 'category' => 1],
            ['description' => 'Cinema', 'amount' => 58.00, 'daysAgo' => 5, 'category' => 2],
            ['description' => 'Conta de luz', 'amount' => 189.40, 'daysAgo' => 8, 'category' => 3],
            ['description' => 'Farmácia', 'amount' => 67.20, 'daysAgo' => 3, 'category' => 4],
            ['description' => 'Restaurante', 'amount' => 95.00, 'daysAgo' => 0, 'category' => 0],
        ];

        foreach ($demoExpenses as $demoExpense) {
            Expense::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'description' => $demoExpense['description'],
                    'date' => now()->subDays($demoExpense['daysAgo'])->format('Y-m-d'),
                ],
                [
                    'expense_category_id' => $createdCategories[$demoExpense['category']]->id,
                    'amount' => $demoExpense['amount'],
                ]
            );
        }
    }
}
