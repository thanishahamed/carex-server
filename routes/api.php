<?php

use App\Http\Controllers\BloodDonationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use Cartalyst\Stripe\Stripe;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostImageController;
use App\Http\Controllers\OrganDonationController;
use App\Http\Controllers\InformerController;
use App\Http\Controllers\PersonalAccessTokenController;
use App\Http\Controllers\PostServiceRequestByPeopleController;
use App\Http\Controllers\FundController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EducationFundController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\ServiceRequestController;
use App\Models\BloodDonation;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//attach this fund route with a controller later
Route::post('/fund', function (Request $request) {

    try {
        $stripe = Stripe::charges()->create([
            'amount' => $request['amount'],
            'currency' => 'LKR',
            'source' => $request->id,
            'receipt_email' => 'muktharthanish@gmail.com',
            'description' => 'Test Payment'
        ]);
        return response($stripe, 200);
    } catch (Exception $e) {
        return response()->json($e->getMessage());
    }

    return response($stripe, 200);
});



//Public Routes
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'store']);
Route::post('/verify-email', [UserController::class, 'verifyEmail']);
Route::post('/get-all-services', [PostController::class, 'getAllPosts']);
Route::post('/post/{id}/', [PostController::class, 'getPostInfo']);
Route::post('/login-informer', [InformerController::class, 'login']);
Route::post('/get-donor-for-informer', [InformerController::class, 'getDonorDetails']);
Route::post('/update-donor-by-informer', [OrganDonationController::class, 'updateAvailability']);
Route::post('/verify-donation-by-officer/{id}/{userId}', [OrganDonationController::class, 'checkPostRecord']);
Route::post('/approveOrganDonation', [OrganDonationController::class, 'approveOrganDonation']);
Route::post('/update-fund', [FundController::class, 'updateFunder']);
Route::post('/view', [ViewController::class, 'viewed']);

//delete these testing routes
Route::get('/user/{id}/posts', [UserController::class, 'getPosts']);
Route::get('/post/{id}/', [PostController::class, 'getPostInfo']);
Route::get('/image/{id}/post', [PostImageController::class, 'getPostOfTheImage']);
Route::get('/organ-donation/{id}', [OrganDonationController::class, 'organDonationInfo']);

//Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/allusers', [UserController::class, 'index']);
    Route::get('/user/{id}', [UserController::class, 'userInfo']);
    Route::post('/user/{id}', [UserController::class, 'updateUser']);
    Route::post('/verified-user', [UserController::class, 'loggedUser']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/send-email-verification', [UserController::class, 'sendRegisterEmail']);
    Route::delete('/destroy-user/{user}', [UserController::class, 'destroy']);

    Route::post('/loggedusers', [PersonalAccessTokenController::class, 'getTokens']);
    Route::post('/organ-agreement-form', [OrganDonationController::class, 'getAgreementForm']);
    Route::post('/add-organ-donation', [OrganDonationController::class, 'create']);
    Route::post('/donate-organ', [OrganDonationController::class, 'donateOrgan']);

    Route::post('/add-blood-donation', [BloodDonationController::class, 'create']);

    Route::post('/add-body-donation', [OrganDonationController::class, 'createBodyDonation']);

    Route::post('/add-student-fund', [EducationFundController::class, 'create']);

    Route::post('/add-new-service-request', [ServiceRequestController::class, 'create']);

    Route::post('/add-scholarship', [ScholarshipController::class, 'create']);

    Route::post('/register-informer', [InformerController::class, 'create']);
    Route::delete('/destroy-informer/{informer}', [InformerController::class, 'destroy']);

    Route::post('/create/post', [PostController::class, 'createPost']);
    Route::post('/close/post', [PostController::class, 'closePost']);
    Route::post('/approve/post', [PostController::class, 'approvePost']);
    Route::delete('/destroy-post/{post}', [PostController::class, 'destroy']);

    Route::post('/create/request', [PostServiceRequestByPeopleController::class, 'createRequest']);
    Route::post('/findRequestInfo', [PostServiceRequestByPeopleController::class, 'findRequestInfo']);

    Route::post('/get-all-funds', [FundController::class, 'getAllFunds']);
    Route::post('/get-all-funds-with-posts', [FundController::class, 'getAllFundsWithPosts']);

    Route::post('/comment', [CommentController::class, 'create']);

    Route::post('/like', [LikeController::class, 'like']);

    Route::post('/shared', [ShareController::class, 'share']);
});
