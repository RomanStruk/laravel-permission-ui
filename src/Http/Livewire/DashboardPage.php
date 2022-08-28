<?php

namespace RomanStruk\LaravelPermissionUi\Http\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class DashboardPage extends Component
{
    use WithPagination;

    public $searchLogin;
    public $searchPermission;
    public $guard = null;
    public $guards = [];

    protected $queryString = ['searchLogin', 'searchPermission', 'guard'];

    public $listeners = [
        'permissionWasUpdated' => '$refresh',
    ];

    public function mount()
    {
        if (is_null($this->guard)) {
            $this->guard = config('auth.defaults.guard');
        }

        $this->guards = collect(config('auth.guards'))->keys();
    }

    protected function newModel(): Model
    {
        $model = getModelForGuard($this->guard);

        return new $model;
    }

    public function render()
    {
        $adminsQuery = $this->newModel()::with('permissions')
            ->when($this->searchLogin, function (Builder $builder) {
                return $builder->where('name', 'like', '%' . $this->searchLogin . '%');
            });
        $count = $adminsQuery->count();

        $users = $adminsQuery->forPage($this->page, 9)->get();
        $users = $users->mapWithKeys(function ($user) {
            return [$user->name => ['permissions' => $user->getAllPermissions()->pluck('id'), 'user_id' => $user->id]];
        });

        $body = Permission::query()
            ->when($this->searchPermission, function (Builder $builder) {
                return $builder->where('name', 'like', '%' . $this->searchPermission . '%');
            })
            ->get()
            ->map(function ($permission) use ($users) {
                return $users->map(function (array $user_data, string $name) use ($permission) {
                    return [
                        'permission_id' => $permission['id'],
                        'user_name' => $name,
                        'user_id' => $user_data['user_id'],
                        'guard' => $permission['guard_name'],
                        'access' => $user_data['permissions']->contains($permission['id']),
                    ];
                })->prepend($permission['name']);
            });

        $header = $users->keys()->prepend('')->toArray();
        $body = $body->toArray();

        $pagination = new LengthAwarePaginator($users->keys(), $count, 9, $this->page);

        return view('permissions-ui::livewire.dashboard-page', compact('pagination', 'header', 'body'));
    }
}
