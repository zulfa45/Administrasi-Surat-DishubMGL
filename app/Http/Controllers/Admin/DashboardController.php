<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers      = User::count();
        $totalDepartments = Department::count();

        return view('admin.dashboard', compact('totalUsers', 'totalDepartments'));
    }
}
