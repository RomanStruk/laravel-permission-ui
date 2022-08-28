<?php

// dashboard
use Illuminate\Support\Facades\Route;
use RomanStruk\LaravelPermissionUi\Http\Controllers\DashboardController;
use RomanStruk\LaravelPermissionUi\Http\Controllers\PermissionController;
use RomanStruk\LaravelPermissionUi\Http\Controllers\RoleController;

Route::get('/permissions-ui/', DashboardController::class)
    ->name('permissions-ui.index');

Route::get('/permissions-ui/roles/', RoleController::class)
    ->name('permissions-ui.roles.index');

Route::get('/permissions-ui/permissions/', PermissionController::class)
    ->name('permissions-ui.permissions.index');