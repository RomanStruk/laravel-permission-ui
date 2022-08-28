<?php

namespace RomanStruk\LaravelPermissionUi\Http\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionPage extends Component
{
    use WithPagination;

    public $guard = null;
    public $searchPermission;
    public $guards = [];

    protected $queryString = ['guard', 'searchPermission'];

    public function mount()
    {
        if (is_null($this->guard)) {
            $this->guard = config('auth.defaults.guard');
        }

        $this->guards = collect(config('auth.guards'))->keys();
    }

    public function render()
    {
        $permissions = app(PermissionRegistrar::class)->getPermissionClass()
            ->query()
            ->when($this->searchPermission, function (Builder $builder) {
                return $builder->where('name', 'like', '%' . $this->searchPermission . '%');
            })
            ->where('guard_name', '=', $this->guard)
            ->paginate(40);

        return view('permissions-ui::livewire.permission-page', compact('permissions'));
    }
}