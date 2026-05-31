<?php

namespace App\Models;

use Database\Factories\EmployerFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Employer extends Model
{
    /** @use HasFactory<EmployerFactory> */
    use HasFactory, HasUlids;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'upwork_url',
        'location',
        'total_spent',
        'hire_rate',
        'reviews_count',
        'payment_verified',
        'open_jobs_count',
        'member_since',
        'internal_notes',
        'flag',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payment_verified' => 'boolean',
            'total_spent' => 'decimal:2',
            'hire_rate' => 'decimal:2',
            'member_since' => 'date',
        ];
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function qualityScore(): float
    {
        $hireRate = $this->hire_rate === null ? null : (float) $this->hire_rate;
        $totalSpent = $this->total_spent === null ? null : (float) $this->total_spent;
        $paymentVerified = $this->payment_verified;

        if ($hireRate === null && $totalSpent === null && $paymentVerified === null) {
            return 0.0;
        }

        $score = 0;

        if ($hireRate !== null) {
            $score += min(($hireRate / 100) * 4, 4);
        }

        if ($totalSpent !== null) {
            $score += min(($totalSpent / 5000) * 4, 4);
        }

        if ($paymentVerified) {
            $score += 2;
        }

        return round($score, 2);
    }

    public function flagColor(): string
    {
        return $this->flag ?? 'gray';
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('payment_verified', true);
    }

    public function scopeHighQuality(Builder $query): Builder
    {
        return $query
            ->where('hire_rate', '>=', 60)
            ->where('total_spent', '>=', 1000);
    }
}
