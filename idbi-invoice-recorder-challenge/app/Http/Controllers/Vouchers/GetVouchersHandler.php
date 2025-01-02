<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Requests\Vouchers\GetVouchersRequest;
use App\Http\Resources\Vouchers\VoucherResource;
use App\Services\VoucherService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Voucher;

class GetVouchersHandler
{
    protected $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService; // Inyectar el servicio
    }

    public function __invoke(Request $request)
    {
        // Validación de parámetros
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'serie' => 'nullable|string',
            'numero' => 'nullable|string',
            'tipo' => 'nullable|string',
            'moneda' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1',  // Añadido para controlar la cantidad de resultados por página
        ]);

        // Obtener los filtros de la consulta
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'serie' => $request->input('serie'),
            'numero' => $request->input('numero'),
            'tipo' => $request->input('tipo'),
            'moneda' => $request->input('moneda'),
        ];

        // Obtener el número de página y los resultados por página
        $page = $request->query('page', 1); // Página actual (default 1)
        $perPage = $request->query('per_page', 15); // Resultados por página (default 15)

        // Llamada al servicio para obtener los comprobantes filtrados y paginados
        $vouchers = $this->voucherService->getVouchers($filters, $page, $perPage);

        // Retornar los comprobantes utilizando el recurso para formatear la respuesta
        return VoucherResource::collection($vouchers);
    }
}
