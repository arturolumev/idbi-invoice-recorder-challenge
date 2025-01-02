<?php

namespace App\Jobs;

use App\Models\Voucher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use App\Models\User;

class ProcessVoucherJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $voucher;
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(Voucher $voucher)
    {
        Log::info('Evento VouchersCreated disparado');
        $this->voucher = $voucher;
        $this->user = auth()->user();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Listas para almacenar los resultados
        $successfullyProcessed = [];
        $failedProcessing = [];

        // Lógica para validar y guardar comprobante
        try {
            $this->voucher->validateAndSave(); // Método que implementas en el modelo

            // Si se procesa correctamente, agregar a la lista de éxito
            $successfullyProcessed[] = $this->voucher;
        } catch (\Exception $e) {
            // En caso de error, agregar a la lista de fallidos con la razón
            $failedProcessing[] = [
                'voucher_id' => $this->voucher->id,
                'error_message' => $e->getMessage(),
            ];

            Log::error("Error procesando comprobante ID {$this->voucher->id}: {$e->getMessage()}");
        }
    }
}
