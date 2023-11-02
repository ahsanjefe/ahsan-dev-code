<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Company;
use App\Events\CompanyAdded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Notifications\CompanyAddedNotification;


class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1); 
        $perPage = $request->input('rowsPerPage', 10);
    
        // Query the companies table with pagination
        $companies = Company::orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);
    
        return response()->json([
            'companies' => $companies->items(),
            'companiesCount' => $companies->total(),
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
            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'email|max:255',
                'website' => 'string|max:255',
                'logo' => 'nullable',
            ]);

            // Handle the logo file upload
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $path = $file[0]->store('public/company_logos');
                $validatedData['logo'] = str_replace('public/', '', $path);
            }
    
            // Create a new Company instance and save it
            $company = new Company;
            $company->name = $validatedData['name'];
            $company->email = $validatedData['email'];
            if($validatedData['logo']) {
                $company->logo = $validatedData['logo'];
            }
            $company->website = $validatedData['website'];
            $company->save();

            // Notify the user by sending the email
            $company->notify(new CompanyAddedNotification($company));
    
            return response()->json(['status' => 200, 'message' => 'Company created successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        try {
            // Check if the company exists
            if (!$company) {
                return response()->json(['status' => 404, 'message' => 'Company not found'], 404);
            }

            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|string|max:255',
                'logo' => 'nullable',
            ]);

            // Handle the logo file upload
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $path = $file[0]->store('public/company_logos');
                $validatedData['logo'] = str_replace('public/', '', $path);
            }
    
            // Update the company's attributes with the validated data
            $company->update($validatedData);
    
            return response()->json(['status' => 200, 'message' => 'Company updated successfully'], 200);
        } catch (\Exception $e) {
            // Handle any other exceptions or errors
            return response()->json(['status' => 500, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        try {
            // Check if the company exists
            if (!$company) {
                return response()->json(['status' => 404, 'message' => 'Company not found'], 404);
            }
    
            // Delete the company
            $company->delete();
    
            return response()->json(['status' => 200, 'message' => 'Company deleted successfully'], 200);
        } catch (\Exception $e) {
            // Handle any other exceptions or errors
            return response()->json(['status' => 500, 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * get a list of companies
     */
    public function companiesList(Request $request)
    {
        try {
            // get list of company with id and name
            $companiesList = Company::select('id', 'name')->get()->toArray();
    
            return response()->json(['status' => 200, 'companiesList' => $companiesList], 200);
        } catch (\Exception $e) {
            // Handle any other exceptions or errors
            return response()->json(['status' => 500, 'error' => $e->getMessage()], 500);
        }
    }
}
