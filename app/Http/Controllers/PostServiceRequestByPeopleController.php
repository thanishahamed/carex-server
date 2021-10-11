<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostServiceRequestByPeople;
use App\Models\Post;
use App\Models\Organ;
use App\Models\Like;

class PostServiceRequestByPeopleController extends Controller
{
    public function createRequest(Request $request) {
        $inputs = $request->validate([
            'file' => 'required|file',
            'name' => 'required|string',
            'address' => 'required|string',
            'description' => 'required|string',
            'nic' => 'required|string|min:10|max:12',
        ]);

        $upload = $request['file'];
        $uploadName = $upload->getClientOriginalName();
        $uploadFileName = time()."_".$uploadName;
        $uploadFilePath = asset('/images/'.$uploadFileName);
        $upload->move('images', $uploadFileName);

        $req = PostServiceRequestByPeople::create([
            'user_id' => auth()->user()->id,
            'post_id' => $request['post_id'],
            'description' => $request['description'],
            'name' => $request['name'],
            'nic' => $request['nic'], 
            'address' => $request['address'],
            'approval_document_link' => $uploadFilePath,
            'status'=> 'requested',
        ]);

        return response($req);
    }

    public function findRequestInfo(Request $request) {
        $service = PostServiceRequestByPeople::findOrFail($request['requestId']);
        $post = Post::findOrFail($service['post_id']);
        $post->images;
        $post->comments;
        $post->user;
        $post->shares;
        $post->likes;
        $post->approvals;
        $post->services;
        $post->blood_donation;
        $post->organ_donation;
        $post->education_fund;
        $post->service_requests_by_people;
        // $comments = Comment::where('post_id', $id)->get();

        $liked = Like::where('post_id', $service['post_id'])->where('user_id', auth()->user()->id)->get();
        $post->liked = $liked;

        $organs = Organ::select('*')->where('organ_donation_id', $post->organ_donation->id)->get();

        return response([
            'service' => $service,
            'post' => $post,
            'organs' => $organs
        ]);
    }
}
