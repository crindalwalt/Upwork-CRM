<?php

namespace App\Models;

use App\Enums\BudgetType;
use App\Enums\JobDifficulty;
use App\Enums\JobNiche;
use Database\Factories\JobFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    /** @use HasFactory<JobFactory> */
    use HasFactory, HasUlids;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'upwork_job_id',
        'title',
        'url',
        'description',
        'niche',
        'budget_type',
        'budget_min',
        'budget_max',
        'hourly_rate_min',
        'hourly_rate_max',
        'posted_at',
        'proposals_count_at_time',
        'difficulty',
        'required_skills',
        'is_featured',
        'employer_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'niche' => JobNiche::class,
            'budget_type' => BudgetType::class,
            'difficulty' => JobDifficulty::class,
            'required_skills' => 'array',
            'posted_at' => 'datetime',
            'is_featured' => 'boolean',
            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',
            'hourly_rate_min' => 'decimal:2',
            'hourly_rate_max' => 'decimal:2',
        ];
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function budgetDisplay(): string
    {
        if ($this->budget_type === BudgetType::Hourly) {
            return sprintf(
                '$%s - $%s/hr (Hourly)',
                $this->formatCurrency($this->hourly_rate_min),
                $this->formatCurrency($this->hourly_rate_max)
            );
        }

        return sprintf(
            '$%s - $%s (Fixed)',
            $this->formatCurrency($this->budget_min),
            $this->formatCurrency($this->budget_max)
        );
    }

    public function isRecent(): bool
    {
        return $this->posted_at !== null
            && $this->posted_at->greaterThanOrEqualTo(now()->subHours(48));
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->where('posted_at', '>=', now()->subHours(48));
    }

    public function scopeByNiche(Builder $query, JobNiche $niche): Builder
    {
        return $query->where('niche', $niche->value);
    }

    private function formatCurrency(string|float|int|null $amount): string
    {
        if ($amount === null) {
            return '0';
        }

        $value = (float) $amount;
        $decimals = floor($value) === $value ? 0 : 2;

        return number_format($value, $decimals);
    }
}
