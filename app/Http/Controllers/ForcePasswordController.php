<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForcePasswordController extends Controller
{
    public function edit()
    {
        return view('auth.force_password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();
        $user->update([
            'password'               => Hash::make($request->password),
            'force_password_change'  => false,
        ]);

        return redirect()->route('dashboard')->with('message', 'Password updated. Welcome!');
    }
}
