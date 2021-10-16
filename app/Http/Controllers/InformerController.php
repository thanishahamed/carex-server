<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Informer;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Hash;

class InformerController extends Controller
{
  public function create(Request $request)
  {
    $inputs = $request->validate([
      'fullName' => 'required|string|max:255',
      'address' => 'required|string|max:255',
      'nic' => 'required|string|min:10|max:12',
      'telephone' => 'required|string|min:11',
      'email' => 'required|string|email:rfc,dns|unique:informers,email',
      'gender' => 'required|string',
      'password' => 'required|string|min:8|confirmed',
      'dob' => 'required|date'
    ]);

    $informer = Informer::create([
      'user_id' => auth()->user()->id,
      'full_name' => $inputs['fullName'],
      'email' => $inputs['email'],
      'telephone' => $inputs['telephone'],
      'address' => $inputs['address'],
      'status' => 'Active',
      'dob' => $inputs['dob'],
      'nic' => $inputs['nic'],
      'gender' => $inputs['gender'],
      'password' => bcrypt($inputs['password']),
    ]);
    return response($informer);
  }

  public function login(Request $request)
  {
    $inputs = $request->validate([
      'email' => 'required|string',
      'password' => 'required|string',
    ]);

    //checking the email
    $user = Informer::where('email', $inputs['email'])->first();

    if (!$user || !Hash::check($inputs['password'], $user->password)) {
      return response([
        'message' => 'Login failed! Please use your correct email and password'
      ], 401);
    }

    if ($user->status != 'Active') {
      return response([
        'message' => 'Sorry ' . $user->name . '! Your account has been suspended'
      ], 203);
    }

    $user->remember_token = Str::random(40);
    $user->update();

    $response = [
      "message" => "success",
      'user' => $user,
      'token' => $user->remember_token
    ];
    return response($response, 200);
  }

  public function getDonorDetails(Request $request)
  {
    $token = $request['token'];

    $informer = Informer::select('*')
      ->where('remember_token', $token)
      ->where('id', $request['id'])
      ->get();

    if (!(count($informer) > 0)) {
      return response("unathorized informer", 405);
    }

    $user = User::FindOrFail($informer[0]->user_id);

    $postOrganDonation = Post::select("*")
      ->where('category', 'organ donation')->orWhere('category', 'body donation')
      ->where('user_id', $informer[0]->user_id)
      ->get();

    // $postBodyDonation = Post::select("*")
    //     ->where('category', 'body donation')
    //     ->where('user_id', $informer[0]->user_id)
    //     ->get();

    // if(count($postOrganDonation) > 0) {
    $postOrganDonation[0]->organ_donation;
    // }

    // if(count($postBodyDonation) > 0) {
    //     $postBodyDonation[0]->organ_donation;
    // }

    return response([
      'organDonation' => $postOrganDonation,
      // 'bodyDonation' => $postBodyDonation,
      'user' => $user
    ], 200);
  }
}
