<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

/*
|--------------------------------------------------------------------------
| User Resource
|--------------------------------------------------------------------------
|
| Access and modify users or the signed-in user
|
*/

// authenticated routes
Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::get('/users/me/panels', 'API\PanelController@listUserPanels');
    Route::get('/users/me', 'API\UserController@me');
    Route::get('/users', 'API\UserController@index');
    Route::get('/users/{user}', 'API\UserController@show');
    Route::patch('/users/{user}', 'API\UserController@update');
    Route::delete('/users/{user}', 'API\UserController@destroy');
    Route::patch('/panels/{panel}/share', 'API\PanelController@hackShare');
    Route::patch('/panels/{panel}', 'API\PanelController@update');
    Route::post('/panels/{panel}/tokens', 'API\AccessTokenController@create');
    Route::delete('/panels/{panel}/tokens', 'API\AccessTokenController@destroy');
    Route::post('/panels/{panel}/tags', 'API\TagController@store');
    Route::post('/panels/{panel}/files', 'API\FileController@store');
    Route::post('/panels/{panel}/comments', 'API\CommentController@store');
    Route::post('/panels', 'API\PanelController@store');
    Route::get('/panels/{panel}', 'API\PanelController@show');
    Route::delete('/panels', 'API\PanelController@deleteMultiple');
    Route::delete('/panels/{panel}/tags/{tag}', 'API\TagController@detachPanel');
    Route::delete('/panels/{panel}', 'API\PanelController@destroy');
    Route::patch('/comments/{comment}', 'API\CommentController@update');
    Route::delete('/comments/{comment}', 'API\CommentController@destroy');
    Route::delete('/files/{file}', 'API\FileController@destroy');
    Route::patch('/files/{file}', 'API\FileController@update');
    Route::post('/groups', 'API\GroupController@store');
    Route::get('/groups/{group}', 'API\GroupController@show');
    Route::put('/groups/{group}', 'API\GroupController@replace');
    Route::patch('/groups/{group}', 'API\GroupController@update');
    Route::delete('/groups/{group}/users', 'API\UserController@removeFromGroup');
    Route::delete('/groups/{group}', 'API\GroupController@destroy');
    Route::get('groups/{group}/panels', 'API\PanelController@listGroupPanels');
});

// Route::get('/users', 'API\UserController@index'); // TODO - superadmin only!

Route::get('panels/public', 'API\PanelController@listPublicPanels');
