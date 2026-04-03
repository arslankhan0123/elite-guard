<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Repositories\NfcTagsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NfcTagController extends Controller
{
    protected $nfcTagsRepo;

    // Inject the repository via constructor
    public function __construct(NfcTagsRepository $nfcTagsRepo)
    {
        $this->nfcTagsRepo = $nfcTagsRepo;
    }

    public function index()
    {
        // Use the repository to get all NFC tags
        $nfcTags = $this->nfcTagsRepo->getAllNfcTags();

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
            'uid'     => 'required|string|max:255|unique:nfc_tags,uid',
            'name'    => 'required|string|max:255',
            'status'  => 'required|boolean',
        ]);

        $this->nfcTagsRepo->createNfcTag($request);

        return redirect()->route('nfc.index')->with('success', 'NFC Tag created successfully.');
    }

    public function edit($nfc_id)
    {
        $nfcTag = $this->nfcTagsRepo->findNfcTagById($nfc_id);
        $sites = Site::with('company')->where('status', true)->get();
        return view('admin.nfc.edit', compact('nfcTag', 'sites'));
    }

    public function update(Request $request, $nfc_id)
    {
        $nfcTag = $this->nfcTagsRepo->findNfcTagById($nfc_id);

        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'uid'     => 'required|string|max:255|unique:nfc_tags,uid,' . $nfcTag->id,
            'name'    => 'required|string|max:255',
            'status'  => 'required|boolean',
        ]);

        $this->nfcTagsRepo->updateNfcTag($request, $nfc_id);

        return redirect()->route('nfc.index')->with('success', 'NFC Tag updated successfully.');
    }

    public function delete($nfc_id)
    {
        $this->nfcTagsRepo->deleteNfcTag($nfc_id);

        return redirect()->route('nfc.index')->with('success', 'NFC Tag deleted successfully.');
    }
}
