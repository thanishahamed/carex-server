<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function create(Request $request) {
        $comment = Comment::create([
            'user_id' => auth()->user()->id,
            'post_id' => $request['post_id'],
            'comment' => $request['comment']
        ]);

        return response($comment, 200);
    }
}
