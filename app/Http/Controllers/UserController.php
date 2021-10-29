<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;

class UserController extends Controller
{
  public function index()
  {
    $user = User::select('*')->orderByDesc('id')->get();
    return response($user);
  }

  public function store(Request $request)
  {
    //Register user
    $inputs = $request->validate([
      'firstName' => 'required|string|max:255',
      'lastName' => 'required|string|max:255',
      'nic' => 'required|string|min:10|max:12|unique:users,nic',
      'mobile' => 'required|string|min:11',
      'dob' => 'required|date',
      'gender' => 'required|string',
      'address' => 'required|string|max:255',
      'email' => 'required|string|email:rfc,dns|unique:users,email',
      'password' => 'required|string|min:8|confirmed',
      'role' => 'required|string'
    ]);

    $user = User::create([
      'name' => $inputs['firstName'] . " " . $inputs['lastName'],
      'f_name' => $inputs['firstName'],
      'l_name' => $inputs['lastName'],
      'email' => $inputs['email'],
      'telephone' => $inputs['mobile'],
      'address' => $inputs['address'],
      'status' => 'Active',
      'dob' => $inputs['dob'],
      'nic' => $inputs['nic'],
      'gender' => $inputs['gender'],
      'password' => bcrypt($inputs['password']),
      'role' => $inputs['role']
    ]);

    $token = $user->createToken('myapptoken')->plainTextToken;

    $response = [
      "message" => "Registration Successfull!",
      "user" => $user,
      "token" => $token
    ];

    return response($response, 201);
  }

  public function login(Request $request)
  {
    $inputs = $request->validate([
      'email' => 'required|string',
      'password' => 'required|string',
    ]);

    //checking the email
    $user = User::where('email', $inputs['email'])->first();

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

    $token = $user->createToken('myapptoken')->plainTextToken;

    $response = [
      "message" => "success",
      'user' => $user,
      'token' => $token
    ];
    return response($response, 200);
  }
  public function logout()
  {
    auth()->user()->tokens()->delete();

    return [
      'message' => 'Logged out!'
    ];
  }

  public function sendRegisterEmail(Request $request)
  {
    $data = [
      "name" => $request['name'],
      "link" => env('FRONT_END') . "verify-email/" . Crypt::encryptString($request['id']),
      "email" => $request['email']
    ];

    Mail::send('email.register', $data, function ($message) use ($data) {
      $message->to($data['email'], $data['name'])->subject('Registration Successfull!');
    });

    return response('Email Sent', 200);
  }

  public function loggedUser(Request $request)
  {

    $realUser = auth()->user();
    $user = User::findOrFail($realUser->id);

    $user->feedbacks;
    $user->posts;
    $user->comments;
    $user->servicesRecieved;
    $user->informers;
    $user->service_requests;

    return response($user);
  }

  public function verifyEmail(Request $request)
  {
    $dec = Crypt::decryptString($request['id']);
    $user = User::findOrFail($dec);

    if ($user->email_verified_at) {
      return response('Already Verified');
    } else {
      $user->email_verified_at = $request['time'];
      $user->save();
      return response('Verification Successful!');
    }
  }

  public function getPosts($id)
  {
    $user = User::findOrFail($id);
    $user->posts;
    $user->comments;
    return $user;
  }

  public function userInfo($id)
  {
    $user = User::findOrFail($id);

    $user->feedbacks;
    $user->posts;
    $user->comments;
    $user->servicesRecieved;
    $user->informers;
    $user->service_requests;

    return $user;
  }

  public function updateUser(Request $request, $id)
  {

    $inputs = $request->validate([
      'f_name' => 'required|string|max:255',
      'l_name' => 'required|string|max:255',
      'nic' => 'required|string|min:10|max:12',
      'telephone' => 'required|string|min:11',
      'dob' => 'required|date',
      'gender' => 'required|string',
      'address' => 'required|string|max:255',
      'email' => 'required|string|email:rfc,dns',
      'role' => 'required|string'
    ]);

    $user = User::findOrFail($id);
    $user->role = $request->role;
    $user->status = $request->status;
    $user->name = $request->name;
    $user->f_name = $request->f_name;
    $user->l_name = $request->l_name;
    $user->description = $request->description;
    $user->email = $request->email;
    $user->telephone = $request->telephone;
    $user->address = $request->address;
    $user->dob = $request->dob;
    $user->gender = $request->gender;
    $user->nic = $request->nic;

    $user->update();

    return response($user);
  }

  public function destroy(User $user)
  {
    $this->logout();
    $user->delete();
    return $user;
  }

  public function updateProfile(Request $request)
  {
    $user = User::findOrFail($request['id']);

    $filepathname = "/" . explode("/", $user->profile_image)[3] . "/" . explode("/", $user->profile_image)[4];
    unlink(public_path($filepathname));

    $file = $request['file'];
    $fileName = $file->getClientOriginalName();
    $fileFileName = time() . "_" . $fileName;
    $filePath = asset('/images/' . $fileFileName);
    $file->move('images', $fileFileName);

    $user->profile_image = $filePath;
    $user->save();

    return response($user);
  }
}
