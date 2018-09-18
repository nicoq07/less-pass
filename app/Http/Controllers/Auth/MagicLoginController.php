<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Auth\MagicAuthentication;
use App\UserLoginToken;
use Auth;

class MagicLoginController extends Controller
{
    //
    protected $redirectOnRequested = '/login/magic';

    public function show()
    {
        return view('auth.magic.login');
    }

    public function sendToken(Request $request, MagicAuthentication $auth)
    {
        $this->validateLogin($request);

        $auth->requestedLink();

        return redirect()->to($this->redirectOnRequested)->with('success', 'Revisá tu correo');
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email'
        ]);
    }

    protected function validateToken(Request $request, UserLoginToken $token)
    {
        $token->delete();

        if ($token->isExpired()) {
            return redirect()->to($this->redirectOnRequested)->with('error', 'Tu token expiró. Generá otro.');
        }

        if (!$token->belongsToEmail($request->email)) {
            return redirect()->to($this->redirectOnRequested)->with('error', 'Ese token no es válido');
        }


        Auth::login($token->user, $request->remember);

        return redirect()->intended();
    }

}
