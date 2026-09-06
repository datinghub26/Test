<?php

use App\Http\Controllers\Postback\PostbackController;

Route::group(['prefix' => 'ZJX44M5PLK'], function () {
    Route::get('/adgem', [PostbackController::class, 'adgem'])->name('postback.adgem');
    Route::any('/ayetstudios', [PostbackController::class, 'ayetstudios']);
    Route::any('/lootably', [PostbackController::class, 'lootably']);
    Route::any('/monlix', [PostbackController::class, 'monlix']);
    Route::any('/adgatemedia', [PostbackController::class, 'adgatemedia']);
    Route::any('/admantum', [PostbackController::class, 'admantum']);
    Route::any('/wannads', [PostbackController::class, 'wannads']);
    Route::any('/offertoro', [PostbackController::class, 'offertoro']);
    Route::any('/timewall', [PostbackController::class, 'timewall']);
    Route::any('/pollfish', [PostbackController::class, 'pollfish']);
    Route::any('/ogads', [PostbackController::class, 'ogads']);
    Route::any('/bitlabs', [PostbackController::class, 'bitlabs']);
    Route::any('/adbreakmedia', [PostbackController::class, 'adbreakmedia']);
    Route::any('/cpxresearch', [PostbackController::class, 'cpxresearch']);
    Route::any('/admantium', [PostbackController::class, 'admantium']);
    Route::any('/adscendmedia', [PostbackController::class, 'adscendmedia']);
    Route::any('/mylead', [PostbackController::class, 'mylead']);
    Route::any('/revlum', [PostbackController::class, 'revlum']);
    Route::any('/revu', [PostbackController::class, 'revu']);
    Route::any('/mychips', [PostbackController::class, 'mychips']);
    Route::any('/theoremreach', [PostbackController::class, 'theoremreach']);
    Route::any('/upwall', [PostbackController::class, 'upwall']);
    Route::any('/taskwall', [PostbackController::class, 'taskwall']);
    Route::any('/tplayad', [PostbackController::class, 'tplayad']);
    Route::any('/adtogame', [PostbackController::class, 'adtogame']);
    Route::any('/adparagon', [PostbackController::class, 'adparagon']);
    Route::any('/revtoo', [PostbackController::class, 'revtoo']);
    Route::any('/inbrain', [PostbackController::class, 'inbrain']);
    Route::any('/notik', [PostbackController::class, 'notik']);
    Route::any('/paidbusky', [PostbackController::class, 'paidbusky']);
    Route::any('/radientwall', [PostbackController::class, 'radientwall']);
    Route::any('/primewall', [PostbackController::class, 'primewall']);
    Route::any('/offery', [PostbackController::class, 'offery']);
    Route::any('/sushiads', [PostbackController::class, 'sushiads']);
    Route::any('/adtowall', [PostbackController::class, 'adtowall']);
    Route::any('/pubscale', [PostbackController::class, 'pubscale']);
    Route::any('/adspritmedia', [PostbackController::class, 'adspritmedia']);
    Route::any('/adswed', [PostbackController::class, 'adswed']);
    Route::any('/mmwall', [PostbackController::class, 'mmwall']);
    Route::any('/opinion', [PostbackController::class, 'opinion']);
    Route::any('/opinionsurvey', [PostbackController::class, 'opinionsurvey']);
    Route::any('/clickwall', [PostbackController::class, 'clickwall']);
    Route::any('/cpagrip', [PostbackController::class, 'cpagrip']);
});

// Direct postback routes (without secret prefix) for networks that use clean paths
Route::any('/clickwall', [PostbackController::class, 'clickwall']);
Route::any('/cpagrip', [PostbackController::class, 'cpagrip']);


