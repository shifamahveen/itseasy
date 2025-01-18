<?php

namespace App\Models\Community;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'slug',
        'client_slug',
        'type',
        'location',
        'zone',
        'zone_code',
        'district',
        'state',
        'contact_person',
        'contact_designation',
        'contact_phone',
        'contact_email',
        'client_id',
        'rating',
        'reviews',
        'category',
        'address',
        'status',
    ];
}
