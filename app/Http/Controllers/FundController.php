<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fund;
use App\Models\Post;

class FundController extends Controller
{
    public function updateFunder(Request $request)
    {
        $fund = Fund::create([
            'post_id' => $request['post_id'],
            'user_id' => $request['user_id'],
            'name' => $request['name'],
            'telephone' => $request['mobile'],
            'amount' => $request['amount'],
            'address' => $request['address'],
            'description' => $request['description'],
            'category' => $request['category']
        ]);

        return response($fund);
    }

    public function getAllFunds()
    {
        $funds = Fund::select('*')->orderByDesc('id')->get();
        return response($funds);
    }

    public function getAllFundsWithPosts()
    {
        $posts = Post::select('*')->orderByDesc('id')->get();

        $newPostFormat = [
            'id' => ''
        ];
        $postWithTotalFunds = array();
        foreach ($posts as $post) {
            $tot = 0;
            $postSingle = Post::findOrFail($post['id']);
            $postSingle->user;
            $singleFunds = $postSingle->funds;
            foreach ($singleFunds as $singleFund) {
                // $tot += number_format($singleFund['amount']);
                if ($singleFund->status === "active") {
                    $tot += $singleFund['amount'];
                }
            }
            array_push($postWithTotalFunds, [
                'id' => $postSingle->id,
                'posted_by' => $postSingle->user->name,
                'user_id' => $postSingle->user->id,
                'category' => $postSingle->category,
                'title' => $postSingle->title,
                'amount' => $tot,
            ]);
        }
        return response([
            'posts' => $posts,
            'postWithTotalFunds' => $postWithTotalFunds
        ]);
    }
}
