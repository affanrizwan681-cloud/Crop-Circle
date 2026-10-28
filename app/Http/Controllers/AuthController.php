<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FarmerProfile;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->status === 'suspended') {
                Auth::logout();
                return back()->with('danger', 'Your account has been suspended. Please contact support.');
            }

            ActivityLog::record($user->id, 'login', 'User logged in successfully.');

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome back, Administrator!');
            }

            if ($user->isFarmer()) {
                if (!$user->farmerProfile || !$user->farmerProfile->is_approved) {
                    return redirect()->route('farmer.pending');
                }
                return redirect()->intended(route('farmer.dashboard'))->with('success', 'Welcome to your Farm Portal!');
            }

            return redirect()->intended(route('customer.dashboard'))->with('success', 'Welcome back to CropCircle, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $role = $request->input('role', 'customer');
        if (!in_array($role, ['customer', 'farmer'], true)) {
            $role = 'customer';
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];

        if ($role === 'farmer') {
            $rules['farm_name'] = ['required', 'string', 'max:255'];
            $rules['farm_description'] = ['required', 'string', 'max:1000'];
            $rules['city'] = ['required', 'string', 'max:100'];
            $rules['state'] = ['required', 'string', 'max:100'];
            $rules['operating_days'] = ['nullable', 'array'];
        }

        $validated = $request->validate($rules);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'role' => $role,
            'status' => $role === 'farmer' ? 'pending' : 'active',
            'password' => Hash::make($validated['password']),
        ]);

        if ($role === 'farmer') {
            FarmerProfile::create([
                'user_id' => $user->id,
                'farm_name' => $validated['farm_name'],
                'contact_person' => $validated['name'],
                'description' => $validated['farm_description'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'operating_days' => $validated['operating_days'] ?? ['Saturday', 'Sunday'],
                'is_approved' => false,
            ]);

            ActivityLog::record($user->id, 'farmer_registered', 'New farmer applied for verification: ' . $validated['farm_name']);

            // Notify Admin
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                Notification::send(
                    $admin->id,
                    'New Farmer Application',
                    $validated['farm_name'] . ' registered and is awaiting approval.',
                    'farmer_approval',
                    '/admin/farmers'
                );
            }

            Auth::login($user);
            return redirect()->route('farmer.pending')->with('info', 'Your farmer account was created! Your profile is currently under review by administrators.');
        }

        Auth::login($user);
        ActivityLog::record($user->id, 'customer_registered', 'New customer account created.');

        return redirect()->route('customer.dashboard')->with('success', 'Welcome to CropCircle! Your customer account has been created.');
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            ActivityLog::record(Auth::id(), 'logout', 'User logged out.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('info', 'You have been signed out successfully.');
    }

    public function showProfile(): View
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
        ];

        if ($user->isFarmer()) {
            $rules['farm_name'] = ['required', 'string', 'max:255'];
            $rules['description'] = ['required', 'string'];
            $rules['city'] = ['required', 'string', 'max:100'];
            $rules['state'] = ['required', 'string', 'max:100'];
            $rules['pickup_start_time'] = ['required'];
            $rules['pickup_end_time'] = ['required'];
            $rules['operating_days'] = ['nullable', 'array'];
        }

        $validated = $request->validate($rules);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ]);

        if ($user->isFarmer() && $user->farmerProfile) {
            $user->farmerProfile->update([
                'farm_name' => $validated['farm_name'],
                'description' => $validated['description'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'pickup_start_time' => $validated['pickup_start_time'],
                'pickup_end_time' => $validated['pickup_end_time'],
                'operating_days' => $validated['operating_days'] ?? [],
            ]);
        }

        ActivityLog::record($user->id, 'profile_updated', 'User updated their profile details.');

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::record($user->id, 'password_changed', 'User changed their password.');

        return back()->with('success', 'Password updated successfully.');
    }
}
