<?php

namespace RomanStruk\LaravelPermissionUi\Http\Controllers;

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('permissions-ui::dashboard.index');
    }
}