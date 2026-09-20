<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(){
        return Inertia::render('admin/Dashboard');
    }
}
