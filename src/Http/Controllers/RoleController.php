<?php

namespace RomanStruk\LaravelPermissionUi\Http\Controllers;

class RoleController
{
    public function __invoke()
    {
        return view('permissions-ui::roles.index');
    }
}