<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return auth()->user()->role === 'admin' 
            ? redirect()->route('admin.dashboard') 
            : redirect()->route('user.dashboard');
    }
    
    public function adminDashboard()
    {
        return view('admin.dashboard');
    }
    
    public function userDashboard()
    {
        return view('user.dashboard');
    }
}