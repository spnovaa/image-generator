<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                 $id
 * @property string              $email
 * @property RequestStatus       $status
 * @property string|null         $caption
 * @property string|null         $url
 * @property string|null         $file_name
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 */
class RequestHistory extends Model
{
    protected $table      = 'requests';
    protected $primaryKey = 'id';

    protected $fillable = [
        'email',
        'status',
        'caption',
        'url',
        'file_name',
    ];

    protected $casts = [
        'status' => RequestStatus::class,
    ];

    public function scopeWithStatus(Builder $query, RequestStatus $status): Builder
    {
        return $query->where('status', $status->value);
    }

    public function markAs(RequestStatus $status): self
    {
        $this->status = $status;
        return $this;
    }
}
