<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validate($request, [
            'name' => 'required',
        ]);

        $role = Role::create($validated);
        return response()->json(['message' => 'Role created successfully!', 'role' => $role], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function showRole(string $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['role' => $role], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $this->validate($request, [
            'name' => 'required',
        ]);

        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }
        $role->update($validated);
        return response()->json(['message' => 'Role updated successfully!', 'role' => $role], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully'], Response::HTTP_OK);
    }
}