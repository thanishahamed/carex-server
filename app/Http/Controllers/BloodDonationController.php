<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\BloodDonation;

class BloodDonationController extends Controller
{
    public function create(Request $request)
    {
        $inputs = $request->validate([
            'bloodGroup' => 'required|string',
            'aditionalNumber' => 'required|string|min:11',
            'description' => 'required|string',
        ]);

        $user = auth()->user();
        $postBody = 'I like to donate blood for the needed people. Please contact me for more information.';

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Blood donation (' . $request['bloodGroup'] . ')',
            'body' => $postBody,
            'status' => 'pending',
            'category' => 'blood donation'
        ]);

        $blood_donation = BloodDonation::create([
            'post_id' => $post->id,
            'blood_group' => $request->bloodGroup,
            'description' => $request->description,
            'agreement_accepted' => 1,
            'status' => 'pending',
            'last_donated' => $request->lastDonated,
            'additional_contact' => $request->aditionalNumber,
            'hide_identity' => $request['hideIdentity'] == "true" ? 1 : 0,
            'additional_tests' => 1,
        ]);

        return response([
            'post' => $post,
            'blood_donation' => $blood_donation
        ], 201);
    }
}
