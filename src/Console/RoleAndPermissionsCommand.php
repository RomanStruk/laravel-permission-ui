<?php

namespace RomanStruk\LaravelPermissionUi\Console;

use Illuminate\Console\Command;
use Illuminate\Foundation\Auth\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionsCommand extends Command
{
    protected $signature = 'permission:crud {admin?*} {--r=*} {--p=*}';

    protected $description = 'Управління дозволами';

    protected string $styleTable = 'box';

    protected string $guard = 'admin';

    protected array $actions = [
        'Вихід' => 'finish',
        'Перевірка дозволів користувачів' => 'indexUserPermissions',
        'Користувачі та їх ролі' => 'indexAdmins',
        'Ролі та їх дозволи' => 'indexRoles',
        'Дозволи' => 'indexPermissions',
    ];

    protected array $adminActions = [
        'Назад' => 'finish',
        'Призначити роль' => 'assignRole',
        'Забрати роль' => 'removeRole',
        'Надати дозвіл' => 'givePermissionToUser',
        'Забрати дозвіл' => 'revokePermissionToUser', // ✔
    ];

    protected array $roleActions = [
        'Назад' => 'finish',
        'Додати роль' => 'createRoles',
        'Видалити роль' => 'destroyRoles',
        'Надати дозвіл для ролі' => 'givePermissionToRole',
        'Забрати дозвіл для ролі' => 'revokePermissionToRole',
    ];

    protected array $permissionActions = [
        'Назад' => 'finish',
        'Додати дозвіл' => 'createPermissions',
        'Видалити дозвіл' => 'destroyPermissions',
    ];
    protected string $model = User::class;

    public function handle()
    {
        if ($this->resolveParameters() > 0){
            return 0;
        }

        $this->actions($this->actions);

        return 0;
    }

    protected function resolveParameters(): int
    {
        $userLogin = $this->argument('admin');
        $rolesOption = $this->option('r');
        $permissionsOption = $this->option('p');
        $permissions = collect();
        $roles = collect();
        if (count($permissionsOption) > 0){
            foreach ($permissionsOption as $name) {
                $permissions->push(Permission::findOrCreate($name, $this->guard));
            }
            $this->info('Дозволи додані!');
        }
        if (count($rolesOption) > 0){
            foreach ($rolesOption as $name) {
                $role = Role::findOrCreate($name, $this->guard);
                $roles->push($role);
                $role->givePermissionTo($permissions);
            }
            $this->info('Ролі додані!');
        }
        if ($userLogin){
            $user = $this->newModel()::query()->where('login', $userLogin)->first();
            if (!$user){
                $this->error('Admin not found!');
                return 0;
            }
            $user->assignRole($roles);

            $this->info('Користувачу додано ролі!');

            return 1;
        }

        return $roles->count() + $permissions->count();
    }

    /**
     * Таблиця дозволів користувачів
     */
    protected function indexUserPermissions()
    {
        $this->info('Дозволи користувачів:');

        (new (config('permissions-ui.model')))::with('permissions')->chunk(12, function ($admins) {
            $admins = $admins->mapWithKeys(function ($admin) {
                return [$admin->login => ['permissions' => $admin->getAllPermissions()->pluck('id')]];
            });

            $body = Permission::query()->get()->pluck('name', 'id')
                ->map(function ($permission, $id) use ($admins) {
                    return $admins->map(function (array $admin_data) use ($id) {
                        return $admin_data['permissions']->contains($id) ? ' ✔' : ' ·';
                    })->prepend($permission);
                });

            $this->table(
                $admins->keys()->prepend('')->toArray(),
                $body->toArray(),
                $this->styleTable
            );

        });

        $this->actions($this->actions);
    }

    /**
     * Список дозволів
     */
    protected function indexPermissions()
    {
        $this->info('Список дозволів:');
        $this->table(
            ['id', 'Назва', 'guard_name'],
            Permission::query()->orderBy('id')->get(['id', 'name', 'guard_name'])->toArray(),
            $this->styleTable
        );
        $this->actions($this->permissionActions);
        $this->actions($this->actions);
    }

    /**
     * Видалити дозвіл
     */
    protected function destroyPermissions()
    {
        $permissions = Permission::query()->orderBy('id')->get();
        $permissionName = $this->choice('Який дозвіл видалити?', $permissions->pluck('name')->toArray());

        if(Permission::query()->where('name', $permissionName)->delete()){
            $this->info('Дозвіл видалено!');
        }else{
            $this->error('Не вдалось видалити!');
        }
    }

    /**
     * Додати новий дозвіл
     */
    protected function createPermissions()
    {
        $name = $this->ask('Назва дозволу (латинецею):');
        $permission = Permission::create(['name' => $name, 'guard_name' => $this->guard]);
        if ($permission){
            $this->info('Дозвіл "' . $permission->name . '" доданий!');
        }else{
            $this->error('Не вдалось створити дозвіл!');
        }
    }

    /**
     * Надати дозвіл користувачу
     */
    protected function givePermissionToUser()
    {
        $admin = Admin::with(['roles', 'permissions'])->findOrFail($this->ask('Введіть id користувача'));

        $choicePermissions = Permission::all(['id', 'name'])->pluck('name')->toArray();

        $permissionNames = $this->choice(
            'Виберіть дозволи для користувача які надати(через кому якщо декілька)',
            $choicePermissions,
            null,
            count($choicePermissions),
            true
        );
        $permissions = Permission::query()->whereIn('name', $permissionNames)->get();
        $admin->givePermissionTo($permissions);
    }

    /**
     * Забрати дозволи в користувача
     */
    protected function revokePermissionToUser()
    {
        $admin = Admin::with(['roles', 'permissions'])->findOrFail($this->ask('Введіть id користувача'));

        $choicePermissions = $admin->permissions->pluck('name')->toArray();
        if (count($choicePermissions) === 0){
            $this->error('В користувача не знайдено дозволів окремих від ролей!');
            return;
        }
        $permissionNames = $this->choice(
            'Виберіть дозволи для користувача які надати(через кому якщо декілька)',
            $choicePermissions,
            null,
            count($choicePermissions),
            true
        );
        $permissions = Permission::query()->whereIn('name', $permissionNames)->get();

        $admin->revokePermissionTo($permissions);
    }

    /**
     * Надати дозвіл для ролі
     */
    protected function givePermissionToRole()
    {
        $role = Role::with(['permissions'])->findOrFail($this->ask('Введіть id ролі'));

        $choicePermissions = Permission::all(['id', 'name'])->pluck('name')->toArray();
        if (count($choicePermissions) === 0){
            $this->error('Не знайдено дозволів!');
            return;
        }

        $permissionNames = $this->choice(
            'Виберіть дозволи для ролі які надати(через кому якщо декілька)',
            $choicePermissions,
            null,
            count($choicePermissions),
            true
        );
        $permissions = Permission::query()->whereIn('name', $permissionNames)->get();
        if ($role->givePermissionTo($permissions)){
            $this->info('givePermissionTo');
        }else{
            $this->error('givePermissionTo');
        }
    }

    /**
     * Забрати дозвіл для ролі
     */
    protected function revokePermissionToRole()
    {
        $role = Role::with(['permissions'])->findOrFail($this->ask('Введіть id ролі'));

        $choicePermissions = $role->permissions->pluck('name')->toArray();
        if (count($choicePermissions) === 0){
            $this->error('Не знайдено дозволів!');
            return;
        }
        $permissionNames = $this->choice(
            'Виберіть дозволи для користувача які надати(через кому якщо декілька)',
            $choicePermissions,
            null,
            count($choicePermissions),
            true
        );
        $permissions = Permission::query()->whereIn('name', $permissionNames)->get();

        $role->revokePermissionTo($permissions);
    }

    /**
     * Додавання ролей
     */
    protected function createRoles()
    {
        $name = $this->ask('Назва ролі');
        if($this->confirm('Додати нову роль? ' . $name, true)){
            $role = Role::create(['name' => $name, 'guard_name' => $this->guard]);
            $this->info('Роль "' . $role->name . '" додана!');
        }
    }

    /**
     * Видалення ролей
     */
    protected function destroyRoles()
    {
        $roles = Role::query()->orderBy('id')->get();
        $roleName = $this->choice('Яку роль видалити?', $roles->pluck('name')->toArray());

        if(Role::query()->where('name', $roleName)->delete()){
            $this->info('Роль видалено!');
        }else{
            $this->error('Не вдалось видалити!');
        }
    }

    /**
     * Забрати роль у користувача
     */
    protected function removeRole()
    {
        $admin = Admin::with(['roles'])->findOrFail($this->ask('Введіть id користувача'));

        $roleName = $this->choice('Яку роль забрати?', $admin->roles->pluck('name')->toArray());
        $role = $admin->roles->where('name', $roleName)->firstOrFail();
        $admin->removeRole($role);
    }

    /**
     * Призначення ролі користувачу
     */
    protected function assignRole()
    {
        $admin = Admin::with(['roles'])->findOrFail($this->ask('Введіть id користувача'));

        $roles = Role::whereNotIn('name', $admin->getRoleNames())->get();
        if ($roles->count() === 0){
            $this->comment('Всі ролі назначені!');
            return;
        }
        $roleName = $this->choice('Яку роль надати?', $roles->pluck('name')->toArray());
        $role = $roles->where('name', $roleName)->firstOrFail();
        $admin->assignRole($role);
    }

    /**
     * Список ролей
     */
    protected function indexRoles()
    {
        $this->info('Список ролей:');
        $roles = Role::with(['permissions'])->get(['id', 'name', 'guard_name'])->toArray();
        $roles = collect($roles)->map(function ($role) {
            $role['permissions'] =  collect($role['permissions'])->implode('name', "\n");
            return collect($role)->only('id', 'name', 'guard_name', 'permissions');
        });

        $this->table(
            ['id', 'Назва', 'guard_name', 'Дозволи ролі'],
            $roles->toArray(),
            $this->styleTable,
        );
        $this->actions($this->roleActions);
        $this->actions($this->actions);
    }

    /**
     * Список адміністраторів
     */
    protected function indexAdmins()
    {
        $this->info('Список адміністраторів:');
        $admins = Admin::with(['roles', 'permissions'])->get();
        $admins = collect($admins->toArray())->map(function ($user) {
            $user['roles'] =  collect($user['roles'])->implode('name', "\n");
            $user['permissions'] =  collect($user['permissions'])->implode('name', "\n");
            return collect($user)->only('id', 'name', 'email', 'login', 'roles', 'permissions');
        });

        $this->table(
            ['id', 'Імя', 'email', 'Логін', 'Ролі', 'Дозволи користувача'],
            $admins->toArray(),
            $this->styleTable,
        );

        $this->actions($this->adminActions);
        $this->actions($this->actions);
    }

    protected function actions(array $actions)
    {
        $action = $this->choice('Що робимо? ', array_keys($actions));
        if (key_exists($action, $actions) and method_exists($this, $actions[$action])){
            $this->{$actions[$action]}();
        }
    }

    protected function newModel()
    {
        return new $this->model;
    }

    protected function finish()
    {
    }
}
