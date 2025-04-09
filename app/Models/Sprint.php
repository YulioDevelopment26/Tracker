<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sprint extends Model
{
    protected $table = 'sprints';

    use softDeletes;
    protected $fillable = [
      'name',
      'goal',
      'start_date',
      'end_date',
      'project_id',
    ];

    public function casts(): array
    {
        return [
          'name' => 'string',
          'goal' => 'string',
          'start_date' => 'date',
          'end_date' => 'date',
          'project_id' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks(): BelongsTo
    {
        return $this->BelongsTo(Task::class);
    }
}
