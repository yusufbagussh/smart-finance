<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MachineLearningMonitoringController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\InvestmentTransactionController;
use App\Http\Controllers\LiabilityController;
use App\Http\Controllers\MachineLearningController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Cek apakah pengguna sudah login
    if (Auth::check()) {
        // Jika sudah login, cek perannya
        if (auth()->user()->isAdmin()) {
            // Jika admin, arahkan ke admin.dashboard
            return redirect()->route('admin.dashboard');
        } else {
            // Jika user biasa, arahkan ke dashboard biasa
            return redirect()->route('dashboard');
        }
    }

    // Jika belum login (guest), arahkan ke halaman login
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/home', function () {
    //return to view welcome
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Transaction Management
    Route::resource('transactions', TransactionController::class);

    // Budget Management
    Route::resource('budgets', BudgetController::class);

    // ML Features (Mock for now)
    Route::get('/ml-features', [MachineLearningController::class, 'index'])->name('ml.index');
    Route::post('/ml-features/classify', [MachineLearningController::class, 'classifyTransaction'])->name('ml.classify');
    Route::get('/ml-features/predictions', [MachineLearningController::class, 'predictions'])->name('ml.predictions');
    Route::get('/ml-features/recommendations', [MachineLearningController::class, 'recommendations'])->name('ml.recommendations');

    Route::resource('portfolios', PortfolioController::class);

    // Account Management
    Route::resource('accounts', AccountController::class);

    Route::resource('liabilities', LiabilityController::class);

    // Rute untuk Transaksi (dari langkah sebelumnya)
    Route::resource('investment-transactions', InvestmentTransactionController::class)->except(['index', 'show']);
});

Route::middleware(['auth', 'admin'])->group(function () {

    // Rute CRUD untuk Aset (untuk update harga manual)
    // Kita mungkin hanya butuh index, edit, update
    Route::resource('assets', AssetController::class);
});

// Admin Routes (hanya untuk admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // Category Management
    Route::resource('categories', CategoryController::class);

    // Machine Learning Monitoring
    Route::get('/monitoring', [MachineLearningMonitoringController::class, 'index'])->name('monitoring.index');
});

Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {
    // Main report page
    Route::get('/', [ReportController::class, 'index'])->name('index');

    // Excel Exports
    Route::post('/export/transactions/excel', [ReportController::class, 'exportTransactionsExcel'])->name('transactions.excel');
    Route::post('/export/financial-report/excel', [ReportController::class, 'exportFinancialReportExcel'])->name('financial-report.excel');

    // PDF Exports
    Route::post('/export/transactions/pdf', [ReportController::class, 'exportTransactionsPdfEnhanced'])->name('transactions.pdf');
    Route::post('/export/financial-report/pdf', [ReportController::class, 'exportFinancialReportPdfEnhanced'])->name('financial-report.pdf');

    // Preview PDF (opens in browser)
    Route::post('/preview/transactions', [ReportController::class, 'previewTransactionsPdf'])->name('transactions.preview');
    Route::post('/preview/financial-report', [ReportController::class, 'previewFinancialReportPdf'])->name('financial-report.preview');
});
require __DIR__ . '/auth.php';
