<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    protected $table = 'projects';

    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by',
    ];

    public function casts(): array
    {
        return [
            'name' => 'string',
            'description' => 'string',
            'status' => 'string',
            'created_by' => 'integer',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id');
    }

    public function sprints(): BelongsTo
    {
        return $this->belongsTo(Sprint::class, 'sprint_id');
    }
}
