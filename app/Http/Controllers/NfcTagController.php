<?php

namespace App\Http\Controllers;

use App\Models\NfcTag;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NfcTagController extends Controller
{
    public function index()
    {
        $nfcTags = NfcTag::with('site.company')->orderBy('id', 'desc')->get();
        return view('admin.nfc.index', compact('nfcTags'));
    }

    public function create()
    {
        $sites = Site::with('company')->where('status', true)->get();
        // Generate a random unique UID as a suggestion
        $suggestedUid = 'NFC-' . strtoupper(Str::random(10));
        return view('admin.nfc.create', compact('sites', 'suggestedUid'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'uid' => 'required|string|max:255|unique:nfc_tags,uid',
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        NfcTag::create($request->all());

        return redirect()->route('nfc.index')->with('success', 'NFC Tag created successfully.');
    }

    public function edit($nfc_id)
    {
        $nfcTag = NfcTag::findOrFail($nfc_id);
        $sites = Site::with('company')->where('status', true)->get();
        return view('admin.nfc.edit', compact('nfcTag', 'sites'));
    }

    public function update(Request $request, $nfc_id)
    {
        $nfcTag = NfcTag::findOrFail($nfc_id);

        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'uid' => 'required|string|max:255|unique:nfc_tags,uid,' . $nfcTag->id,
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $nfcTag->update($request->all());

        return redirect()->route('nfc.index')->with('success', 'NFC Tag updated successfully.');
    }

    public function delete($nfc_id)
    {
        $nfcTag = NfcTag::findOrFail($nfc_id);
        $nfcTag->delete();

        return redirect()->route('nfc.index')->with('success', 'NFC Tag deleted successfully.');
    }
}
