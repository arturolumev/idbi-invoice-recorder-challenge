<?php

namespace App\Events\Vouchers;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VouchersCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public readonly array $successfullyProcessed;
    public readonly array $failedProcessing;
    public readonly array $vouchers;
    public readonly User $user;

    /**
     * @param Voucher[] $vouchers
     * @param User $user
     * @param Voucher[] $successfullyProcessed
     * @param array $failedProcessing
     */
    public function __construct(
        array $vouchers,
        User $user,
        array $successfullyProcessed = [],
        array $failedProcessing = []
    ) {
        $this->vouchers = $vouchers;
        $this->user = $user;
        $this->successfullyProcessed = $successfullyProcessed;
        $this->failedProcessing = $failedProcessing;
    }
}
