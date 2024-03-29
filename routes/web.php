<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth::routes(['verify' => true]);

// Necessary routes for the registration and email verification process
Route::get('email/verify/{id}/{hash}', 'API\Authentication\EmailVerificationController@verify')->name('verification.verify');

Route::get('/panels/{panel}/image', 'API\ImageController@showPanelImage');

// download routes - special access gates for these routes are defined in the controller
Route::get('/panels/{panel}/pdf', 'DownloadController@downloadPdf');
Route::get('/panels/{panel}/powerpoint', 'DownloadController@downloadPowerpoint');
Route::get('/panels/{panel}/zip', 'DownloadController@downloadZip');
Route::get('/panels/{panel}/dar', 'DownloadController@downloadDar');
Route::get('/panels/{panel}/original', 'DownloadController@downloadOriginal');
Route::get('/files/{file}', 'API\FileController@download');

// Allow user to join groups or admin to authorise new members
Route::get('/groups/{group}/join/{token}', 'API\GroupController@join')->name("group.join")->middleware('signed');
Route::get('/groups/{group}/accept/{token}', 'API\GroupController@acceptUser')->name("group.accept")->middleware('signed');

Route::middleware(['auth:web', 'verified'])->group(function () {
    Route::get('/panels/{panel}/image/thumbnail', 'API\ImageController@showPanelThumbnail');
    Route::get('/panels/{panel}/token/qr', 'API\AccessTokenController@qrCode');
});

/**
 * Mount Vue JS app on base route
 */
Route::get('/', 'DashboardController@index')->name('home');
Route::get('/{vue?}', 'DashboardController@index')->where('vue', '[\/\w\.-]*');
