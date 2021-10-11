<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\View;

class ViewController extends Controller
{
    public function viewed(Request $request) {
        $view = View::create([
            'post_id' => $request['post_id'],
            'user_id' => $request['user_id']
        ]);

        return response($view, 200);
    }
}
