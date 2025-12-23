<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;
use App\Models\Staff;

/**
 * Handles user authentication: login, registration, and logout
 * Supports both customer and staff roles
 */
class AuthController extends Controller
{
    /**
     * Display the customer login form page
     * @return \Illuminate\View\View
     */
    public function showCustomerLogin()
    {
        return view('auth.customer.login');
    }

    /**
     * Display the staff login form page
     * @return \Illuminate\View\View
     */
    public function showStaffLogin()
    {
        return view('auth.staff.login');
    }

    /**
     * Display the login form page (legacy method - redirects to customer login)
     * @return \Illuminate\View\View
     * @deprecated Use showCustomerLogin() instead
     */
    public function showLoginForm()
    {
        return view('auth.customer.login');
    }

    /**
     * Display the customer registration form page.
     *
     * @return \Illuminate\View\View
     */
    public function showCustomerRegister()
    {
        return view('auth.customer.register');
    }

    /**
     * Display the staff registration form page.
     *
     * @return \Illuminate\View\View
     */
    public function showStaffRegister()
    {
        return view('auth.staff.register');
    }

    /**
     * Display registration success page after customer registration
     * Redirects to login if user is not authenticated
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showRegisterSuccess()
    {
        if (!session('auth_role')) {
            return redirect()->route('login');
        }
        return view('auth.register-success');
    }

    /**
     * Handle user registration for both customers and staff
     * Creates new user account and sets up session
     * 
     * @param Request $request Contains registration form data (role, name, email, password, etc.)
     * @return \Illuminate\Http\RedirectResponse Redirects to appropriate dashboard or success page
     */
    public function register(Request $request)
    {
        $role = $request->input('role', 'customer');

        // Handle staff registration
        if ($role === 'staff') {
            // Validate staff registration data
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:staff,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Create staff account with hashed password
            $staff = Staff::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password_hash' => Hash::make($data['password']),
                'role' => 'staff',
            ]);

            // Set session for staff user
            session([
                'auth_role' => 'staff',
                'auth_id' => $staff->staff_id,
                'auth_name' => $staff->name,
            ]);

            return redirect()->route('staff.dashboard');
        } else {
            // Handle customer registration
            // Validate customer registration data
            $data = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:customers,email',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:30',
                'utm_role' => 'nullable|in:student,staff',
            ]);

            // Create customer account with pending verification status
            // Document URLs (IC, UTM ID, License) are set to null initially - can be uploaded later
            $customer = Customer::create([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password_hash' => Hash::make($data['password']),
                'ic_url' => null,
                'utmid_url' => null,
                'license_url' => null,
                'utm_role' => $data['utm_role'] ?? null,
                'verification_status' => 'pending', // Requires staff approval
            ]);

            // Set session for customer user
            session([
                'auth_role' => 'customer',
                'auth_id' => $customer->customer_id,
                'auth_name' => $customer->full_name,
            ]);

            return redirect()->route('register.success');
        }
    }

    /**
     * Handle user login for both customers and staff
     * Validates credentials and sets up session
     * Checks if customer account is blacklisted
     * 
     * @param Request $request Contains login form data (role, email, password)
     * @return \Illuminate\Http\RedirectResponse Redirects to dashboard or home on success, back with errors on failure
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'role' => 'required|in:customer,staff',
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Handle staff login
        if ($data['role'] === 'staff') {
            $user = Staff::where('email', $data['email'])->first();
            
            // Verify password matches hashed password in database
            if (! $user || ! Hash::check($data['password'], $user->password_hash)) {
                return back()->withErrors(['email' => 'Invalid staff credentials'])->withInput();
            }

            // Set session for staff user
            session([
                'auth_role' => 'staff',
                'auth_id' => $user->staff_id,
                'auth_name' => $user->name,
            ]);

            return redirect()->route('staff.dashboard');
        } else {
            // Handle customer login
            $user = Customer::where('email', $data['email'])->first();
            
            // Verify password matches hashed password in database
            if (! $user || ! Hash::check($data['password'], $user->password_hash)) {
                return back()->withErrors(['email' => 'Invalid customer credentials'])->withInput();
            }

            // Check if customer account is blacklisted
            if ($user->is_blacklisted) {
                return back()->withErrors(['email' => 'Account is blacklisted. Please contact Hasta Travels & Tours.']);
            }

            // Set session for customer user
            session([
                'auth_role' => 'customer',
                'auth_id' => $user->customer_id,
                'auth_name' => $user->full_name,
            ]);

            return redirect()->route('home');
        }
    }

    /**
     * Handle user logout
     * Clears all session data and redirects to home
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('home');
    }
}
