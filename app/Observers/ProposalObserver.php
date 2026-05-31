<?php

namespace App\Observers;

use App\Enums\ProposalStatus;
use App\Events\ProposalStatusChanged;
use App\Models\Proposal;
use Illuminate\Support\Facades\Log;

class ProposalObserver
{
    public function creating(Proposal $proposal): void
    {
        if ($proposal->employer_id !== null) {
            return;
        }

        $job = $proposal->relationLoaded('job') ? $proposal->job : $proposal->job()->first();

        if ($job?->employer_id !== null) {
            $proposal->employer_id = $job->employer_id;
        }
    }

    public function updating(Proposal $proposal): void
    {
        $status = $this->normalizeStatus($proposal->status);

        if ($proposal->isDirty('status') && $status === ProposalStatus::Won && $proposal->won_amount === null) {
            Log::warning('Proposal marked as won without a won_amount value.', [
                'proposal_id' => $proposal->id,
            ]);
        }
    }

    public function updated(Proposal $proposal): void
    {
        if (! $proposal->wasChanged('status')) {
            return;
        }

        event(new ProposalStatusChanged(
            $proposal,
            $this->normalizeStatus($proposal->getOriginal('status')),
        ));
    }

    private function normalizeStatus(ProposalStatus|string $status): ProposalStatus
    {
        return $status instanceof ProposalStatus
            ? $status
            : ProposalStatus::from($status);
    }
}
