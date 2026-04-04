<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordOtpMail;
use App\Models\Otp;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ForgotPasswordApiController extends Controller
{
    use ApiResponser;

    /**
     * @OA\Post(
     *     path="/api/forgot-password",
     *     summary="Send OTP to email for password reset",
     *     tags={"Forgot Password"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="OTP sent to email."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="OTP sent to email.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Account doesn’t exist with this email")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="object")
     *         )
     *     )
     * )
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => "Account doesn’t exist with this email"
            ], 404);
        }

        $otp = mt_rand(10000, 99999);
        $token = Str::random(60);

        Otp::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otp, 'token' => $token]
        );

        Mail::to($request->email)->send(new ForgotPasswordOtpMail($otp, $user->name));
        $data = [
            'status' => true,
            'message' => 'OTP sent to email.',
        ];
        return $this->successResponse($data, 'OTP sent to email.');
    }

    /**
     * @OA\Post(
     *     path="/api/verify-otp",
     *     summary="Verify the OTP",
     *     tags={"Forgot Password"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "otp"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="otp", type="string", example="12345")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="OTP verified successfully!"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="OTP verified successfully!")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or expired OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid OTP")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="object")
     *         )
     *     )
     * )
     */
    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'otp' => 'required',
                'email' => 'required|email|exists:users,email',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            // $otp = implode('', $request->otp);
            $otpRecord = Otp::where('email', $request->email)->where('otp', $request->otp)->first();

            if (!$otpRecord) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP'
                ], 400);
            }

            if (Carbon::parse($otpRecord->created_at)->addMinutes(10)->isPast()) {
                $otpRecord->delete();
                return response()->json([
                    'status' => false,
                    'message' => 'OTP has expired. Please request a new one.'
                ], 400);
            }

            $data = [
                'status' => true,
                'message' => 'OTP verified successfully!',
            ];
            return $this->successResponse($data, 'OTP verified successfully!');
        } catch (HttpException $exception) {
            return $this->failure(
                $exception->getMessage(),
                $exception->getStatusCode()
            );
        }
    }

    /**
     * @OA\Post(
     *     path="/api/reset-password",
     *     summary="Reset password with email confirmation and OTP",
     *     tags={"Forgot Password"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password", "confirm_password", "otp"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
     *             @OA\Property(property="confirm_password", type="string", format="password", example="newpassword123"),
     *             @OA\Property(property="otp", type="string", example="12345")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Password reset successfully!"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Password reset successfully!"),
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="OTP mismatch",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Email not matched with given OTP")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="object")
     *         )
     *     )
     * )
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6',
                'confirm_password' => 'required|same:password',
                'otp' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $otp = $request->otp;
            // $otp = implode('', $request->otp);
            $otpRecord = Otp::where('email', $request->email)->where('otp', $otp)->first();
            if (!$otpRecord) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email not matched with given OTP'
                ], 400);
            }

            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->real_password = $request->password;
            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
            }
            $user->save();
            $otpRecord->delete();

            $data = [
                'status' => true,
                'message' => 'Password reset successfully!',
                'user' => $user,
            ];
            return $this->successResponse($data, 'Password reset successfully!');
        } catch (HttpException $exception) {
            return $this->failure(
                $exception->getMessage(),
                $exception->getStatusCode()
            );
        }
    }
}
