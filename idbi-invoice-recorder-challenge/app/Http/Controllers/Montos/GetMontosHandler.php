<?php

namespace App\Http\Controllers\Montos;

use App\Services\VoucherService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class GetMontosHandler
{
    private VoucherService $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    public function __invoke(): JsonResponse
    {
        // Obtener el usuario autenticado
        $userId = Auth::id();

        Log::info('user id: '. $userId);

        // Calcular los montos totales por moneda usando el servicio
        $montos = $this->voucherService->getMontosPorMoneda($userId);

        // Retornar la respuesta como JSON
        return response()->json($montos);
    }
}
