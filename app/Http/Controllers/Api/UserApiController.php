<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserApiController extends Controller
{
    use ApiResponser;
    protected $userRepo;

    // Inject the repository via constructor
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Get authenticated user details",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User details fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="User fetched successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="User retrieved successfully"),
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function user(Request $request)
    {
        $user = $this->userRepo->getUser();
        return $this->successResponse($user, 'User fetched successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/user/update",
     *     summary="Update user details",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="first_name", type="string", example="Arslan", description="Updates user table name combined with last_name"),
     *                 @OA\Property(property="last_name", type="string", example="Khan"),
     *                 @OA\Property(property="email", type="string", format="email", example="admin@admin.com", description="Updates system login email"),
     *                 @OA\Property(property="password", type="string", example="12345678", description="Updates login password"),
     *                 
     *                 @OA\Property(property="candidate[first_name]", type="string", example="Arslan"),
     *                 @OA\Property(property="candidate[last_name]", type="string", example="Khan"),
     *                 @OA\Property(property="candidate[dob]", type="string", format="date", example="1998-12-11"),
     *                 @OA\Property(property="candidate[sin]", type="string", example="SIN-123-456"),
     *                 @OA\Property(property="candidate[phone]", type="string", example="+923001234567"),
     *                 @OA\Property(property="candidate[email]", type="string", example="personal@email.com"),
     *                 @OA\Property(property="candidate[address]", type="string", example="123 Street"),
     *                 @OA\Property(property="candidate[city]", type="string", example="Lahore"),
     *                 @OA\Property(property="candidate[province]", type="string", example="Punjab"),
     *                 @OA\Property(property="candidate[postal_code]", type="string", example="54000"),
     *                 @OA\Property(property="candidate[emergency_contact_name]", type="string", example="John Doe"),
     *                 @OA\Property(property="candidate[emergency_contact_phone]", type="string", example="+923009876543"),
     *                 
     *                 @OA\Property(property="bank_detail[bank_name]", type="string", example="HBL"),
     *                 @OA\Property(property="bank_detail[institution_number]", type="string", example="012"),
     *                 @OA\Property(property="bank_detail[transit_number]", type="string", example="54321"),
     *                 @OA\Property(property="bank_detail[account_number]", type="string", example="11223344"),
     *                 @OA\Property(property="bank_detail[bank_address]", type="string", example="Main Blvd"),
     *                 @OA\Property(property="bank_detail[interac_email]", type="string", example="bank@email.com"),
     *                 @OA\Property(property="bank_detail[void_cheque_file]", type="string", format="binary", description="File upload (PDF/Image)"),
     *                 
     *                 @OA\Property(property="license_detail[security_license_number]", type="string", example="SL-123"),
     *                 @OA\Property(property="license_detail[security_license_expiry]", type="string", format="date", example="2027-12-31"),
     *                 @OA\Property(property="license_detail[security_license_file]", type="string", format="binary"),
     *                 @OA\Property(property="license_detail[drivers_license_number]", type="string", example="DL-456"),
     *                 @OA\Property(property="license_detail[drivers_license_expiry]", type="string", format="date", example="2026-06-15"),
     *                 @OA\Property(property="license_detail[drivers_license_file]", type="string", format="binary"),
     *                 @OA\Property(property="license_detail[work_eligibility_type_number]", type="string", example="WE-789"),
     *                 @OA\Property(property="license_detail[work_eligibility_expiry]", type="string", format="date"),
     *                 @OA\Property(property="license_detail[work_eligibility_file]", type="string", format="binary"),
     *                 @OA\Property(property="license_detail[criminal_record_check]", type="string", example="Valid"),
     *                 @OA\Property(property="license_detail[first_aid_training]", type="string", example="Level 1"),
     *                 @OA\Property(property="license_detail[other_certificates]", type="string"),
     *                 @OA\Property(property="license_detail[other_documents_file]", type="string", format="binary"),
     *                 
     *                 @OA\Property(property="availability[availability_date]", type="string", format="date"),
     *                 @OA\Property(property="availability[willing_hours]", type="string", example="40"),
     *                 @OA\Property(property="availability[unable_hours]", type="string"),
     *                 @OA\Property(property="availability[unable_days]", type="string"),
     *                 
     *                 @OA\Property(property="office_detail[employment_type]", type="string", example="Full Time"),
     *                 @OA\Property(property="office_detail[start_date]", type="string", format="date"),
     *                 @OA\Property(property="office_detail[job_position]", type="string"),
     *                 @OA\Property(property="office_detail[wage]", type="string"),
     *                 @OA\Property(property="office_detail[other_notes]", type="string"),
     *                 @OA\Property(property="office_detail[hiring_manager_name]", type="string"),
     *                 @OA\Property(property="office_detail[hiring_manager_signature]", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="User updated successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Validation Error or Password Mismatch",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Old password is incorrect")
     *         )
     *     )
     * )
     */
    public function userUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|nullable|string|max:255',
            'first_name' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|nullable|email|unique:users,email,' . Auth::id(),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 401);
        }

        $user = $this->userRepo->userUpdate($request);
        return $this->successResponse($user, 'User updated successfully.');
    }
}
