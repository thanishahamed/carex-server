<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrganDonation;
use App\Models\Organ;
use Illuminate\Support\Facades\Crypt;
use App\Models\Post;
use App\Models\PostServiceRequestByPeople;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class OrganDonationController extends Controller
{
    public function organDonationInfo($id) {
        $organDonation = OrganDonation::findOrFail($id);
        $organDonation->organs;

        return $organDonation;
    }

    public function getAgreementForm() {
        return response()->file(public_path('/downloadable/'.'organDonation.pdf'), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function create(Request $request) {
        
        $inputs = $request->validate([
            'agreementForm' => 'required|file',
            'hospitalForm' => 'required|file',
            'bloodGroup' => 'required|string',
            'aditionalNumber' => 'required|string'

          ]);

        $organDonation = Post::select('*')->where('category','organ donation')->where('id', auth()->user()->id)->get();

        if(count($organDonation) > 0) {
            return response([
                'errors' => ['duplicate Entry' => array('You have already registered to organ donation')]
            ],422);
        }

        $user = auth()->user();
        $after_death_organs = "\n\nAfter death or brain death: ";
        $alive_organs = "\n\nWhile alive: ";
        $alive = array();
        $after_death = array();
        $request->heart == "true" ? array_push($after_death, 'Heart') : '';
        $request->kidney == "true" ? array_push($after_death, 'Kidney') : '';
        $request->liver == "true" ? array_push($after_death, 'Liver') : '';
        $request->lungs == "true" ? array_push($after_death, 'Lungs') : '';
        $request->pancrease == "true" ? array_push($after_death, 'Pancrease') : '';
        $request->intestines == "true" ? array_push($after_death, 'Intestines') : '';
        $request->skin == "true" ? array_push($after_death, 'Skin') : '';
        $request->bloodVessels == "true" ? array_push($after_death, 'Blood Vessels') : '';
        $request->eyeTissues == "true" ? array_push($after_death, 'Eye Tissues') : '';
        $request->heartValves == "true" ? array_push($after_death, 'Heart Valves') : '';
        $request->boneTissue == "true" ? array_push($after_death, 'Bone Tissue') : '';
        
        $request->alive_kidney == "true" ? array_push($alive, '1 Kidney') : '';
        $request->alive_partOfTheLiver == "true" ? array_push($alive, 'Part of the liver') : '';
        $request->alive_partOfTheLungs == "true" ? array_push($alive, 'Part of the lungs') : '';
        $request->alive_stemCells == "true" ? array_push($alive, 'Part of stem cells') : '';
        $request->alive_boneMarrow == "true" ? array_push($alive, 'Part of bone marrow') : '';
        
        foreach ($after_death as $organ) {
            $after_death_organs = $after_death_organs."\n - ".$organ;
        }
        
        foreach ($alive as $organ) {
            $alive_organs = $alive_organs."\n - ".$organ;
        }
        
        if(count($alive) > 0 || count($after_death) > 0) {

        }else{
            return response([
                'errors' => ['organ' => array('You need to select at least one organ to donate')]
            ],422);
        }
        $postBody = 'I like to donate my organs for the needed people. Please contact me for more information.';
        $postBody = $postBody.$after_death_organs.$alive_organs;

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Interested in donating organs ('.$request['bloodGroup'].')',
            'body' => $postBody,
            'status' => 'pending',
            'category' => 'organ donation'
        ]);
        $agreement = $request->agreementForm;
        $agreementName = $agreement->getClientOriginalName();
        $agreementFileName = time()."_".$agreementName;
        $agreementFilePath = asset('/images/'.$agreementFileName);
        $agreement->move('images', $agreementFileName);

        $hostpitalForm = $request->hospitalForm;
        $hostpitalFormName = $hostpitalForm->getClientOriginalName();
        $hostpitalFormFileName = time()."_".$hostpitalFormName;
        $hostpitalFormFilePath = asset('/images/'.$hostpitalFormFileName);
        $hostpitalForm->move('images', $hostpitalFormFileName);

        $organ_donation = OrganDonation::create([
            'post_id' => $post->id,
            'blood_group' => $request->bloodGroup,
            'additional_contact' => $request->aditionalNumber,
            'description' => 'none',
            'status' => 'pending',
            'method' => 'organ',
            'agreement_link' => $agreementFilePath,
            'agreement_accepted' => 0,
            'hospital_certificate_link' => $hostpitalFormFilePath,
            'hide_identity' => $request['hideIdentity'] == "true" ? 1 : 0,
            'additional_tests' => '1',
        ]);

        
        foreach($after_death as $organ) {
            Organ::create([
                'organ_donation_id' => $organ_donation->id,
                'user_id' => $user->id,
                'alive_death' => 'death',
                'organ_name' => $organ,
                'status' => 'pending',
                'transplanted_to' => '',
                'received_address' => '',
                'description' => '',
                'agreement_accepted' => 1,
                'additional_contact' => $request->aditionalNumber,
                'agreement_link' => '',
                'hospital_certificate_link' => '',
                'hide_identity' => 1,
                'additional_tests' => 1,
            ]);
        }

        foreach($alive as $organ) {
            Organ::create([
                'organ_donation_id' => 1, //$organ_donation->id,
                'user_id' => $user->id,
                'alive_death' => 'alive',
                'organ_name' => $organ,
                'status' => 'pending',
                'transplanted_to' => '',
                'received_address' => '',
                'description' => '',
                'agreement_accepted' => 1,
                'additional_contact' => $request->aditionalNumber,
                'agreement_link' => '',
                'hospital_certificate_link' => '',
                'hide_identity' => 1,
                'additional_tests' => 1,
            ]);
        }
        
        return response($post);
    }

    public function updateAvailability(Request $request) {
        $inputs = $request->validate([
            'description' => 'required|string',
            'informer_proof_certificate' => 'required|file',
            'informer_id' => 'required|string',
            'organ_id' => 'required|string'
        ]);

        $donation = OrganDonation::findOrFail($request['organ_id']);

        
        $informer_proof_certificate = $request->informer_proof_certificate;
        $informer_proof_certificate_name = $informer_proof_certificate->getClientOriginalName();
        $informer_proof_certificate_file_name = time()."_".$informer_proof_certificate_name;
        $informer_proof_certificate_path = asset('/images/'.$informer_proof_certificate_file_name);
        $informer_proof_certificate->move('images', $informer_proof_certificate_file_name);
        
        $donation->informer_id = $request['informer_id'];
        $donation->informed_on = $request['informed_on'];
        $donation->informer_proof_certificate = $informer_proof_certificate_path;
        $donation->status = $request['status'];

        $donation->update();

        $post = Post::findOrFail($donation->post_id);

        $post->status = "informed";
        $post->update();
        
        $enc = Crypt::encryptString($post->id);

        $externs = User::select('*')->where('role', 'extern')->orWhere('role', 'admin')->get();

        foreach($externs as $extern) {
            $data = [
                "name" => $extern->name,
                "link" => env('FRONT_END')."approve-donation-for-public/".$enc."/".Crypt::encryptString($extern->id),
                "email" => $extern->email
            ];
        
            Mail::send('informer.informerInform', $data, function($message) use ($data) {
                $message->to($data['email'], $data['name'])->subject('Organ Donation Informed!');
            });
        }
        
        

        return response($externs);
    }

    public function checkPostRecord(Request $request, $id, $userId) {
        $dec = Crypt::decryptString($id);
        $uId = Crypt::decryptString($userId);
        $user = User::findOrFail($uId);
        $post = Post::findOrFail($dec);
        $organ_donation = OrganDonation::select("*")->where('post_id', $dec)->get();

        return response([
            'organDonation' => $organ_donation[0],
            'post' => $post,
            'user' => $user
        ],200);
    }

    public function approveOrganDonation(Request $request) {

        // if($request->organDonation['status'] === 'available') {
        //     return response("You can't make a service availble for mulitiple times.");
        // }
        $organ_donation = OrganDonation::findOrFail($request->organDonation['id']);
        $organ_donation->status = 'available';
        $organ_donation->update();

        $post = Post::findOrFail($request->post['id']);
        $post->status = 'available';
        $post->update();

        $users = User::all();

        $organs = Organ::select('*')->where('organ_donation_id', $organ_donation->id)->get();

        foreach($organs as $organ) {
            $o = Organ::findOrFail($organ->id);
            $o->status = "available";
            $o->update();
        }

        foreach($users as $user) {
            $data = [
                "name" => $user->name,
                "link" => env('FRONT_END')."services/explore/service/".$post->id,
                "email" => $user->email
            ];
        
            Mail::send('organ.available', $data, function($message) use ($data) {
                $message->to($data['email'], $data['name'])->subject('Organ Donation Available!');
            });
        }
        return response($post);
    }

    public function donateOrgan(Request $request) {
        $organ = Organ::findOrFail($request->organId);

        $organ->transplanted_to = $request->data['service']['name'];
        $organ->received_address = $request->data['service']['address'];
        $organ->status = "donated";
        $organ->received_on = $request->date;
        $organ->received_nic = $request->data['service']['name'];
        $organ->description = $request->data['service']['description'];

        $organ->update();

        $postService = PostServiceRequestByPeople::findOrFail($request->data['service']['id']);
        $postService->status = "responded";
        $postService->update();

        return response($organ);
    }
}
