<?php

namespace App\Enums;

enum ProposalStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Viewed = 'viewed';
    case Replied = 'replied';
    case InterviewScheduled = 'interview_scheduled';
    case Won = 'won';
    case Lost = 'lost';
    case Withdrawn = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Sent => 'Sent',
            self::Viewed => 'Viewed',
            self::Replied => 'Replied',
            self::InterviewScheduled => 'Interview Scheduled',
            self::Won => 'Won',
            self::Lost => 'Lost',
            self::Withdrawn => 'Withdrawn',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Sent => 'blue',
            self::Viewed => 'amber',
            self::Replied => 'purple',
            self::InterviewScheduled => 'slate',
            self::Won => 'green',
            self::Lost => 'red',
            self::Withdrawn => 'orange',
        };
    }

    /**
     * @return array<int, self>
     */
    public static function activeStatuses(): array
    {
        return [
            self::Sent,
            self::Viewed,
            self::Replied,
            self::InterviewScheduled,
        ];
    }
}
