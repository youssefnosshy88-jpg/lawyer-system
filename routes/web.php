<?php

use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/admin'));

Route::get('/locale/{locale}', LocaleController::class)
    ->where('locale', 'ar|en')
    ->name('locale.switch');

Route::middleware(['auth'])->group(function (): void {
    Route::get('/print/invoices/{invoice}', [PrintController::class, 'invoice'])->name('invoices.print');
    Route::get('/print/payments/{payment}', [PrintController::class, 'receipt'])->name('payments.receipt');
});
