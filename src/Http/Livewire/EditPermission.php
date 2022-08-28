<?php

namespace RomanStruk\LaravelPermissionUi\Http\Livewire;

use LivewireUI\Modal\ModalComponent;

class EditPermission extends ModalComponent
{
    public string $userName;
    public int $permissionId;
    public bool $access;
    public ?string $guard;
    public ?\Illuminate\Contracts\Auth\Authenticatable $user;
    public $permission;
    public bool $isDirectPermission;

    public function mount(int $permission_id, string $user_name, int $user_id, ?string $guard, bool $access)
    {
        $this->userName = $user_name;
        $this->permissionId = $permission_id;
        $this->guard = $guard;
        $this->access = $access;
        $provider = config('auth.guards.' . $guard . '.provider');
        $this->user = app('auth')->createUserProvider($provider)->retrieveById($user_id);

        $permissionClass = $this->user->getPermissionClass();
        $this->permission = $permissionClass->findById($permission_id, $this->guard);

        $this->isDirectPermission = $this->user->hasDirectPermission($permission_id);
    }

    public function closeAndRevokePermission()
    {
        $this->user->revokePermissionTo($this->permissionId);

        $this->closeModalWithEvents([
            'permissionWasUpdated', // Emit global event
        ]);
    }

    public function closeAndGivePermission()
    {
        $this->user->givePermissionTo($this->permissionId);

        $this->closeModalWithEvents([
            'permissionWasUpdated', // Emit global event
        ]);
    }

    public function render()
    {
        return view('permissions-ui::livewire.modals.edit-permission');
    }
}