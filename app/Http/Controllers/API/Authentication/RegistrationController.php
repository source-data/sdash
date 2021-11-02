<?php

namespace App\Http\Controllers\API\Authentication;

use API;
use App\User;
use App\Models\UserConsent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegistrationController extends Controller
{

    use RegistersUsers;

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
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        // $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return API::response(201, "Registration succeeded.", []);
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
