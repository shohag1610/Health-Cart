<?php

namespace App\Http\Controllers;


class DashboardController extends Controller
{
    // Display Shopping List
    public function index()
    {
        return view('dashboard');
    }
}
