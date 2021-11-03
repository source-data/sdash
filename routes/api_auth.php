<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Authentication Routes
|--------------------------------------------------------------------------
|
| These are the routes required by Laravel Sanctum. They use the API
| namespace but are protected by the Web middleware as they require
| full CSRF token protection.
|
| This is defined in the Providers/RouteServiceProvider file.
|
*/

Route::post('/login', 'API\Authentication\LoginController@login')->name('login');
Route::post('/logout', 'API\Authentication\LoginController@logout');
Route::post('/users', 'API\Authentication\RegistrationController@register');
Route::post('/users/password/reset', 'API\Authentication\ForgottenPasswordController@sendResetLinkEmail');
Route::post('emails', 'API\Authentication\EmailVerificationController@resend')->name('verification.resend');
Route::post('/users/password', 'API\Authentication\ResetPasswordController@reset');
