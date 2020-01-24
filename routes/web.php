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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::get('/dashboard', 'DashboardController@index')->name('home');
Route::get('/dashboard/{vue?}', 'DashboardController@index')->where('vue', '[\/\w\.-]*');

Route::middleware(['auth:web', 'verified'])->group(function(){
    Route::get('/groups/{group}/join/{token}', 'API\GroupController@join')->name("group.join")->middleware('signed');
    Route::get('/panels/{panel}/image/thumbnail', 'API\ImageController@showPanelThumbnail');
    Route::get('/panels/{panel}/image', 'API\ImageController@showPanelImage');
    Route::get('/panels/{panel}/pdf', 'DownloadController@downloadPdf');
    Route::get('/panels/{panel}/powerpoint', 'DownloadController@downloadPowerpoint');
    Route::get('/panels/{panel}/zip', 'DownloadController@downloadZip');
    Route::get('/panels/{panel}/dar', 'DownloadController@downloadDar');
    Route::get('/panels/{panel}', 'DownloadController@downloadOriginal');
    Route::get('/files/{file}', 'API\FileController@download');
});