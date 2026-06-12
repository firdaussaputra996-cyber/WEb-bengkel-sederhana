<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View
    {
        return view('landing', [
            'services' => Service::where('is_active', true)->latest()->take(6)->get(),
        ]);
    }
}
