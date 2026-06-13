<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    /**
     * Show admin login form
     */
    public function showLoginForm()
    {
        if (auth('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $admin = Admin::where('email', $credentials['email'])->first();

        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            // Log failed attempt
            Log::warning('Failed login attempt', [
                'email' => $credentials['email'],
                'ip' => $request->ip(),
                'time' => now(),
            ]);

            return back()->withErrors(['email' => 'Invalid email or password'])->withInput();
        }

        if (!$admin->is_active) {
            return back()->withErrors(['email' => 'Your account is disabled'])->withInput();
        }

        // Update last login
        $admin->update(['last_login_at' => now()]);

        // Create session
        /** @var \Illuminate\Auth\SessionGuard $guard */
        $guard = auth('admin');
        $guard->login($admin, $request->filled('remember'));

        return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . $admin->name);
    }

    /**
     * Show forgot password form
     */
    public function showForgotForm()
    {
        return view('admin.auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            // Don't reveal if email exists (security)
            return back()->with('status', 'Jika email tersebut terdaftar, link reset password telah dikirimkan.');
        }

        // Generate reset token
        $token = Str::random(60);
        $hashedToken = Hash::make($token);

        // Store token
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $hashedToken,
                'created_at' => now(),
            ]
        );

        $isDevMode = config('mail.default') === 'log' || config('mail.mailers.'.config('mail.default').'.transport') === 'log';
        $resetUrl  = url('/admin/reset-password/' . $token);

        if ($isDevMode) {
            // Development mode: email goes to log, surface reset URL in the UI
            Log::info('Admin password reset link (dev mode)', [
                'email'     => $request->email,
                'reset_url' => $resetUrl,
            ]);

            return back()
                ->with('status', 'Mode development: link reset ditampilkan di bawah karena MAIL_MAILER=log.')
                ->with('dev_reset_url', $resetUrl);
        }

        // Production: actually send the email
        try {
            Mail::send('admin.auth.email.reset-password', ['token' => $token], function ($message) use ($request) {
                $message->to($request->email)->subject('Reset Password Admin — Toba Tourism');
            });

            return back()->with('status', 'Link reset password telah dikirimkan ke email Anda. Cek inbox (dan folder spam).');
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['email' => 'Gagal mengirim email. Silakan coba lagi atau hubungi super admin.']);
        }
    }

    /**
     * Show password reset form
     */
    public function showResetForm($token)
    {
        return view('admin.auth.reset-password', ['token' => $token]);
    }

    /**
     * Handle password reset
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])/',
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, number, and special character',
        ]);

        // Verify token
        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return back()->withErrors(['token' => 'Invalid or expired token']);
        }

        // Token expires in 1 hour
        if (now()->diffInMinutes($reset->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['token' => 'Link reset password telah kedaluwarsa. Silakan minta link baru.']);
        }

        // Update admin password
        $admin = Admin::where('email', $request->email)->first();
        if (!$admin) {
            return back()->withErrors(['email' => 'Email admin tidak ditemukan.']);
        }

        $admin->update(['password' => Hash::make($request->password)]);

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        Log::info('Admin password reset successfully', [
            'admin_id' => $admin->id,
            'email'    => $admin->email,
            'ip'       => $request->ip(),
        ]);

        return redirect()->route('admin.login')->with('success', 'Password berhasil diubah. Silakan login dengan password baru Anda.');
    }

    /**
     * Handle admin logout with proper session cleanup
     * 
     * @param  \\Illuminate\\Http\\Request  $request
     * @return \\Illuminate\\Http\\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Get auth guard with explicit type hint
        /** @var \\Illuminate\\Auth\\SessionGuard $guard */
        $guard = auth('admin');
        
        // Log logout action
        $admin = $guard->user();
        if ($admin) {
            Log::info('Admin logout', [
                'admin_id' => $admin->id,
                'username' => $admin->username,
                'ip' => $request->ip(),
                'time' => now(),
            ]);
        }
        
        // Explicitly logout from guard
        $guard->logout();
        
        // Invalidate session
        $request->session()->invalidate();
        
        // Regenerate CSRF token
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')
            ->with('success', 'You have been logged out successfully');
    }
}
