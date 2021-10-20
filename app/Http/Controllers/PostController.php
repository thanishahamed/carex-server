<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use App\Models\PostImage;
use App\Models\Approval;
use App\Models\Fund;
use App\Models\User;

class PostController extends Controller
{

    public function getPostInfo(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $post->images;
        // $post->comments;
        $post->user;
        $post->shares;
        $post->likes;
        $post->views;
        $post->approvals;
        $post->services;
        $post->blood_donation;
        $post->organ_donation;
        $post->education_fund;
        $post->service_requests_by_people;
        // $comments = Comment::where('post_id', $id)->get();

        $liked = Like::where('post_id', $id)->where('user_id', $request['user_id'])->get();
        $post->liked = $liked;

        $funds = Fund::where('post_id', $id)->get();
        $post->funds = $funds;

        $commentsWithUsers = array();
        $comments = $post->comments;
        foreach ($comments as $com) {
            $user = User::findOrFail($com->user_id);
            array_push($commentsWithUsers, ['user' => $user, 'comment' => $com]);
        }

        $post->commentsWithUsers = $commentsWithUsers;

        return $post;
    }

    public function getAllPosts()
    {
        $posts = Post::select('*')->orderByDesc('id')->get();
        $available = Post::select('*')->where('status', 'available')->orderByDesc('id')->get();

        foreach ($posts as $post) {
            $p = Post::findOrFail($post->id);
            $user = $p->user;

            $post->user = $user;
            $post->images;
        }

        foreach ($available as $av) {
            $a = Post::findOrFail($av->id);
            $user = $a->user;

            $av->user = $user;
        }

        return response([
            'posts' => $posts,
            'available' => $available
        ]);
    }

    public function createPost(Request $request)
    {
        $inputs = $request->validate([
            'title' => 'required|string',
            'body' => 'required|string'
        ]);

        $post = Post::create([
            'title' => $request['title'],
            'body' => $request['body'],
            'user_id' => $request['user_id'],
            'category' => 'community request',
            'status' => 'pending verification',
        ]);

        $num = 0;
        $files = array();

        if ($request['image0']) {
            array_push($files, $request['image0']);
        }
        if ($request['image1']) {
            array_push($files, $request['image1']);
        }
        if ($request['image2']) {
            array_push($files, $request['image2']);
        }
        if ($request['image3']) {
            array_push($files, $request['image3']);
        }
        if ($request['image4']) {
            array_push($files, $request['image4']);
        }

        foreach ($files as $file) {
            $upload = $file;
            $uploadName = $upload->getClientOriginalName();
            $uploadFileName = time() . "_" . $uploadName;
            $uploadFilePath = asset('/images/' . $uploadFileName);
            $upload->move('images', $uploadFileName);

            $image = PostImage::create([
                'post_id' => $post->id,
                'url' => $uploadFilePath
            ]);
        }

        return response($post);
    }

    public function closePost(Request $request)
    {
        $post = Post::findOrFail($request['post_id']);
        $post->status = "closed";
        $post->update();

        return response($post);
    }

    public function approvePost(Request $request)
    {
        $user = auth()->user();

        $approval = '';
        if ($request['status'] === "approved") {
            $approval = Approval::create([
                'user_id' => $user->id,
                'post_id' => $request['postId'],
                'approved' => '1'
            ]);
        }

        $post = Post::findOrFail($request['postId']);
        $post->status = $request['status'];
        $post->update();

        return response([
            'approval' => $approval,
            'post' => $post
        ], 200);
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return $post;
    }
}
