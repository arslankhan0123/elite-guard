<?php

namespace App\Repositories;

use App\Models\NfcTag;
use Illuminate\Support\Facades\DB;

class NfcTagsRepository
{
    // Get all NFC tags
    public function getAllNfcTags()
    {
        $nfcTags = NfcTag::with('site.company')->orderBy('id', 'desc')->get();
        $data = [
            'status' => true,
            'message' => 'NFC Tags retrieved successfully',
            'nfcTags' => $nfcTags
        ];
        return $data;
    }

    // Find an NFC tag by ID
    public function findNfcTagById($id)
    {
        return NfcTag::find($id);
    }

    // Create a new NFC tag
    public function createNfcTag($request)
    {
        $data = $request->all();
        return NfcTag::create($data);
    }

    // Update an existing NFC tag
    public function updateNfcTag($request, $nfc_id)
    {
        $nfcTag = NfcTag::find($nfc_id);
        if ($nfcTag) {
            $data = $request->all();
            $nfcTag->update($data);
            return $nfcTag;
        }
        return null;
    }

    // Delete an NFC tag
    public function deleteNfcTag($id)
    {
        $nfcTag = NfcTag::find($id);
        if ($nfcTag) {
            return $nfcTag->delete();
        }
        return false;
    }

    public function getNfcTagsBySiteId($site_id)
    {
        $nfcTags = NfcTag::where('site_id', $site_id)->with('site.company')->orderBy('id', 'desc')->get();
        $data = [
            'status' => true,
            'message' => 'NFC Tags for the specified site retrieved successfully',
            'nfcTags' => $nfcTags
        ];
        return $data;
    }

    public function getCheckSiteTags($site_id)
    {
        $nfcTags = NfcTag::where('site_id', $site_id)
            ->with('site.company')
            ->withExists(['timeClocks as time_clock' => function ($q) {
                $q->select(DB::raw(1));
            }])
            ->orderBy('id', 'desc')
            ->get();

        return [
            'status' => true,
            'message' => 'NFC Tags for the specified site retrieved successfully',
            'nfcTags' => $nfcTags
        ];
    }

    public function getCheckPoints()
    {
        $nfcTags = NfcTag::with('site.company')
            ->withExists(['timeClocks as time_clock' => function ($q) {
                $q->select(DB::raw(1));
            }])
            ->orderBy('id', 'desc')
            ->get();

        return [
            'status' => true,
            'message' => 'NFC Tags Check Points retrieved successfully.',
            'nfcTags' => $nfcTags
        ];
    }
}
