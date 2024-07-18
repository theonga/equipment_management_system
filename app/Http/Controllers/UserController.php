<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

     public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken('token')->plainTextToken;
    }

    public function index(Request $request)
    { 
        $users = $request->user()->role->name == "super_admin" ? User::all() : User::where('company_id', $request->user()->company_id)->get();
        return response()->json($users);
    }

    public function showUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    public function store(Request $request)
    {

        if (!$request->user()->role->name == "super_admin" || !$request->user()->role->name == "company_admin"){
            return response()->json(['error' => 'Unauthorized, only super admins and company admins can create accounts'], 403);
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role_id' => 'required|integer',
                'company_id' => 'required|integer',
            ]);

           /**
            *  check if role is super_admin and request user is not super admin
            */
            $super_admin_role = Role::where('name', 'super_admin')->first();
            
            if ($validated['role_id'] == $super_admin_role->id && $request->user()->role->name != "super_admin") {
                return response()->json(['message' => 'Unauthorized, only super admins can create super admin accounts'], 403);
            }
            

            // check if role is company_admin and request user is not super admin
            $company_admin_role = Role::where('name', 'company_admin')->first();
            if ($validated['role_id'] == $company_admin_role->id && $request->user()->role->name != "super_admin") {
                return response()->json(['message' => 'Unauthorized, only super admins can create company admin accounts'], 403);
            }

            // check if Company with company_id exists
            $company = Company::find($validated['company_id']);
            if (!$company) {
                return response()->json(['message' => 'Company not found'], 404);
            }
            
            //check if Role with role_id exists
            $role = Role::find($validated['role_id']);
            if (!$role) {
                return response()->json(['message' => 'Role not found'], 404);
            }
            
            // Hash the password before creating the user
            $validated['password'] = bcrypt($validated['password']);
            $user = User::create($validated);

            return response()->json($user, 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred while creating the user.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        if (!$request->user()->role->name == "super_admin" || !$request->user()->role->name == "company_admin"){
            return response()->json(['error' => 'Unauthorized, only super admins can create accounts'], 403);
        }
       try{
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|string|min:8',
                'role_id' => 'sometimes|integer',
                'company_id' => 'sometimes|integer',
            ]);

            // check if Company with company_id exists
            $company = Company::find($validated['company_id']);
            if (!$company) {
                return response()->json(['message' => 'Company not found'], 404);
            }
            
            //check if Role with role_id exists
            $role = Role::find($validated['role_id']);
            if (!$role) {
                return response()->json(['message' => 'Role not found'], 404);
            }

            if (isset($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            }

            $user->update($validated);

            return response()->json($user);
       }catch (Exception $e) {
            return response()->json(['message' => 'An error occurred while creating the user.'], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->role->name == "super_admin" || !$request->user()->role->name == "company_admin"){
            return response()->json(['error' => 'Unauthorized, only super admins can create accounts'], 403);
        }
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}