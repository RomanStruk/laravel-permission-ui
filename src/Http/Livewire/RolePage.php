<?php

namespace RomanStruk\LaravelPermissionUi\Http\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePage extends Component
{
    use WithPagination;

    public $guard = null;
    public $guards = [];
    public $searchRole;

    protected $queryString = ['guard', 'searchRole'];

    public function mount()
    {
        if (is_null($this->guard)) {
            $this->guard = config('auth.defaults.guard');
        }

        $this->guards = collect(config('auth.guards'))->keys();
    }

    public function render()
    {
        $roles = app(PermissionRegistrar::class)->getRoleClass()
            ->query()
            ->when($this->searchRole, function (Builder $builder) {
                return $builder->where('name', 'like', '%' . $this->searchRole . '%');
            })
            ->where('guard_name', '=', $this->guard)
            ->paginate(15);

        return view('permissions-ui::livewire.role-page', compact('roles'));
    }
}