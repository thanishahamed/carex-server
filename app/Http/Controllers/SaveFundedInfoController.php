<?php

namespace App\Http\Controllers;

use App\Models\Fund;
use App\Models\PostServiceRequestByPeople;
use App\Models\SaveFundedInfo;
use Illuminate\Http\Request;

class SaveFundedInfoController extends Controller
{
    public function save_funded_info(Request $request)
    {
        $funds = Fund::select('*')->where('post_id', $request['id'])->get();

        if (count($funds) <= 0) {
            return response('Funds not fund for this service to proceed!', 404);
        }

        foreach ($funds as $fund) {
            $fnd = Fund::find($fund->id);
            $fnd->status = 'funded';
            $fnd->update();
        }
        $data = SaveFundedInfo::create([
            'user_id' => $request['user_id'],
            'post_id' => $request['id'],
            'amount' => $request['amount']
        ]);

        return response($data, 200);
    }

    public function download_successfull_funds(Request $request)
    {
        $fileName = 'fundedServices.csv';
        $funded = SaveFundedInfo::select('*')->get();
        $services = array();

        foreach ($funded as $fnd) {
            $fund = SaveFundedInfo::find($fnd->id);
            $fund->user;
            $fund->post;
            array_push($services, $fund);
        }

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Name', 'NIC', 'Email', 'Mobile', 'Address', 'Gender', 'Service', 'Title', 'Amount', 'Description');

        $callback = function () use ($services, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($services as $task) {
                $row['Name'] = $task->user->name;
                $row['NIC'] = $task->user->nic;
                $row['Email'] = $task->user->email;
                $row['Mobile'] = $task->user->telephone;
                $row['Address'] = $task->user->address;
                $row['Gender'] = $task->user->gender;
                $row['Service'] = $task->post->category;
                $row['Title'] = $task->post->title;
                $row['Amount'] = $task->amount;
                $row['Description'] = $task->post->description;

                // fputcsv($file, array($row['Name'], $row['NIC'], $row['Amount'], $row['Description']));
                fputcsv($file, array($row['Name'], $row['NIC'], $row['Email'], $row['Mobile'], $row['Address'], $row['Gender'], $row['Service'], $row['Title'], $row['Amount'], $row['Description']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
