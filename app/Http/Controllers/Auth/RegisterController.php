<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use App\Models\UserConsent;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => ['required', 'string', 'max:120'],
            'surname' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'institution_name' => ['nullable', 'max:120'],
            'institution_address' => ['nullable', 'max:255'],
            'department_name' => ['nullable', 'max:255'],
            'linkedin' => ['nullable', 'max:255'],
            'orcid' => ['nullable', 'max:30', 'regex:/0000-000(1-[5-9]|2-[0-9]|3-[0-4])\d{3}-\d{3}[\dX]/i'],
            'twitter' => ['nullable', 'max:255'],
            'confirmation.0' => ['accepted'],
            'confirmation.1' => ['accepted'],
            'confirmation.2' => ['accepted'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'firstname' => $data['firstname'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'institution_name' => $data['institution_name'],
            'institution_address' => $data['institution_address'],
            'department_name' => $data['department_name'],
            'linkedin' => $data['linkedin'],
            'orcid' => $data['orcid'],
            'twitter' => $data['twitter'],
            'password' => Hash::make($data['password']),
            'has_consented' => true,
        ]);

        UserConsent::create([
            'user_id' => $user->id,
        ]);

        return $user;
    }
}
