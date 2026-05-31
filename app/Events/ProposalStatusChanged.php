<?php

namespace App\Events;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProposalStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Proposal $proposal,
        public ProposalStatus $oldStatus,
    ) {
    }
}
