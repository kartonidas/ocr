<?php

use App\Http\Controllers\OcrDocumentController;
use Illuminate\Support\Facades\Route;

Auth::routes([
    'register' => false,
    'reset' => false,
]);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [OcrDocumentController::class, 'index'])->name('documents.index');
    Route::get('/document/{document}', [OcrDocumentController::class, 'show'])->name('document.show');
    Route::get('/document/{document}/pdf/{inline?}', [OcrDocumentController::class, 'pdf'])->name('document.pdf');
});

