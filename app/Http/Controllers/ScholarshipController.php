<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    public function create(Request $request)
    {

        $inputs = $request->validate([
            'target' => 'required|string',
            'no_of_scholarships' => 'required|integer',
            'worth_of_scholarship' => 'required|integer',
            'description' => 'required|string',
            'aditionalNumber' => 'required|string',
        ]);

        $user = auth()->user();
        $postBody = "There is a scholarship available. \nPlease contact me for more information.\n";
        $postBody .= $inputs['description'];

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Scholarship For Needies!',
            'body' => $postBody,
            'status' => 'pending verification',
            'category' => 'scholarship'
        ]);

        $scholarship = Scholarship::create([
            'post_id' => $post->id,
            'target' => $inputs['target'],
            'subject' => 'none',
            'no_of_scholarships' => $inputs['no_of_scholarships'],
            'worth_of_scholarship' => $inputs['worth_of_scholarship'],
            'description' => $inputs['description'],
            'agreement_accepted' => 1,
            'status' => 'pending verification',
            'additional_contact' => $inputs['aditionalNumber'],
            'hide_identity' => $request['hideIdentity'] == "true" ? 1 : 0,
        ]);

        return response([
            'post' => $post,
            'scholarship' => $scholarship
        ], 201);
    }

    public function update(Request $request)
    {
        $request->validate([
            'no_of_scholarships' => 'required',
            'worth_of_scholarship' => 'required',
            'description' => 'required|string',
            'additional_contact' => 'required|string',
        ]);

        $request->validate([
            'description' => 'required|string',
        ]);
        $scholarship = Scholarship::findOrfail($request['id']);
        $scholarship->description = $request['description'];
        $scholarship->no_of_scholarships = $request['no_of_scholarships'];
        $scholarship->worth_of_scholarship = $request['worth_of_scholarship'];
        $scholarship->additional_contact = $request['additional_contact'];
        $scholarship->save();

        return response($scholarship);
    }
}
