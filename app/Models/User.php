<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Feedback;
use App\Models\ReceivedService;
use App\Models\Informer;
use App\Models\ServiceRequest;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'f_name',
        'l_name',
        'telephone',
        'address',
        'status',
        'dob',
        'gender',
        'nic',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts() {
        return $this->hasMany(Post::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function feedbacks() {
        return $this->hasMany(Feedback::class);
    }

    public function servicesRecieved() {
        return $this->hasMany(ReceivedService::class);
    }

    public function informers() {
        return $this->hasMany(Informer::class);
    }

    public function service_requests() {
        return $this->hasMany(ServiceRequest::class);
    }
}
