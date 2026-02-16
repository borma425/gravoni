<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('settings.index', compact('users'));
    }

    /**
     * Store a new user
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'تم إنشاء الحساب بنجاح');
    }

    /**
     * Delete a user
     */
    public function destroyUser(User $user)
    {
        // Prevent deleting the current user
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'لا يمكنك حذف حسابك الخاص']);
        }

        $user->delete();

        return redirect()->route('settings.index')
            ->with('success', 'تم حذف الحساب بنجاح');
    }
}
