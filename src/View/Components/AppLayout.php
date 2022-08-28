<?php

namespace RomanStruk\LaravelPermissionUi\View\Components;

use Illuminate\View\Component;

class AppLayout extends Component
{
    public function render()
    {
        return view('permissions-ui::layouts.app');
    }
}
