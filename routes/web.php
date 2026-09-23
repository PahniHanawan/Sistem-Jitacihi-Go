<?php

use App\Http\Controllers\AhpController;
use App\Http\Controllers\AssessmentPeriodController;
use App\Http\Controllers\CriterionController;
use App\Http\Controllers\ProductAssessmentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionDecisionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SawController;
use App\Http\Controllers\UserController;
use App\Models\AssessmentPeriod;
use App\Models\Product;
use App\Models\RankingResult;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $activePeriod = AssessmentPeriod::where('status', 'aktif')->first();
    $totalProducts = Product::where('status', 'aktif')->count();

    $topRankings = collect();
    if ($activePeriod) {
        $topRankings = RankingResult::with(['productAssessment.product', 'promotionDecision'])
            ->where('period_id', $activePeriod->period_id)
            ->orderBy('rank', 'asc')
            ->take(5)
            ->get();
    }

    return view('dashboard', compact('activePeriod', 'totalProducts', 'topRankings'));
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ✅ USER MANAGEMENT (OWNER ONLY)
    Route::middleware(['role:owner'])->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::patch('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/users/{user}/edit-data', [UserController::class, 'editData'])->name('users.edit-data');
    });

    // Master Data (Admin & Owner)
    Route::resource('criteria', CriterionController::class)->except(['create', 'show']);
    Route::resource('products', ProductController::class)->except(['create']);
    Route::resource('periods', AssessmentPeriodController::class)->except(['create', 'show']);
    Route::resource('assessments', ProductAssessmentController::class)
    ->only(['index', 'store', 'show', 'edit', 'update', 'destroy']);

    // SAW Calculations (Admin & Owner)
    Route::get('/saw', [SawController::class, 'index'])->name('saw.index');
    Route::post('/saw', [SawController::class, 'store'])->name('saw.store');

    // Reports (Admin & Owner)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');

    // Owner Only Routes
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/ahp', [AhpController::class, 'index'])->name('ahp.index');
        Route::post('/ahp', [AhpController::class, 'store'])->name('ahp.store');

        Route::get('/decisions', [PromotionDecisionController::class, 'index'])->name('decisions.index');
        Route::post('/decisions', [PromotionDecisionController::class, 'store'])->name('decisions.store');
    });
});

require __DIR__.'/auth.php';
