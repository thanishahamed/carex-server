<?php

namespace App\Http\Controllers;

use App\Models\EducationFund;
use App\Models\Post;
use Illuminate\Http\Request;

class EducationFundController extends Controller
{
    public function create(Request $request)
    {
        $inputs = $request->validate([
            'aditionalNumber' => 'required|string|min:11',
            'description' => 'required|string',
        ]);

        $user = auth()->user();
        $postBody = "I like to contirbute fund for the needed students for their educational needs. \nPlease contact me for more information.";

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Funds for students',
            'body' => $postBody,
            'status' => 'pending verification',
            'category' => 'study fund'
        ]);

        $student_fund = EducationFund::create([
            'post_id' => $post->id,
            'blood_group' => $request->bloodGroup,
            'description' => $request->description,
            'agreement_accepted' => 1,
            'status' => 'pending verification',
            'last_donated' => $request->lastDonated,
            'additional_contact' => $request->aditionalNumber,
            'hide_identity' => $request['hideIdentity'] == "true" ? 1 : 0,
            'additional_tests' => 1,
        ]);

        return response([
            'post' => $post,
            'student_fund' => $student_fund
        ], 201);
    }
}
