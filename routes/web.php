<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AiToolsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ProposalNoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('dashboard'));
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('proposals', ProposalController::class);
    Route::patch('proposals/{proposal}/status', [ProposalController::class, 'updateStatus'])->name('proposals.updateStatus');
    Route::post('proposals/{proposal}/loom-view', [ProposalController::class, 'recordLoomView'])->name('proposals.loomView');
    Route::post('proposals/{proposal}/notes', [ProposalNoteController::class, 'store'])->name('proposal-notes.store');
    Route::delete('proposals/{proposal}/notes/{note}', [ProposalNoteController::class, 'destroy'])->name('proposal-notes.destroy');

    Route::resource('jobs', JobController::class);
    Route::post('jobs/{job}/score', [JobController::class, 'score'])->name('jobs.score');

    Route::resource('employers', EmployerController::class);
    Route::resource('follow-ups', FollowUpController::class)->except('show');
    Route::patch('follow-ups/{follow_up}/complete', [FollowUpController::class, 'complete'])->name('follow-ups.complete');
    Route::resource('portfolio', PortfolioController::class);
    Route::get('ai-tools', [AiToolsController::class, 'index'])->name('ai-tools.index');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::patch('settings/team/{user}', [SettingsController::class, 'updateUser'])->name('settings.users.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
