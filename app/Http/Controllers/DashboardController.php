<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // return (Auth::user()->hasVerifiedEmail()) ? view('dashboard') : view('auth.verify');
        return view('dashboard');
    }
}
