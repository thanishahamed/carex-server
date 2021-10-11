<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Organ;

class OrganDonation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    
    public function organs() {
        return $this->hasMany(Organ::class);
    }

}
