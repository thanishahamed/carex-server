<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function create(Request $request)
    {
        $inputs = $request->validate([
            'description' => 'required|string',
            'subject' => 'required|string'
        ]);

        $req = ServiceRequest::create([
            'user_id' => auth()->user()->id,
            'subject' => $inputs['subject'],
            'description' => $inputs['description'],
            'agreement_accepted' => 1,
            'status' => 'pending',
            'additional_contact' => 'none',
            'hide_identity' => 0
        ]);

        return response($req, 201);
    }
}
