<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\CustomValidationException;
use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterFormRequest;
use App\Http\Requests\Request;
use App\Models\User;
use Auth;
use App\Http\Controllers\Controller;
use Cache;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest', [
            'except' => ['getLogout', 'getUser'],
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param RegisterFormRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(RegisterFormRequest $request)
    {
        $user = $this->create($request->all());
        $this->userLogin($user);

        return $this->authenticated($request, $user);
    }

    /**
     * Handle a login request to the application.
     *
     * @param LoginFormRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(LoginFormRequest $request)
    {
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            //return 'sendLockoutResponse';
            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->getCredentials($request);

        if ($this->loginAttempt($credentials, $request)) {
            return $this->handleUserWasAuthenticated($request, $throttles);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if ($throttles) {
            $this->incrementLoginAttempts($request);
        }

        throw new CustomValidationException([
            $this->loginUsername() => $this->getFailedLoginMessage(),
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        Auth::logout();

        return 'Logged out';
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return Auth::user();
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = (int) Cache::get($this->getLoginLockExpirationKey($request)) - time();

        throw new CustomValidationException([
            $this->loginUsername() => $this->getLockoutErrorMessage($seconds),
        ]);
    }

    /**
     * @param array $credentials
     * @param LoginFormRequest $request
     * @return bool
     */
    protected function loginAttempt($credentials, LoginFormRequest $request)
    {
        return Auth::attempt($credentials, $request->has('remember'));
    }

    /**
     * @param User $user
     */
    protected function userLogin(User $user)
    {
        Auth::login($user);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param Request $request
     * @param User $user
     * @return User
     */
    protected function authenticated(Request $request, User $user)
    {
        return $user;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}