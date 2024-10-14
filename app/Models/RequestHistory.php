<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestHistory extends Model
{
    protected $table = 'requests';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

}
