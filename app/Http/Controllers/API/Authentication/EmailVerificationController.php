<?php

namespace App\Http\Controllers\API\Authentication;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\User;

class EmailVerificationController extends Controller
{
  /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

  use VerifiesEmails;

  /**
   * Where to redirect users after verification.
   *
   * @var string
   */
  protected $redirectTo = '/?firstLogin=1';

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth')->except('verify', 'resend');
    $this->middleware('signed')->only('verify');
    $this->middleware('throttle:6,1')->only('verify', 'resend');
  }

  /**
   * Resend the email verification notification.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
   */
  public function resend(Request $request)
  {
    $request->validate(['email' => [
      'required',
      'email',
      'exists:users',
    ]]);

    $user = User::where('email', $request->input('email'))->first();

    if ($user->hasVerifiedEmail()) {
      return $request->wantsJson()
        ? new JsonResponse([], 204)
        : redirect($this->redirectPath());
    }

    $user->sendEmailVerificationNotification();

    return $request->wantsJson()
      ? new JsonResponse([], 202)
      : back()->with('resent', true);
  }

  /**
   * Mark the authenticated user's email address as verified.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
   *
   * @throws \Illuminate\Auth\Access\AuthorizationException
   */
  public function verify(Request $request)
  {
    $user = User::find($request->route('id'));

    if (!hash_equals((string) $request->route('id'), (string) $user->getKey())) {
      throw new AuthorizationException;
    }

    if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
      throw new AuthorizationException;
    }

    if ($user->hasVerifiedEmail()) {
      return $request->wantsJson()
        ? new JsonResponse([], 204)
        : redirect($this->redirectPath());
    }

    if ($user->markEmailAsVerified()) {
      event(new Verified($user));
      Auth::login($user);
    }

    if ($response = $this->verified($request)) {
      return $response;
    }

    return $request->wantsJson()
      ? new JsonResponse([], 204)
      : redirect($this->redirectPath())->with('verified', true);
  }
}
