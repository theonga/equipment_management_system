<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$request->user()->role->name == "super_user" || !$request->user()->role->name == "company_admin"){
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if($request->user()->role->name == "company_admin"){
            $company = Company::where('id', $request->user()->company_id)->get();
            if ($company->isEmpty()) {
                return response()->json(['message' => 'No company found'], 404);
            }
            return response()->json($company);
        }

        $companies = Company::all();
        return response()->json($companies);
    }

   

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
            ]);

            $company = Company::create($validated);
            return response()->json(['success' => 'Company created successfully!', 'company' => $company], 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed: ' . $e->getMessage()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create company: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function showCompany(string $id)
    {
        try {
            $company = Company::find($id);
            if (!$company) {
                return response()->json(['error' => 'Company not found'], 404);
            }
            return response()->json($company);
        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required',
        ]);

        $company = Company::find($id);
        if (!$company) {
            return response()->json(['error' => 'Company not found'], 404);
        }
        $company->update($validated);
        return response()->json(['success' => 'Company updated successfully!', 'company' => $company]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = Company::find($id);
        if (!$company) {
            return response()->json(['error' => 'Company not found'], 404);
        }
        $company->delete();
        return response()->json(['success' => 'Company deleted successfully'], 204);
    }
}