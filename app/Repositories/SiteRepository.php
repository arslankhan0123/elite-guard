<?php

namespace App\Repositories;

use App\Models\Site;

class SiteRepository
{
    // Get all companies
    public function getAllSites()
    {
        $sites = Site::with('nfcTags')->orderBy('id', 'desc')->get();
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
    public function createSite(array $data)
    {
        return Site::create($data);
    }

    // Update an existing site
    public function updateSite($id, array $data)
    {
        $site = Site::find($id);
        if ($site) {
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