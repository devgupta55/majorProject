<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            if (auth()->user()->user_type == 1) {
                return redirect('/admin/dashboard')
                    ->withSuccess('You have Successfully logged in');
            } else {
                return redirect('/seller/machines')
                    ->withSuccess('You have Successfully logged in');
            }
        } else {
            return redirect()
                ->back()
                ->withErrors(['logInErrors' => 'You have entered wrong credentials!']);
        }
    }

    public function dashboard()
    {
        $users = User::all();
        return view('admin.dashboard', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function addUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'user_type' => 'required',
        ]);
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' =>  Hash::make($validated['password']),
            'user_type' => (int)$validated['user_type'],
        ]);
        return redirect('/admin/dashboard')
            ->withSuccess('User Created Successfully');
    }

    public function deleteUser(Request $request, $id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect('/admin/dashboard')
            ->withSuccess('User deleted Successfully');
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/admin');
    }
}
