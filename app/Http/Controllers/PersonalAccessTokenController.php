<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonalAccessToken;
use App\Models\User;

class PersonalAccessTokenController extends Controller
{
    public function getTokens() {
        $tokens = PersonalAccessToken::all();
        $tokensFinal = array();
       
        foreach ($tokens as $token) {   
            $user = User::findOrFail($token->tokenable_id);
            array_push($tokensFinal, [ 
                'id' => $token->id,
                'token_id' => $token->tokenable_id,
                'name' => $user->name,
                'last_used' => $token->last_used_at,
                'created_at' => $token->created_at,
                'updated_at' => $token->updated_at
            ]);
        }
        return response($tokensFinal);
    }
}
