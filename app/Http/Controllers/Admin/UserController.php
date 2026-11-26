<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index()
    {
        Gate::authorize('read-users');
        
        return view('admin.users.index');
    }

    public function create()
    {
        Gate::authorize('create-users');

        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create-users');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario creado correctamente',
            'text' => 'El usuario ha sido creado exitosamente.',
        ]);

        return redirect()->route('admin.users.edit', $user);
    }

    public function show(User $user)
    {
        Gate::authorize('read-users');

        return view('admin.users.show');
    }

    public function edit(User $user)
    {
        Gate::authorize('update-users');

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('update-users');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario actualizado correctamente',
            'text' => 'El usuario ha sido actualizado exitosamente.',
        ]);

        return redirect()->route('admin.users.edit', $user);
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete-users');

        $user->delete();

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario eliminado correctamente',
            'text' => 'El usuario ha sido eliminado exitosamente.',
        ]);

        return redirect()->route('admin.users.index');
    }
}
