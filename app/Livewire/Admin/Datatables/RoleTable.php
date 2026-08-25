<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class RoleTable extends DataTableComponent
{
    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Rol", "name")
                ->sortable(),
            Column::make("N° Usuarios")
                ->label(fn($row) => $row->users_count),
            Column::make('Permisos')
                ->label(function ($row) {
                    return view('admin.roles.permissions', ['permissions' => $row->permissions]);
                }),
            Column::make('Acciones')
                ->label(function ($row) {
                    return view('admin.roles.actions', ['role' => $row]);
                })
        ];
    }

    public function builder(): Builder
    {
        return Role::query()->withCount('users')->with('permissions');
    }
}
