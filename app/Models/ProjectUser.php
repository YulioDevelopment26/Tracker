<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectUser extends Model
{
    protected $table = 'project_user';
    use softDeletes;

    protected $fillable = [
        'project_id',
        'user_id',
    ];

    protected $casts = [
        'project_id' => 'integer',
        'user_id' => 'integer',
    ];
}
