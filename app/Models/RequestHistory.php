<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestHistory extends Model
{
    protected $table = 'Requests';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

}
