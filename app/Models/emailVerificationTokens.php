<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class emailVerificationTokens extends Model
{
    protected $fillable = [
        'email', 'token', 'code'
    ];
}
