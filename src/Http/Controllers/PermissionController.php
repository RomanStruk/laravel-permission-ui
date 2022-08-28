<?php

namespace RomanStruk\LaravelPermissionUi\Http\Controllers;

class PermissionController
{
    public function __invoke()
    {
        return view('permissions-ui::permissions.index');
    }
}