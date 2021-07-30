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
    Route::post('/feedback', 'API\FeedbackController@send');
    Route::get('/users/me/panels', 'API\PanelController@listUserPanels');
    Route::get('/users/me', 'API\UserController@me');
    Route::get('/users', 'API\UserController@index');
    Route::patch('/users/{user}/password', 'API\UserController@changePassword');
    Route::get('/users/{user}', 'API\UserController@show');
    Route::patch('/users/{user}', 'API\UserController@update');
    Route::patch('/users/{user}/consent', 'API\UserController@updateConsent');
    Route::delete('/users/{user}', 'API\UserController@destroy');
    Route::post('/users/me/groups/{group}/applications', 'API\GroupController@apply');
    Route::patch('/users/me/groups/{group}/join/{token}', 'API\GroupController@joinViaApi');
    Route::delete('/users/me/groups/{group}/join/{token}', 'API\GroupController@declineGroupInvitation');
    Route::patch('/panels/{panel}', 'API\PanelController@update');
    Route::patch('/panels/{panel}/image', 'API\PanelController@changeImage');
    Route::patch('/panels/{panel}/status', 'API\PanelController@updateStatus');
    Route::post('/panels/{panel}/tokens', 'API\AccessTokenController@create');
    Route::delete('/panels/{panel}/tokens', 'API\AccessTokenController@destroy');
    Route::delete('/panels/{panel}/users/me', 'API\PanelAuthorController@remove');
    Route::post('/panels/{panel}/tags', 'API\TagController@store');
    Route::post('/panels/{panel}/files', 'API\FileController@store');
    Route::delete('/panels/{panel}/comments/{comment}', 'API\CommentController@destroy');
    Route::post('/panels/{panel}/comments', 'API\CommentController@store');
    Route::post('/panels', 'API\PanelController@store');
    Route::get('/panels/{panel}', 'API\PanelController@show');
    Route::delete('/panels', 'API\PanelController@deleteMultiple');
    Route::delete('/panels/{panel}/tags/{tag}', 'API\TagController@detachPanel');
    Route::patch('/panels/{panel}/tags', 'API\TagController@addToPanelTags');
    Route::delete('/panels/{panel}', 'API\PanelController@destroy');
    Route::patch('/comments/{comment}', 'API\CommentController@update');
    Route::delete('/comments/{comment}', 'API\CommentController@destroy');
    Route::delete('/files/{file}', 'API\FileController@destroy');
    Route::patch('/files/{file}', 'API\FileController@update');
    Route::post('/groups', 'API\GroupController@store');
    Route::get('/groups', 'API\GroupController@listPublicGroups');
    Route::get('/groups/{group}', 'API\GroupController@show');
    Route::put('/groups/{group}', 'API\GroupController@replace');
    Route::patch('/groups/{group}/panels', 'API\GroupController@managePanels');
    Route::delete('/groups/{group}/users', 'API\UserController@removeFromGroup');
    Route::delete('/groups/{group}', 'API\GroupController@destroy');
    Route::get('/groups/{group}/panels', 'API\PanelController@listGroupPanels');
    Route::put('/panels/{panel}/authors', 'API\PanelAuthorController@update');
    Route::get('/tags', 'API\TagController@index');
});

// Route::get('/users', 'API\UserController@index'); // TODO - superadmin only!

Route::get('panels/public', 'API\PanelController@listPublicPanels');
Route::get('files/categories', 'API\FileController@listFileCategories');
