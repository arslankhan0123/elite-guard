<?php

namespace App\Repositories;

use App\Models\TaxDocument;
use App\Models\TaxDocumentSubmission;
use Illuminate\Support\Facades\Auth;

class TaxDocsRepository
{
    public function getUserTaxDocs($request)
    {
        $user = Auth::user();

        // Get all tax_document_ids already submitted by this user
        $submittedIds = TaxDocumentSubmission::where('user_id', $user->id)
            ->pluck('tax_document_id')
            ->toArray();

        // Attach 'submitted' flag to each tax document
        $taxDocs = TaxDocument::latest()->get()->map(function ($doc) use ($submittedIds) {
            $doc->submitted = in_array($doc->id, $submittedIds);
            return $doc;
        });

        return [
            'status'  => true,
            'message' => 'Tax documents retrieved successfully',
            'taxDocs' => $taxDocs
        ];
    }

    public function submitUserTaxDocs($request)
    {
        $user = Auth::user();

        // Handle file upload
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $taxDocId = $request->input('tax_document_id');

            // Check if already submitted for this user + tax document
            $alreadyExists = TaxDocumentSubmission::where('user_id', $user->id)
                ->where('tax_document_id', $taxDocId)
                ->exists();

            if ($alreadyExists) {
                return [
                    'status'  => false,
                    'message' => 'You have already submitted this tax document.'
                ];
            }

            // Build filename: userId_taxDocId_timestamp.pdf
            $filename = $user->id . '_' . $taxDocId . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Store in public/documents/tax_docs/submissions/
            $relativeDir = 'documents/tax_docs/submissions';
            $destination = public_path($relativeDir);
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);

            // Save full URL path (same pattern as EmployeeController / PaySlip)
            $baseUrl = rtrim(config('app.url'), '/');
            $documentPath = "{$baseUrl}/{$relativeDir}/{$filename}";

            $record = TaxDocumentSubmission::create([
                'user_id' => $user->id,
                'tax_document_id' => $taxDocId,
                'document_path' => $documentPath,
            ]);

            return [
                'status' => true,
                'message' => 'Tax document uploaded successfully',
                'taxDoc' => $record
            ];
        }

        return [
            'status' => false,
            'message' => 'No document uploaded'
        ];
    }
}
