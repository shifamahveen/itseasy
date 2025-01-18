<?php

namespace App\Models\Hire;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Hire\Job;

class JobUser extends Model
{
    use HasFactory;

    protected $table = 'job_user';

    protected $fillable = [
        'job_id', 'user_id', 'data', 'applied_at', 
        'result_at', 'accesscode', 'feedback', 'status'
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];
    

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
