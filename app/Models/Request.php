<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $table = 'Requests';
    protected $primaryKey = 'R_Id';
    protected $guarded = ['R_Id'];
    public const CREATED_AT = 'R_CreatedAt';
    public const UPDATED_AT = 'R_UpdatedAt';


}
