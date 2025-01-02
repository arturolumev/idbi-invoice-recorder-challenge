<?php

namespace App\Http\Controllers\Vouchers;

use App\Models\Voucher; // Asegúrate de que este sea el modelo correcto
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeleteVoucherHandler
{
    public function __invoke($id)
    {
        // Buscar el voucher por ID
        $voucher = Voucher::find($id);

        // Verificar si el voucher existe
        if (!$voucher) {
            return response()->json(['error' => 'Voucher no encontrado.'], 404);
        }

        // Verificar que el voucher pertenece al usuario autenticado
        if ($voucher->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado para eliminar este voucher.'], 403);
        }

        // Eliminar el voucher
        $voucher->delete();

        return response()->json(['mensaje' => 'Voucher eliminado exitosamente.'], 200);
    }
}
