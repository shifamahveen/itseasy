<?php

namespace App\Models\Hire;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'company', 'locations', 'description', 'logo',
    ];

    public function bookmarkedUsers()
    {
        return $this->belongsToMany(User::class, 'job_bookmarks');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'job_user')->withPivot('data', 'applied_at');
    }

}