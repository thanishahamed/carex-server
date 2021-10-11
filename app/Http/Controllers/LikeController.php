<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;

class LikeController extends Controller
{
    public function like(Request $request) {
        $like = Like::select('*')
                ->where('post_id', $request['post_id'])
                ->where('user_id', $request['user_id'])
                ->get();

        if(count($like) > 0) {
            $liked = Like::findOrFail($like[0]->id);

            $liked->delete();
            return response($liked);
        }else{
            $liked = Like::create([
                'post_id' => $request['post_id'],
                'user_id' => $request['user_id'],
                'liked' => 1
            ]);

            return response($liked, 200);
        }
    }
}
