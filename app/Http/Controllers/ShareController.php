<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Share;

class ShareController extends Controller
{
    public function share(Request $request) {
        $share = Share::create([
            'post_id' => $request['post_id'], 
            'user_id' => auth()->user()->id,
            'shared_to' => $request['sharedTo']
        ]);

        return response($share, 200);
    }
}
