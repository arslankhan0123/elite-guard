<?php

namespace App\Http\Controllers;

use App\Models\TaxDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TaxDocumentController extends Controller
{
    public function index()
    {
        $taxDocs = TaxDocument::latest()->get();
        $usedTypes = $taxDocs->pluck('type')->toArray();
        $availableTypes = array_diff(TaxDocument::TYPES, $usedTypes);

        return view('admin.tax-docs.index', compact('taxDocs', 'availableTypes'));
    }

    public function create()
    {
        $usedTypes = TaxDocument::pluck('type')->toArray();
        $availableTypes = array_diff(TaxDocument::TYPES, $usedTypes);

        if (empty($availableTypes)) {
            return redirect()->route('tax-docs.index')
                ->with('warning', 'All document types have already been uploaded. Please edit an existing one.');
        }

        return view('admin.tax-docs.create', compact('availableTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'     => 'required|in:Td1-Fill,Td1-Ab,Td2-Ab|unique:tax_documents,type',
            'document' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ], [
            'type.unique' => 'A document for type "' . $request->type . '" has already been uploaded.',
        ]);

        $file = $request->file('document');
        $filename = $this->generateFilename($file);
        $dir = public_path('documents/tax_docs');

        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0777, true);
        }

        $file->move($dir, $filename);
        $filePath = rtrim(config('app.url'), '/') . '/documents/tax_docs/' . $filename;

        TaxDocument::create([
            'type'      => $request->type,
            'file_path' => $filePath,
        ]);

        return redirect()->route('tax-docs.index')->with('success', 'Tax document uploaded successfully!');
    }

    public function edit($id)
    {
        $taxDoc = TaxDocument::findOrFail($id);
        return view('admin.tax-docs.edit', compact('taxDoc'));
    }

    public function update(Request $request, $id)
    {
        $taxDoc = TaxDocument::findOrFail($id);

        $request->validate([
            'type'     => 'required|in:Td1-Fill,Td1-Ab,Td2-Ab',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // If type changed, make sure new type is not already taken
        if ($request->type !== $taxDoc->type) {
            $typeExists = TaxDocument::where('type', $request->type)->exists();
            if ($typeExists) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['type' => 'A document for type "' . $request->type . '" has already been uploaded.']);
            }
        }

        if ($request->hasFile('document')) {
            // Delete old file
            $baseUrl = rtrim(config('app.url'), '/');
            $relativePath = ltrim(str_replace($baseUrl, '', $taxDoc->file_path), '/');
            $fullPath = public_path($relativePath);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }

            $file = $request->file('document');
            $filename = $this->generateFilename($file);
            $dir = public_path('documents/tax_docs');

            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0777, true);
            }

            $file->move($dir, $filename);
            $taxDoc->file_path = $baseUrl . '/documents/tax_docs/' . $filename;
        }

        $taxDoc->type = $request->type;
        $taxDoc->save();

        return redirect()->route('tax-docs.index')->with('success', 'Tax document updated successfully!');
    }

    public function delete($id)
    {
        $taxDoc = TaxDocument::findOrFail($id);

        $baseUrl = rtrim(config('app.url'), '/');
        $relativePath = ltrim(str_replace($baseUrl, '', $taxDoc->file_path), '/');
        $fullPath = public_path($relativePath);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }

        $taxDoc->delete();

        return redirect()->route('tax-docs.index')->with('success', 'Tax document deleted successfully!');
    }

    private function generateFilename($file): string
    {
        return time() . '_' . bin2hex(random_bytes(10)) . '.' . $file->getClientOriginalExtension();
    }
}
