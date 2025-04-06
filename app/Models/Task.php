<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $table = 'tasks';

    protected $fillable = [
        'name',
        'description',
        'status',
        'priority',
        'category',
        'story_points',
        'sprint_id',
        'user_id',
        'estimated_start',
        'estimated_finish',
        'estimated_hours',
        'actual_start',
        'actual_finish',
        'actual_hours',
    ];

    public function casts(): array
    {
        return [
            'name' => 'string',
            'description' => 'string',
            'status' => 'string',
            'priority' => 'string',
            'category' => 'string',
            'story_points' => 'integer',
            'sprint_id' => 'integer',
            'user_id' => 'integer',
            'estimated_start' => 'date',
            'estimated_finish' => 'date',
            'estimated_hours' => 'integer',
            'actual_start' => 'date',
            'actual_finish' => 'date',
            'actual_hours' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }
}
