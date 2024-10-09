<?php

namespace App\Http\Controllers;

use App\Models\Main\UserResources;
use App\Models\Permissions;
use Illuminate\Http\Request;
use App\Models\Roles;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('roles.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,resource_id',
            'resource_id' => 'required|integer|exists:resources,resource_id',
        ]);

        $role = new Roles();
        $role->name = $request->input('name');
        $role->save();

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->input('permissions'));
        }

        return redirect()->route('roles.index')->with('success', 'Rol creado');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Roles::findOrFail($id);
        $permissions = UserResources::all();
        $rolePermissions = $role->permissions->pluck('resource_id');
        $permissionList = $permissions->map(function ($permission) use ($rolePermissions) {
            $permission->checked = in_array($permission->id, $rolePermissions->toArray());
            return $permission;
        })->toArray();
        return view('roles.edit', ['role' => $role, 'permissionList' => $permissionList]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array'
        ]);
        $permissions = collect($request->input('permissions'))->map(function ($item) use ($id) {
            $item_exploded = explode('-', $item);
            return ['resource_id' => $item_exploded[0], 'resource_code' => $item_exploded[1], 'role_id' => $id];
        })->toArray();

        DB::transaction(function () use ($id, $request, $permissions) {
            try {
                $role = Roles::findOrFail($id);
                $role->name = $request->input('name');
                $role->save();
                if ($request->has('permissions')) {
                    Permissions::where('role_id', $id)->whereNotIn('resource_id', collect($permissions)->pluck('resource_id'))->delete();
                    Permissions::upsert($permissions, uniqueBy: ['resource_id','role_id'], update: ['resource_id', 'resource_code', 'role_id']);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        });
        return redirect()->route('roles.index')->with('success', 'Rol actualizado');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
