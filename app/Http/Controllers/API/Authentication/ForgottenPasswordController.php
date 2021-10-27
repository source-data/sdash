<?php

namespace App\Http\Controllers\API\Authentication;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForgottenPasswordController extends Controller
{

    use SendsPasswordResetEmails;
}
