<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class MechanicController extends Controller
{
    public function index(): View
    {
        return view('admin.mechanics.index', [
            'mechanics' => User::where('role', 'mechanic')->withCount('assignedBookings')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'mechanic',
        ]);

        return back()->with('success', 'Akun mekanik berhasil dibuat. Mekanik dapat login menggunakan email dan password tersebut.');
    }
}
