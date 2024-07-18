<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\User;
use App\Models\Equipment;
use Illuminate\Validation\ValidationException;
use Exception;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the assignments for a company or super dmin.
     */
    public function index(Request $request)
    {
        if (!$request->user()->role->name == "super_user" || !$request->user()->role->name == "company_admin"){
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($request->user()->role->name == "company_admin") {
            // Assuming the user's company_id is directly accessible
            $user_company_id = $request->user()->company_id;

            // Find assignments for all equipment that belongs to the user's company
            $assignments = Assignment::whereHas('equipment', function ($query) use ($user_company_id) {
                $query->where('company_id', $user_company_id);
            })->get();

            if ($assignments->isEmpty()) {
                return response()->json(['message' => 'No assignments found'], 404);
            }
            return response()->json($assignments);
        }

        $assignments = Assignment::with(['equipment', 'user'])->get();
        return response()->json($assignments);
    }

  /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       try{
            $validated = $request->validate([
                'equipment_id' => 'required|exists:equipment,id',
                'user_id' => 'required|exists:users,id',
                'assigned_at' => 'required|date',
                'returned_at' => 'nullable|date',
                'status_on_return' => 'nullable|string',
            ]);

             // check if equipment with equipment_id exists
            $equipment = Equipment::find($validated['equipment_id']);
            if (!$equipment) {
                return response()->json(['error' => 'Assignment equipment does not exist, Create one first'], 404);
            }
            
            // check if user with user_id exists  and if the user belongs to the company of the equipment
            $user = User::find($validated['user_id']);
            if (!$user || $user->company_id !== $equipment->company_id) {
                return response()->json(['error' => 'User does not belong to the same company as the equipment'], 403);
            }

            $assignment = Assignment::create($validated());
            return response()->json($assignment, 201);
       }catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed: ' . $e->getMessage()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create assignment: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function showAssignment(string $id)
    {
        $assignment = Assignment::with(['equipment', 'user'])->find($id);
        if (!$assignment) {
            return response()->json(['error' => 'Assignment not found'], 404);
        }
        return response()->json($assignment);
    }


     /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       try{
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'user_id' => 'required|exists:users,id',
            'assigned_at' => 'required|date',
            'returned_at' => 'nullable|date',
            'status_on_return' => 'nullable|string',
        ]);

        // check if assignment with id exists
        $assignment = Assignment::find($id);
        if (!$assignment) {
            return response()->json(['error' => 'Assignment not found'], 404);
        }

        // update assignment
        $assignment->update($validated);
        return response()->json($assignment);
       }catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed: ' . $e->getMessage()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update assignment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $assignment = Assignment::find($id);
        if (!$assignment) {
            return response()->json(['error' => 'Assignment not found'], 404);
        }

        $assignment->delete();
        return response()->json(['message' => 'Assignment deleted successfully'], 204);
    }
}
