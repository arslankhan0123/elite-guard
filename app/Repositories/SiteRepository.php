<?php

namespace App\Repositories;

use App\Models\Site;

class SiteRepository
{
    // Get all sites
    public function getAllSites()
    {
        $sites = Site::with('company')->orderBy('id', 'desc')->get();
        $data = [
            'status' => true,
            'message' => 'Sites retrieved successfully',
            'sites' => $sites
        ];
        return $data;
    }

    // Find a site by ID
    public function findSiteById($id)
    {
        return Site::find($id);
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
        $site = Site::find($site_id);
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
        $site = Site::find($id);
        if ($site) {
            return $site->delete();
        }
        return false;
    }
}