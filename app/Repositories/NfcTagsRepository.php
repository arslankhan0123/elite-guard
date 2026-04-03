<?php

namespace App\Repositories;

use App\Models\NfcTag;

class NfcTagsRepository
{
    // Get all NFC tags
    public function getAllNfcTags()
    {
        $nfcTags = NfcTag::orderBy('id', 'desc')->get();
        $data = [
            'status' => true,
            'message' => 'NFC tags retrieved successfully',
            'nfcTags' => $nfcTags
        ];
        return $data;
    }

    // Find a NFC tag by ID
    public function findNfcTagById($id)
    {
        return NfcTag::find($id);
    }

    // Create a new NFC tag
    public function createNfcTag(array $data)
    {
        return NfcTag::create($data);
    }

    // Update an existing NFC tag
    public function updateNfcTag($id, array $data)
    {
        $nfcTag = NfcTag::find($id);
        if ($nfcTag) {
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
}