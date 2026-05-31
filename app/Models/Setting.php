<?php

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function typedValue(): mixed
    {
        return match ($this->type) {
            'integer' => $this->value === null ? null : (int) $this->value,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOL),
            'json' => $this->value === null ? null : json_decode($this->value, true, flags: JSON_THROW_ON_ERROR),
            default => $this->value,
        };
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        return $setting?->typedValue() ?? $default;
    }

    public static function set(string $key, mixed $value, string $type = 'string', ?string $description = null): self
    {
        $serializedValue = match ($type) {
            'json' => $value === null ? null : json_encode($value, JSON_THROW_ON_ERROR),
            'boolean' => $value ? '1' : '0',
            default => $value === null ? null : (string) $value,
        };

        return static::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $serializedValue,
                'type' => $type,
                'description' => $description,
            ]
        );
    }
}
