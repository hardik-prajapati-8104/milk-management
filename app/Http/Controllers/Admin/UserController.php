<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\ActivityLog;
use App\Models\Route as DeliveryRoute;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::with('roles')->orderBy('name')->get(),
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Users & Roles' => null],
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', $this->formData());
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['photo', 'password', 'password_confirmation', 'role']);
        $data['password'] = Hash::make($request->validated('password'));
        $data['employee_code'] = $this->generateEmployeeCode();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('users/photos', 'public');
        }

        $user = User::create($data);
        $user->assignRole($request->validated('role'));

        ActivityLog::record('created', $user, new: ['email' => $user->email, 'role' => $request->validated('role')],
            description: "User {$user->name} created with role {$request->validated('role')}");

        return redirect()->route('admin.users.index')->with('success', "User \"{$user->name}\" created.");
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', $this->formData() + ['user' => $user]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $old = ['email' => $user->email, 'role' => $user->getRoleNames()->first()];
        $data = $request->safe()->except(['photo', 'password', 'password_confirmation', 'role']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->validated('password'));
        }
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('users/photos', 'public');
        }

        $user->update($data);
        $user->syncRoles([$request->validated('role')]);

        ActivityLog::record('updated', $user, old: $old, new: ['email' => $user->email, 'role' => $request->validated('role')],
            description: "User {$user->name} updated");

        return redirect()->route('admin.users.index')->with('success', "User \"{$user->name}\" updated.");
    }

    public function destroy(User $user): RedirectResponse
    {
        $name = $user->name;
        $user->delete();

        ActivityLog::record('deleted', description: "User {$name} deleted");

        return redirect()->route('admin.users.index')->with('success', "User \"{$name}\" deleted.");
    }

    protected function formData(): array
    {
        return [
            'roles' => Role::orderBy('name')->get(),
            'routes' => DeliveryRoute::active()->orderBy('name')->get(),
        ];
    }

    protected function generateEmployeeCode(): string
    {
        $last = User::orderByDesc('id')->whereNotNull('employee_code')->first();
        $next = $last ? ((int) preg_replace('/\D/', '', $last->employee_code)) + 1 : 1;

        return 'EMP' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
