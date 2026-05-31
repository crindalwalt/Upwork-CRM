<?php

namespace App\Models;

use App\Enums\FollowUpType;
use Database\Factories\FollowUpFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUp extends Model
{
    /** @use HasFactory<FollowUpFactory> */
    use HasFactory, HasUlids;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'proposal_id',
        'user_id',
        'type',
        'scheduled_at',
        'completed_at',
        'is_done',
        'outcome_note',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => FollowUpType::class,
            'is_done' => 'boolean',
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markDone(?string $outcome = null): void
    {
        $attributes = [
            'is_done' => true,
            'completed_at' => now(),
        ];

        if ($outcome !== null) {
            $attributes['outcome_note'] = $outcome;
        }

        $this->forceFill($attributes)->save();
    }

    public function isOverdue(): bool
    {
        return ! $this->is_done
            && $this->scheduled_at !== null
            && $this->scheduled_at->isPast();
    }
}
