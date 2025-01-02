<?php

namespace App\Listeners;

use App\Events\Vouchers\VouchersCreated;
use App\Mail\VouchersCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Log;

class SendVoucherAddedNotification implements ShouldQueue
{
    public function handle(VouchersCreated $event): void
    {
        Log::info('Listener ejecutado para el evento VouchersCreated');
        $mail = new VouchersCreatedMail($event->successfullyProcessed, 
        $event->failedProcessing, 
        $event->vouchers, 
        $event->user);
        Mail::to($event->user->email)->send($mail);
        Log::info('Enviando correo a: ' . $event->user->email);
    }
}
