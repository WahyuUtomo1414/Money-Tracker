<?php

use App\Http\Controllers\TransactionPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/money-tracker');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/money-tracker/transactions/{transaction}/pdf', TransactionPdfController::class)
        ->name('transactions.pdf.show');
});
