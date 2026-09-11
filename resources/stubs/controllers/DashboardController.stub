<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if (view()->exists('dashboard.index') && $user && method_exists($user, 'projects')) {
            $totalProjects = $user->projects()->count();
            $activeProjects = $user->projects()->where('status', 'active')->count();
            $recentProjects = $user->projects()->latest()->take(5)->get();

            return view('dashboard.index', compact('totalProjects', 'activeProjects', 'recentProjects'));
        }

        return view('dashboard', [
            'user' => $user,
        ]);
    }
}
