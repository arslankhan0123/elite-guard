<?php

namespace App\Repositories;

use App\Models\Site;
use App\Models\NfcTag;

class SiteRepository
{
    // Get all sites (Scoped by logged-in tenant/user)
    public function getAllSites()
    {
        $user = auth()->user();
        $query = Site::with('company');

        if ($user && $user->role !== 'MasterAdmin') {
            if ($user->tenant_id) {
                $query->whereHas('company', fn ($q) => $q->where('tenant_id', $user->tenant_id));
            } else {
                $query->whereHas('company', fn ($q) => $q->where('user_id', $user->id));
            }
        }

        $sites = $query->orderBy('id', 'desc')->get();

        return [
            'status'  => true,
            'message' => 'Sites retrieved successfully',
            'sites'   => $sites
        ];
    }

    public function getAllSitesAndNfcTags()
    {
        $user = auth()->user();
        $siteQuery = Site::with('company', 'nfcTags');
        $nfcQuery  = NfcTag::with('site');

        if ($user && $user->role !== 'MasterAdmin') {
            if ($user->tenant_id) {
                $siteQuery->whereHas('company', fn ($q) => $q->where('tenant_id', $user->tenant_id));
                $nfcQuery->whereHas('site.company', fn ($q) => $q->where('tenant_id', $user->tenant_id));
            } else {
                $siteQuery->whereHas('company', fn ($q) => $q->where('user_id', $user->id));
                $nfcQuery->whereHas('site.company', fn ($q) => $q->where('user_id', $user->id));
            }
        }

        $sites   = $siteQuery->orderBy('id', 'desc')->get();
        $nfcTags = $nfcQuery->orderBy('id', 'desc')->get();

        return [
            'status'  => true,
            'message' => 'Sites and NfcTags retrieved successfully',
            'sites'   => $sites,
            'nfcTags' => $nfcTags
        ];
    }

    // Find a site by ID (Returns null if site does not belong to logged-in tenant)
    public function findSiteById($id)
    {
        $user = auth()->user();
        $query = Site::where('id', $id)->with('company');

        if ($user && $user->role !== 'MasterAdmin') {
            if ($user->tenant_id) {
                $query->whereHas('company', fn ($q) => $q->where('tenant_id', $user->tenant_id));
            } else {
                $query->whereHas('company', fn ($q) => $q->where('user_id', $user->id));
            }
        }

        return $query->first();
    }

    // Create a new site
    public function createSite($request)
    {
        $data = $request->all();
        return Site::create($data);
    }

    // Update an existing site
    public function updateSite($request, $site_id)
    {
        $site = $this->findSiteById($site_id);
        if ($site) {
            $data = $request->all();
            $site->update($data);
            return $site;
        }
        return null;
    }

    // Delete a site
    public function deleteSite($id)
    {
        $site = $this->findSiteById($id);
        if ($site) {
            return $site->delete();
        }
        return false;
    }

    // Get sites assigned to a specific user
    public function getUserAssignedSites($user)
    {
        $sites = $user->sites()->with(['company', 'nfcTags'])->orderBy('name')->get();
        
        return [
            'status' => true,
            'message' => 'Assigned sites retrieved successfully',
            'sites' => $sites
        ];
    }
}