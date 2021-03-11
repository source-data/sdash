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
Auth::routes(['verify' => true]);

// Route::get('/', 'WelcomeController@index');
// Route::get('/public', 'DashboardController@showPublicDashboard')->name('public.dashboard');
Route::get('/', 'DashboardController@showPublicDashboard')->name('public.dashboard');
Route::get('/about', 'WelcomeController@about');
Route::get('/docs', 'WelcomeController@docs');
Route::get('/dashboard', 'DashboardController@index')->name('home')->middleware('auth');
Route::get('/dashboard/{vue?}', 'DashboardController@index')->where('vue', '[\/\w\.-]*')->middleware('auth');
Route::get('/panels/{panel}/image', 'API\ImageController@showPanelImage');

// Special single panel route
Route::get('/panel/{panel}', 'PanelController@show');

// download routes - special access gates for these routes are defined in the controller
Route::get('/panels/{panel}/pdf', 'DownloadController@downloadPdf');
Route::get('/panels/{panel}/powerpoint', 'DownloadController@downloadPowerpoint');
Route::get('/panels/{panel}/zip', 'DownloadController@downloadZip');
Route::get('/panels/{panel}/dar', 'DownloadController@downloadDar');
Route::get('/panels/{panel}', 'DownloadController@downloadOriginal');
Route::get('/files/{file}', 'API\FileController@download');

Route::middleware(['auth:web', 'verified'])->group(function () {
    Route::get('/groups/{group}/join/{token}', 'API\GroupController@join')->name("group.join")->middleware('signed');
    Route::get('/panels/{panel}/image/thumbnail', 'API\ImageController@showPanelThumbnail');
    Route::get('/panels/{panel}/token/qr', 'API\AccessTokenController@qrCode');    
});
