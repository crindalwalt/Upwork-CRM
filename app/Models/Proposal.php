<?php

namespace App\Models;

use App\Enums\ProposalStatus;
use Database\Factories\ProposalFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proposal extends Model
{
    /** @use HasFactory<ProposalFactory> */
    use HasFactory, HasUlids;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'job_id',
        'employer_id',
        'user_id',
        'status',
        'connects_spent',
        'bid_amount',
        'bid_hourly_rate',
        'cover_letter',
        'loom_url',
        'loom_view_count',
        'loom_viewed_at',
        'loom_viewed',
        'has_leverage',
        'leverage_portfolio_id',
        'leverage_notes',
        'ai_score',
        'ai_score_reasoning',
        'ai_script',
        'sent_at',
        'replied_at',
        'interview_at',
        'closed_at',
        'won_amount',
        'loss_reason',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProposalStatus::class,
            'has_leverage' => 'boolean',
            'loom_viewed' => 'boolean',
            'ai_score' => 'integer',
            'connects_spent' => 'integer',
            'loom_view_count' => 'integer',
            'sent_at' => 'datetime',
            'replied_at' => 'datetime',
            'interview_at' => 'datetime',
            'closed_at' => 'datetime',
            'loom_viewed_at' => 'datetime',
            'bid_amount' => 'decimal:2',
            'bid_hourly_rate' => 'decimal:2',
            'won_amount' => 'decimal:2',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leveragePortfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class, 'leverage_portfolio_id');
    }

    public function proposalNotes(): HasMany
    {
        return $this->hasMany(ProposalNote::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ProposalStatus::activeStatuses(), true);
    }

    public function daysToReply(): ?int
    {
        if ($this->sent_at === null || $this->replied_at === null) {
            return null;
        }

        return $this->sent_at->diffInDays($this->replied_at);
    }

    public function markAsSent(): void
    {
        $this->forceFill([
            'status' => ProposalStatus::Sent,
            'sent_at' => now(),
        ])->save();
    }

    public function markAsViewed(): void
    {
        $this->forceFill([
            'status' => ProposalStatus::Viewed,
            'loom_viewed' => true,
            'loom_viewed_at' => now(),
            'loom_view_count' => $this->loom_view_count + 1,
        ])->save();
    }

    public function markAsReplied(): void
    {
        $this->forceFill([
            'status' => ProposalStatus::Replied,
            'replied_at' => now(),
        ])->save();
    }

    public function scopeByStatus(Builder $query, ProposalStatus $status): Builder
    {
        return $query->where('status', $status->value);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', array_map(
            static fn (ProposalStatus $status) => $status->value,
            ProposalStatus::activeStatuses()
        ));
    }

    public function scopeHighScore(Builder $query, int $min = 7): Builder
    {
        return $query->where('ai_score', '>=', $min);
    }

    public function scopeSentToday(Builder $query): Builder
    {
        return $query->whereDate('sent_at', today());
    }

    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('sent_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }
}
