<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\PostImage;
use App\Models\Comment;
use App\Models\Share;
use App\Models\View;
use App\Models\Like;
use App\Models\Approval;
use App\Models\ReceivedService;
use App\Models\BloodDonation;
use App\Models\OrganDonation;
use App\Models\EducationFund;
use App\Models\Fund;
use App\Models\PostServiceRequestByPeople;

class Post extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function images() {
        return $this->hasMany(PostImage::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function views() {
        return $this->hasMany(View::class);
    }

    public function shares() {
        return $this->hasMany(Share::class);
    }

    public function likes() {
        return $this->hasMany(Like::class);
    }

    public function approvals() {
        return $this->hasOne(Approval::class);
    }

    public function services() {
        return $this->hasMany(ReceivedService::class);
    }

    public function blood_donation() {
        return $this->hasOne(BloodDonation::class);
    }

    public function organ_donation() {
        return $this->hasOne(OrganDonation::class);
    }

    public function education_fund() {
        return $this->hasOne(EducationFund::class);
    }

    public function service_requests_by_people() {
        return $this->hasMany(PostServiceRequestByPeople::class);
    }

    public function funds() {
        return $this->hasMany(Fund::class);
    }
}
