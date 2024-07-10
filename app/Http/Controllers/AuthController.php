<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
 
class AuthController extends Controller
{
    // Show Login Page
    public function index(): View
    {
        return view("auth.login");
    }

    // Show Register Page
    public function toRegister()
    {
        return view("auth.register");
    }

    // Register New Account
    public function registerNewAccount(Request $request)
    {
        // Validate the request...
        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'password' => 'required',
        ]);

        // Validate username (MUST UNIQUE) and password (compare to comfirm password)
        if (User::where('username', $request->username)->first()) {
            return redirect('/register')->with('error', 'Username already taken');
        }

        if ($request->password != $request->password_confirmation) {
            return redirect('/register')->with('error', 'Password not match');
        }
        
        // Create new account
        $user = new User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->password = password_hash($request->password, PASSWORD_DEFAULT);
        $user->save();
        
        // Redirect to login page
        return redirect('/login');   
    }

    // Login
    public function login(Request $request)
    {
        // Validate the request...
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        
        // Find user by username
        $user = User::where('username', $request->username)->first();
        
        // Check if user not found
        if (!$user) {
            return redirect('/login')->with('error', 'User not found');
        }
        
        // Check if password not match
        if (!password_verify($request->password, $user->password)) {
            return redirect('/login')->with('error', 'Password not match');
        }
        
        // Set session user_id
        session(['user_id' => $user->id]);
        
        // Redirect to home page
        return redirect('/');
    }

    // Logout
    public function logout()
    {
        // Remove session user_id
        session()->forget('user_id');
        
        // Redirect to login page
        return redirect('/login');
    }

}