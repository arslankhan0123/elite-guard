<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\PaySlipRepository;
use App\Repositories\TaxDocsRepository;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Validator;

class TaxDocsApiController extends Controller
{
    use ApiResponser;

    protected $taxDocsRepo;

    public function __construct(TaxDocsRepository $taxDocsRepo)
    {
        $this->taxDocsRepo = $taxDocsRepo;
    }

    public function getUserTaxDocs(Request $request)
    {
        $result = $this->taxDocsRepo->getUserTaxDocs($request);
        return $this->successResponse($result, 'Tax documents fetched successfully.');
    }

    public function submitUserTaxDocs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tax_document_id' => 'required|exists:tax_documents,id',
            'document' => 'required|file|mimes:pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 401);
        }

        $result = $this->taxDocsRepo->submitUserTaxDocs($request);
        return $this->successResponse($result, 'Tax documents submitted successfully.');
    }
}
