<?php

namespace App\Http\Controllers;

use App\Models\Tryouts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function loginStore(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
            'password.required' => 'Password wajib diisi',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $tryout = Tryouts::first();
            return redirect()->route('tryouts-tes', ['id' => $tryout->id]);
        } else {
            return redirect()->back()->with('error', 'Email atau password salah');
        }
    }
}
