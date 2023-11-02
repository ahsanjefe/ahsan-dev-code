<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1); 
        $perPage = $request->input('rowsPerPage', 10);
    
        // Query the employees table with pagination
        $employees = Employee::orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);
    
        return response()->json([
            'employees' => $employees->items(),
            'employeesCount' => $employees->total(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming data
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'company_id' => 'required|exists:companies,id',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
            ]);
    
            // Create a new employee instance
            $employee = new Employee;
    
            // Assign values from the validated data
            $employee->first_name = $validatedData['first_name'];
            $employee->last_name = $validatedData['last_name'];
            $employee->company_id = $validatedData['company_id'];
            $employee->email = $validatedData['email'];
            $employee->phone = $validatedData['phone'];
    
            // Save the employee to the database
            $employee->save();
    
            // Return a JSON response for success
            return response()->json(['status' => 200, 'message' => 'Employee created successfully'], 200);
        } catch (Exception $e) {
            // Handle other exceptions and return a JSON response with a 500 status code
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        try {
            // Validate the incoming data
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'string|max:255',
                'company_id' => 'required|exists:companies,id',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
            ]);
        
            // Update the employee record with the validated data
            $employee->update($validatedData);
        
            return response()->json(['status' => 200, 'message' => 'Employee updated successfully'], 200);
        } catch (\Exception $e) {
            // Handle any other exceptions or errors
            return response()->json(['status' => 500, 'status' => 500, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            // Check if the company exists
            if (!$employee) {
                return response()->json(['status' => 404, 'message' => 'Employee not found'], 404);
            }
    
            // Delete the employee
            $employee->delete();
    
            return response()->json(['status' => 200, 'message' => 'Employee deleted successfully'], 200);
        } catch (\Exception $e) {
            // Handle any other exceptions or errors
            return response()->json(['status' => 500, 'error' => $e->getMessage()], 500);
        }
    }
}
