<?php

namespace App\Http\Controllers\Landlord\Admin;

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('landlord.admin.dashboard');
    }
}
