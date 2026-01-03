<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
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
                'college' => 'nullable|string|max:100',
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
                'college_name' => $data['college'] ?? null,
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

    /**
     * Display the forgot password form page
     * @return \Illuminate\View\View
     */
    public function showForgotPassword()
    {
        return view('auth.customer.forgot-password');
    }

    /**
     * Handle password reset request
     * Generates a reset token and stores it in database
     * In production, this would send an email with the reset link
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendPasswordReset(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:customers,email',
        ]);

        // Generate reset token
        $token = Str::random(64);

        // Delete any existing reset tokens for this email
        DB::table('password_resets')->where('email', $validated['email'])->delete();

        // Insert new reset token (hash the token for security)
        DB::table('password_resets')->insert([
            'email' => $validated['email'],
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        // Store token and email in session for the reset flow
        $request->session()->put('reset_token', $token);
        $request->session()->put('reset_email', $validated['email']);

        // Redirect to reset page with token
        return redirect()->route('password.reset', ['token' => $token])
            ->with('success', 'Password reset link has been generated. Please reset your password.');
    }

    /**
     * Display the password reset form page
     * @param Request $request
     * @param string $token Reset token from URL
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showResetPassword(Request $request, $token)
    {
        // Get email from session or validate token from database
        $email = $request->session()->get('reset_email');
        
        // If no email in session, try to find it from database using token
        if (!$email) {
            $passwordReset = DB::table('password_resets')->get();
            foreach ($passwordReset as $reset) {
                if (Hash::check($token, $reset->token)) {
                    $email = $reset->email;
                    // Check if token is expired (24 hours)
                    if (Carbon::parse($reset->created_at)->addHours(24)->isPast()) {
                        DB::table('password_resets')->where('email', $reset->email)->delete();
                        return redirect()->route('password.forgot')
                            ->with('error', 'Reset token has expired. Please request a new one.');
                    }
                    break;
                }
            }
        }
        
        if (!$email) {
            return redirect()->route('password.forgot')
                ->with('error', 'Invalid or expired reset token.');
        }

        return view('auth.customer.reset-password', ['token' => $token, 'email' => $email]);
    }

    /**
     * Handle password reset submission
     * Validates token and updates customer password
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:customers,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Get password reset record
        $passwordReset = DB::table('password_resets')
            ->where('email', $validated['email'])
            ->first();

        if (!$passwordReset) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.'])->withInput();
        }

        // Verify token using hash check
        if (!Hash::check($validated['token'], $passwordReset->token)) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.'])->withInput();
        }

        // Check if token is expired (24 hours)
        if (Carbon::parse($passwordReset->created_at)->addHours(24)->isPast()) {
            DB::table('password_resets')->where('email', $validated['email'])->delete();
            return back()->withErrors(['email' => 'Reset token has expired. Please request a new one.'])->withInput();
        }

        // Update customer password
        $customer = Customer::where('email', $validated['email'])->first();
        if ($customer) {
            $customer->update([
                'password_hash' => Hash::make($validated['password']),
            ]);
        }

        // Delete used reset token
        DB::table('password_resets')->where('email', $validated['email'])->delete();
        $request->session()->forget(['reset_token', 'reset_email']);

        return redirect()->route('login')
            ->with('success', 'Your password has been reset successfully. Please login with your new password.');
    }
}
