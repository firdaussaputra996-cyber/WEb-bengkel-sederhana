<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        return view('admin.customers.index', [
            'customers' => User::with(['profile'])->withCount('bookings')->where('role', 'user')->latest()->paginate(12),
        ]);
    }
}
