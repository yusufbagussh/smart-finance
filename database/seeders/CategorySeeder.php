<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Food & Dining',
                'description' => 'Restaurant, groceries, and food delivery',
                'color' => '#EF4444',
                'icon' => '🍽️',
                'type' => 'expense'
            ],
            [
                'name' => 'Transportation',
                'description' => 'Gas, public transport, taxi, car maintenance',
                'color' => '#3B82F6',
                'icon' => '🚗',
                'type' => 'expense'
            ],
            [
                'name' => 'Shopping',
                'description' => 'Clothing, electronics, and general purchases',
                'color' => '#8B5CF6',
                'icon' => '🛒',
                'type' => 'expense'
            ],
            [
                'name' => 'Entertainment',
                'description' => 'Movies, games, subscriptions, hobbies',
                'color' => '#F59E0B',
                'icon' => '🎬',
                'type' => 'expense'
            ],
            [
                'name' => 'Health & Medical',
                'description' => 'Doctor visits, medicine, hospital bills',
                'color' => '#10B981',
                'icon' => '🏥',
                'type' => 'expense'
            ],
            [
                'name' => 'Education',
                'description' => 'School fees, books, courses, training',
                'color' => '#06B6D4',
                'icon' => '📚',
                'type' => 'expense'
            ],
            [
                'name' => 'Bills & Utilities',
                'description' => 'Electricity, water, internet, phone bills',
                'color' => '#84CC16',
                'icon' => '💡',
                'type' => 'expense'
            ],
            [
                'name' => 'Salary',
                'description' => 'Monthly salary and bonuses',
                'color' => '#22C55E',
                'icon' => '💰',
                'type' => 'income'
            ],
            [
                'name' => 'Business',
                'description' => 'Business income and investments',
                'color' => '#3B82F6',
                'icon' => '💼',
                'type' => 'income'
            ],
            [
                'name' => 'Gift & Donation',
                'description' => 'Gifts received and donations',
                'color' => '#EC4899',
                'icon' => '🎁',
                'type' => 'expense'
            ],
            [
                'name' => 'Other Income',
                'description' => 'Miscellaneous income sources',
                'color' => '#6B7280',
                'icon' => '📈',
                'type' => 'income'
            ],
            [
                'name' => 'Other Expense',
                'description' => 'Miscellaneous expenses',
                'color' => '#EF4444',
                'icon' => '📉',
                'type' => 'expense'
            ],
            // [
            //     'name' => 'Investment',
            //     'description' => 'Investment purchases and sales',
            //     'color' => '#8B5CF6',
            //     'icon' => '💹',
            //     'type' => 'expense'
            // ],
            // [
            //     'name' => 'Investment Income',
            //     'description' => 'Returns from investments',
            //     'color' => '#10B981',
            //     'icon' => '📊',
            //     'type' => 'income'
            // ],
            [
                'name' => 'Debt Repayment',
                'description' => 'Repayment of loans and debts',
                'color' => '#F59E0B',
                'icon' => '📉',
                'type' => 'expense'
            ],
            [
                'name' => 'Debt Collection',
                'description' => 'Collection of loans and debts',
                'color' => '#22C55E',
                'icon' => '🤲',
                'type' => 'income'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
