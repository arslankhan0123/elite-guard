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
     *         @OA\JsonContent(
     *             required={"name", "email"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="old_password", type="string", example="old_password123"),
     *             @OA\Property(property="password", type="string", example="new_password123"),
     *             @OA\Property(property="confirm_password", type="string", example="new_password123")
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
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 401);
        }

        $user = Auth::user();

        // password logic manually
        if ($request->filled('password')) {

            // old password required
            if (!$request->filled('old_password')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Old password is required'
                ], 401);
            }

            // check old password with real_password column
            if ($request->old_password !== $user->real_password) {
                return response()->json([
                    'status' => false,
                    'message' => 'Old password is incorrect'
                ], 401);
            }

            // confirm password check
            if ($request->password !== $request->confirm_password) {
                return response()->json([
                    'status' => false,
                    'message' => 'Password and confirm password do not match'
                ], 401);
            }
        }

        $user = $this->userRepo->userUpdate($request);
        return $this->successResponse($user, 'User updated successfully.');
    }
}
