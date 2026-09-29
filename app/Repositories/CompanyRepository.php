<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Support\Facades\Storage;

class CompanyRepository
{
    // Get all companies (Scoped by logged-in tenant/user)
    public function getAllCompanies()
    {
        $user = auth()->user();
        $query = Company::query();

        if ($user && $user->role !== 'MasterAdmin') {
            if ($user->tenant_id) {
                $query->where('tenant_id', $user->tenant_id);
            } else {
                $query->where('user_id', $user->id);
            }
        }

        $companies = $query->orderBy('id', 'desc')->get();

        return [
            'status'    => true,
            'message'   => 'Companies retrieved successfully',
            'companies' => $companies
        ];
    }

    // Find a company by ID
    public function findCompanyById($id)
    {
        $user = auth()->user();
        $query = Company::where('id', $id);

        if ($user && $user->role !== 'MasterAdmin') {
            if ($user->tenant_id) {
                $query->where('tenant_id', $user->tenant_id);
            } else {
                $query->where('user_id', $user->id);
            }
        }

        return $query->first();
    }

    // Create a new company
    public function createCompany($request)
    {
        $data = $request->all();
        $user = auth()->user();

        if ($user) {
            $data['user_id']   = $user->id;
            $data['tenant_id'] = $user->tenant_id;
        }

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('companies/logos', 'public');
            $data['logo'] = $logoPath;
        }

        return Company::create($data);
    }

    // Update an existing company
    public function updateCompany($request, $company_id)
    {
        $company = $this->findCompanyById($company_id);
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
        $company = $this->findCompanyById($id);
        if ($company) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            return $company->delete();
        }
        return false;
    }
}