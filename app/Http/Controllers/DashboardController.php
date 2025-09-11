<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{


    /**
     * Generate assignments for today
     */
    public function index()
    {

        return view('mpm.page.dashboard.index');
    }
}
