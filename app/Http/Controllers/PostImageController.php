<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostImage;

class PostImageController extends Controller
{
    public function getPostOfTheImage($id) {
        $image = PostImage::findOrFail($id);
        $image->post;

        return response($image,200);
    }
}
