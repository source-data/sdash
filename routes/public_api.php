<?php

/**
 * ==================================
 * PUBLIC API ROUTES
 * ------------------
 * Routes defined in this file have the base URL
 * of /public-api
 *
 * e.g. sdash.sourcedata.io/public-api/panels/1
 * would hit Route::get('/panels/1', ...)
 */

use Illuminate\Http\Request;

/**
 * These routes are publicly accessible
 *
 */
Route::get('/panels', 'API\PanelController@listPublicPanels');
Route::get('/panels/{panel}', 'API\PanelController@showPublic');
Route::get('/panels/{panel}/image/thumbnail', 'API\ImageController@showPanelThumbnail');
Route::get('/files/categories', 'API\FileController@listFileCategories');
Route::get('/tags', 'API\TagController@publicIndex');
Route::get('/users', 'API\UserController@publicIndex');
Route::get('/groups/{group}/panels', 'API\PanelController@listPublicGroupPanels');

/**
 * These routes use a middleware to check that the Panel ID passed in the route refers to
 * a public panel. If you use this middleware, there is no need to check whether the
 * panel is public in the controller.
 */
Route::middleware(['public_panels_only'])->group(function () {
  Route::get('/panels/{panel}/image', 'API\ImageController@showPublicPanelImage');
});
