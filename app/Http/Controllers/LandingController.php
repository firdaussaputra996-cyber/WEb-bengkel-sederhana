<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\SparePart;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View
    {
        return view('landing', [
            'services' => Service::where('is_active', true)
                ->where('category', '!=', 'Sparepart')
                ->latest()
                ->take(6)
                ->get(),
            'sparepartGroups' => SparePart::groupedOptions(),
        ]);
    }

    public function spareparts(): View
    {
        return view('spareparts', [
            'addonGroups' => SparePart::groupedOptions(),
        ]);
    }
}
