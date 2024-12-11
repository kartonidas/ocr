<?php

use App\Http\Controllers\MatchingRuleController;
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

    Route::get('/matching-rules', [MatchingRuleController::class, 'index'])->name('matching-rules.index');
    Route::get('/matching-rule', [MatchingRuleController::class, 'create'])->name('matching-rules.create');
    Route::post('/matching-rule', [MatchingRuleController::class, 'store']);
    Route::get('/matching-rule/{matchingRule}', [MatchingRuleController::class, 'edit'])->name('matching-rules.update');
    Route::post('/matching-rule/{matchingRule}', [MatchingRuleController::class, 'update']);
    Route::post('/matching-rule/{matchingRule}/delete', [MatchingRuleController::class, 'destroy'])->name('matching-rules.destroy');
});

