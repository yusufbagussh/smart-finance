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
                'icon' => '🍽️'
            ],
            [
                'name' => 'Transportation',
                'description' => 'Gas, public transport, taxi, car maintenance',
                'color' => '#3B82F6',
                'icon' => '🚗'
            ],
            [
                'name' => 'Shopping',
                'description' => 'Clothing, electronics, and general purchases',
                'color' => '#8B5CF6',
                'icon' => '🛒'
            ],
            [
                'name' => 'Entertainment',
                'description' => 'Movies, games, subscriptions, hobbies',
                'color' => '#F59E0B',
                'icon' => '🎬'
            ],
            [
                'name' => 'Health & Medical',
                'description' => 'Doctor visits, medicine, hospital bills',
                'color' => '#10B981',
                'icon' => '🏥'
            ],
            [
                'name' => 'Education',
                'description' => 'School fees, books, courses, training',
                'color' => '#06B6D4',
                'icon' => '📚'
            ],
            [
                'name' => 'Bills & Utilities',
                'description' => 'Electricity, water, internet, phone bills',
                'color' => '#84CC16',
                'icon' => '💡'
            ],
            [
                'name' => 'Salary',
                'description' => 'Monthly salary and bonuses',
                'color' => '#22C55E',
                'icon' => '💰'
            ],
            [
                'name' => 'Business',
                'description' => 'Business income and investments',
                'color' => '#3B82F6',
                'icon' => '💼'
            ],
            [
                'name' => 'Gift & Donation',
                'description' => 'Gifts received and donations',
                'color' => '#EC4899',
                'icon' => '🎁'
            ],
            [
                'name' => 'Other Income',
                'description' => 'Miscellaneous income sources',
                'color' => '#6B7280',
                'icon' => '📈'
            ],
            [
                'name' => 'Other Expense',
                'description' => 'Miscellaneous expenses',
                'color' => '#EF4444',
                'icon' => '📉'
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
