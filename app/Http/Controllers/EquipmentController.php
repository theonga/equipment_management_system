<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\Company;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index(Request $request)
    {
        // role back for sanctom
        if (!$request->user()->role->name == "super_user" || !$request->user()->role->name == "company_admin"){
            return response()->json(['error' => 'Unauthorized, only super admins and company admins allowed'], 403);
        }

        if($request->user()->role->name == "company_admin"){
            $equipment = Equipment::where('company_id', $request->user()->company_id)->get();
            if ($equipment->isEmpty()) {
                return response()->json(['message' => 'No equipment found'], 404);
            }
            return response()->json($equipment);
        }

        $equipment = Equipment::all();
        if ($equipment->isEmpty()) {
            return response()->json(['message' => 'No equipment found'], 404);
        }
        return response()->json($equipment);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'type' => 'required',
                'model' => 'required',
                'acquisition_year' => 'required',
                'acquisition_month' => 'required',
                'company_id' => 'required',
                'is_new' => 'required',
            ]);

            // check if company with company_id exists
            $company = Company::find($validated['company_id']);
            if (!$company) {
                return response()->json(['error' => 'Company not found'], 404);
            }

            $equipment = Equipment::create($validated);
            return response()->json(['success' => 'Equipment created successfully!', 'equipment' => $equipment], 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed: ' . $e->getMessage()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create equipment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function showEquipment(string $id)
    {
        try {
            $equipment = Equipment::findOrFail($id);
            return response()->json($equipment);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Equipment not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }
  
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        // check if user is a super_user or company_admin
        if (!$request->user()->role->name == "super_user" || !$request->user()->role->name == "company_admin"){
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // try updating the equipment
        try {
           $validated = $request->validate([
                'name' => 'required|string',
                'type' => 'required|string',
                'model' => 'required|string',
                'acquisition_year' => 'required|integer',
                'acquisition_month' => 'required|integer',
                'company_id' => 'required|integer|exists:companies,id',
                'is_new' => 'required|boolean',
            ]);
            $equipment = Equipment::findOrFail($id);
            $equipment->update($validated);
            return response()->json(['success' => 'Equipment updated successfully!', 'equipment' => $equipment]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed: ' . $e->getMessage()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update equipment: ' . $e->getMessage()], 500);
        }
    }

   /**
     * Remove the specified resource from storage.
    */
   public function destroy(Request $request, string $id)
    {
        // check if the user is a super_user or company_admin
        if (!$request->user()->role->name == "super_user" || !$request->user()->role->name == "company_admin"){
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // try deleting the equipment
        try {
            $equipment = Equipment::findOrFail($id);
            $equipment->delete();
            return response()->json(['success' => 'Equipment deleted successfully'], 204);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed: ' . $e->getMessage()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete equipment: ' . $e->getMessage()], 500);
        }
    }
}