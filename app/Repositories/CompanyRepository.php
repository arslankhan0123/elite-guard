<?php

namespace App\Repositories;

use App\Models\Company;

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
    public function createCompany(array $data)
    {
        return Company::create($data);
    }

    // Update an existing company
    public function updateCompany($id, array $data)
    {
        $company = Company::find($id);
        if ($company) {
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
            return $company->delete();
        }
        return false;
    }
}