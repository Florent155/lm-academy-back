<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Userlist extends Model
{
    protected  $filable = [
       'list_name' 
    ];

    public function users() {
        return $this->bleongsToMany(User::class, 'user_list_items',  'list_id' ,'user_id' )->withTimestamps();

    } 

 }