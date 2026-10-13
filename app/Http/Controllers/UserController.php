<?php

namespace App\Http\Controllers;

use App\Enums\UserTypeEnum;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\ClassResponsible;
use App\Models\SubGrade;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles:id,name', 'userType:id,name'])->paginate(50);

        return inertia('User/Index', ['users' => $users]);
    }

    public function create()
    {
        return inertia('User/Create', [
            'userTypes' => UserType::all(['id', 'name']),
            'roles' => $this->assignableRoles(),
            'canAssignRoles' => request()->user()->can('roles.edit'),
        ]);
    }

    public function store(CreateUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'user_type_id' => $request->user_type_id,
            'is_active' => $request->is_active == 1 ? true : false,
            'password' => Hash::make($request->password),
        ]);

        $this->syncRoles($request, $user);

        // Upload user photo
        if ($request->hasFile('photo')) {
            $user->photo = 'profile-photos/'.$this->upload($request);
            $user->save();
        }

        return redirect('users');
    }

    public function edit(User $user)
    {
        $subGrades = SubGrade::all(['id', 'name']);
        $responsibles = ClassResponsible::where('teacher_id', $user->id)->pluck('id');

        $user->load('roles:id,name');

        return inertia('User/Edit', [
            'user' => $user,
            'userTypes' => UserType::all(['id', 'name']),
            'subGrades' => $subGrades,
            'resposibles' => $responsibles,
            'roles' => $this->assignableRoles(),
            'canAssignRoles' => request()->user()->can('roles.edit'),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->name = $request->name;
        $user->en_name = $request->en_name;
        $user->email = $request->email;
        $user->user_type_id = $request->user_type_id;
        $user->is_active = $request->is_active == 1 ? true : false;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        // Upload user photo
        if ($request->hasFile('photo')) {
            $existingFilePath = $user->photo;

            if ($existingFilePath) {
                $explodeFile = explode('storage/', $existingFilePath);
                Storage::delete('public/'.end($explodeFile));
            }

            $user->photo = 'profile-photos/'.$this->upload($request);
        }

        if ($file = $request->file('signature')) {
            $fileName = time().$file->getClientOriginalName();

            $file->move(public_path().'/images/', $fileName);

            $user->signature = $fileName;
        }

        $user->save();
        $this->syncRoles($request, $user);

        return redirect('users');
    }

    public function upload($request)
    {
        if ($request->hasFile('photo')) {
            $filename = time().'-'.$request->photo->getClientOriginalName();
            $request->photo->storeAs('profile-photos', $filename, 'public');

            return $filename;
        }
    }

    private function assignableRoles()
    {
        return request()->user()->can('roles.edit')
            ? Role::orderBy('name')->get(['id', 'name'])
            : [];
    }

    private function syncRoles($request, User $user): void
    {
        if ($request->user()->can('roles.edit') && $request->has('roles')) {
            $user->syncRoles($request->input('roles', []));

            if (! $user->wasRecentlyCreated || $request->filled('roles')) {
                return;
            }
        }

        if ($user->roles()->doesntExist()) {
            $defaultRole = match ((int) $user->user_type_id) {
                UserTypeEnum::SuperAdmin->value => 'Super Admin',
                UserTypeEnum::Teacher->value => 'Teacher',
                default => 'User',
            };

            if (Role::where('name', $defaultRole)->exists()) {
                $user->assignRole($defaultRole);
            }
        }
    }
}
