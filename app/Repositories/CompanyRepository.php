<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Support\Facades\Storage;

class CompanyRepository
{
    // Get all companies
    public function getAllCompanies()
    {
        $companies = Company::orderBy('id', 'desc')->get();
        $data = [
            'status' => true,
            'message' => 'Companies retrieved successfully',
            'companies' => $companies
        ];
        return $data;
    }

    // Find a company by ID
    public function findCompanyById($id)
    {
        return Company::find($id);
    }

    // Create a new company
    public function createCompany($request)
    {
        $data = $request->all(); // get all input as array

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('companies/logos', 'public');
            $data['logo'] = $logoPath;
        }

        return Company::create($data);
    }

    // Update an existing company
    public function updateCompany($request, $company_id)
    {
        $company = Company::find($company_id);
        if ($company) {
            $data = $request->all();
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($company->logo) {
                    Storage::disk('public')->delete($company->logo);
                }
                $logoPath = $request->file('logo')->store('companies/logos', 'public');
                $data['logo'] = $logoPath;
            }
            $company->update($data);
            return $company;
        }
        return null;
    }

    // Delete a company
    public function deleteCompany($id)
    {
        $company = Company::find($id);
        if ($company) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            return $company->delete();
        }
        return false;
    }
}