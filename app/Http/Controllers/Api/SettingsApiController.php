<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingsApiController extends Controller
{
    use ApiResponser;

     /**
     * @OA\Post(
     *     path="/api/settings/password-update",
     *     summary="Change user password from settings",
     *     tags={"Settings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="old_password", type="string", example="12345678"),
     *             @OA\Property(property="new_password", type="string", example="newpassword123"),
     *             @OA\Property(property="confirm_password", type="string", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Password updated successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Password updated successfully."),
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Old password mismatch",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Error"),
     *             @OA\Property(property="message", type="string", example="Old password does not match.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Error"),
     *             @OA\Property(property="message", type="string", example="The confirm password and new password must match.")
     *         )
     *     )
     * )
     */
    public function passwordUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), $validator->errors(), 422);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if old password matches real_password stored in DB
        if ($user->real_password !== $request->old_password) {
            return $this->errorResponse('Old password does not match.', null, 401);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
            'real_password' => $request->new_password,
        ]);

        $data = [
            'status' => true,
            'message' => 'Password updated successfully.',
            'user' => $user
        ];

        return $this->successResponse($data, 'Password updated successfully.');
    }
}
