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

    /**
     * @OA\Get(
     *     path="/api/tax-docs",
     *     summary="Get all tax documents with submission status for the authenticated user",
     *     tags={"Tax Documents"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Tax documents fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Tax documents fetched successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Tax documents retrieved successfully"),
     *                 @OA\Property(property="taxDocs", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="type", type="string", example="Td1-Fill"),
     *                     @OA\Property(property="file_path", type="string", example="http://localhost/documents/tax_docs/form.pdf"),
     *                     @OA\Property(property="submitted", type="boolean", example=true, description="True if the authenticated user has already submitted this document"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function getUserTaxDocs(Request $request)
    {
        $result = $this->taxDocsRepo->getUserTaxDocs($request);
        return $this->successResponse($result, 'Tax documents fetched successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/tax-docs/submit",
     *     summary="Submit a tax document for the authenticated user",
     *     tags={"Tax Documents"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"tax_document_id", "document"},
     *                 @OA\Property(property="tax_document_id", type="integer", example=1, description="ID of the tax document type from tax_documents table"),
     *                 @OA\Property(property="document", type="string", format="binary", description="PDF file to upload (max 2MB)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tax document submitted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Tax documents submitted successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Tax document uploaded successfully"),
     *                 @OA\Property(property="taxDoc", type="object",
     *                     @OA\Property(property="id", type="integer", example=3),
     *                     @OA\Property(property="user_id", type="integer", example=5),
     *                     @OA\Property(property="tax_document_id", type="integer", example=1),
     *                     @OA\Property(property="document_path", type="string", example="http://localhost/documents/tax_docs/submissions/5_1_1745200000.pdf"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Validation error or already submitted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You have already submitted this tax document.")
     *         )
     *     )
     * )
     */
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
