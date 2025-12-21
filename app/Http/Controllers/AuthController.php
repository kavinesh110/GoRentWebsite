<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;
use App\Models\Staff;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function showRegisterSuccess()
    {
        if (!session('auth_role')) {
            return redirect()->route('login');
        }
        return view('auth.register-success');
    }

    public function register(Request $request)
    {
        $role = $request->input('role', 'customer');

        if ($role === 'staff') {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:staff,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $staff = Staff::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password_hash' => Hash::make($data['password']),
                'role' => 'staff',
            ]);

            session([
                'auth_role' => 'staff',
                'auth_id' => $staff->staff_id,
                'auth_name' => $staff->name,
            ]);

            return redirect()->route('staff.dashboard');
        } else {
            $data = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:customers,email',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:30',
                'utm_role' => 'nullable|in:student,staff',
            ]);

            $customer = Customer::create([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password_hash' => Hash::make($data['password']),
                'ic_url' => null,
                'utmid_url' => null,
                'license_url' => null,
                'utm_role' => $data['utm_role'] ?? null,
                'verification_status' => 'pending',
            ]);

            session([
                'auth_role' => 'customer',
                'auth_id' => $customer->customer_id,
                'auth_name' => $customer->full_name,
            ]);

            return redirect()->route('register.success');
        }
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'role' => 'required|in:customer,staff',
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($data['role'] === 'staff') {
            $user = Staff::where('email', $data['email'])->first();
            if (! $user || ! Hash::check($data['password'], $user->password_hash)) {
                return back()->withErrors(['email' => 'Invalid staff credentials'])->withInput();
            }

            session([
                'auth_role' => 'staff',
                'auth_id' => $user->staff_id,
                'auth_name' => $user->name,
            ]);

            return redirect()->route('staff.dashboard');
        } else {
            $user = Customer::where('email', $data['email'])->first();
            if (! $user || ! Hash::check($data['password'], $user->password_hash)) {
                return back()->withErrors(['email' => 'Invalid customer credentials'])->withInput();
            }

            if ($user->is_blacklisted) {
                return back()->withErrors(['email' => 'Account is blacklisted. Please contact Hasta Travels & Tours.']);
            }

            session([
                'auth_role' => 'customer',
                'auth_id' => $user->customer_id,
                'auth_name' => $user->full_name,
            ]);

            return redirect()->route('home');
        }
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('home');
    }
}
