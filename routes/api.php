<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyApiController;
use App\Http\Controllers\Api\DocumentsApiController;
use App\Http\Controllers\Api\ForgotPasswordApiController;
use App\Http\Controllers\Api\NumberApiController;
use App\Http\Controllers\Api\OfferLetterApiController;
use App\Http\Controllers\Api\PanicApiController;
use App\Http\Controllers\Api\OpenShiftApiController;
use App\Http\Controllers\Api\OrientationApiController;
use App\Http\Controllers\Api\PaySlipApiController;
use App\Http\Controllers\Api\PolicyApiController;
use App\Http\Controllers\Api\ScheduleApiController;
use App\Http\Controllers\Api\SettingsApiController;
use App\Http\Controllers\Api\ShiftApiController;
use App\Http\Controllers\Api\SiteApiController;
use App\Http\Controllers\Api\TagsApiController;
use App\Http\Controllers\Api\TaxDocsApiController;
use App\Http\Controllers\Api\TimeClockApiController;
use App\Http\Controllers\Api\TimeClockController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\CompanyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Forgot Password Flow
Route::post('forgot-password', [ForgotPasswordApiController::class, 'forgotPassword']);
Route::post('verify-otp', [ForgotPasswordApiController::class, 'verifyOtp']);
Route::post('reset-password', [ForgotPasswordApiController::class, 'resetPassword']);

Route::middleware('auth:api')->group(function () {

    Route::group(['prefix' => '/user'], function () {
        Route::get('/', [UserApiController::class, 'user']);
        Route::post('/update', [UserApiController::class, 'userUpdate']);
    });

    Route::group(['prefix' => '/company'], function () {
        Route::get('/', [CompanyApiController::class, 'index']);
    });

    Route::group(['prefix' => '/sites'], function () {
        Route::get('/', [SiteApiController::class, 'index']);
    });

    Route::group(['prefix' => '/numbers'], function () {
        Route::get('/', [NumberApiController::class, 'index']);
    });

    Route::group(['prefix' => '/schedules'], function () {
        Route::get('/', [ScheduleApiController::class, 'index']);
    });

    Route::group(['prefix' => '/shift'], function () {
        Route::get('/{id}', [ShiftApiController::class, 'index']);
    });

    Route::group(['prefix' => '/panic'], function () {
        Route::get('/', [PanicApiController::class, 'panicNotifications']);
    });

    Route::group(['prefix' => '/settings'], function () {
        Route::post('/password-update', [SettingsApiController::class, 'passwordUpdate']);
    });

    Route::group(['prefix' => '/documents'], function () {
        Route::post('/upload', [DocumentsApiController::class, 'documentsUpload']);
    });

    Route::group(['prefix' => '/offer-letter'], function () {
        Route::group(['prefix' => '/user'], function () {
            Route::get('/', [OfferLetterApiController::class, 'getUserOfferLetter']);
            Route::post('/accepted', [OfferLetterApiController::class, 'acceptedOfferLetter']);
        });
    });

    Route::group(['prefix' => '/pay-slips'], function () {
        Route::get('/user', [PaySlipApiController::class, 'getUserPaySlips']);
    });

    Route::group(['prefix' => '/tax-docs'], function () {
        Route::get('/', [TaxDocsApiController::class, 'getUserTaxDocs']);
        Route::post('/submit', [TaxDocsApiController::class, 'submitUserTaxDocs']);
    });

    Route::group(['prefix' => '/nfc-tags'], function () {
        Route::get('/', [TagsApiController::class, 'index']);
        Route::get('/{site_id}', [TagsApiController::class, 'siteTags']);
        Route::get('/checkSiteTags/{site_id}', [TagsApiController::class, 'checkSiteTags']);
        Route::get('/check/points', [TagsApiController::class, 'checkPoints']);
    });

    Route::group(['prefix' => '/time-clock'], function () {
        Route::get('/', [TimeClockApiController::class, 'index']);
        Route::post('/store', [TimeClockApiController::class, 'store']);
    });

    Route::group(['prefix' => '/policies'], function () {
        Route::get('/', [PolicyApiController::class, 'index']);
        Route::post('/signedPolicy', [PolicyApiController::class, 'signedPolicy']);
    });

    Route::group(['prefix' => '/orientations'], function () {
        Route::get('/', [OrientationApiController::class, 'index']);
        Route::post('/submit-quiz', [OrientationApiController::class, 'submitQuiz']);
        Route::post('/signedOrientation', [OrientationApiController::class, 'signedOrientation']);
    });

    Route::group(['prefix' => '/open-shifts'], function () {
        Route::get('/', [OpenShiftApiController::class, 'index']);
        Route::post('/{id}/claim', [OpenShiftApiController::class, 'claim']);
        Route::get('/my-claims', [OpenShiftApiController::class, 'myClaims']);
    });
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
