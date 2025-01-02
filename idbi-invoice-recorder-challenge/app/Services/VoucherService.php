<?php

namespace App\Services;

use App\Events\Vouchers\VouchersCreated;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use SimpleXMLElement;

class VoucherService
{
    /**
     * Obtener los comprobantes filtrados y paginados.
     *
     * @param array $filters
     * @param int $page
     * @param int $paginate
     * @return LengthAwarePaginator
     */
    public function getVouchers(array $filters, int $page, int $paginate): LengthAwarePaginator
    {
        // Inicializar la consulta base
        $query = Voucher::with(['lines', 'user'])
                        ->where('user_id', auth()->id())  // Solo comprobantes del usuario autenticado
                        ->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]); // Filtro obligatorio de fechas

        // Filtros opcionales
        if (!empty($filters['serie'])) {
            $query->where('serie', 'like', '%' . $filters['serie'] . '%');
        }
        if (!empty($filters['numero'])) {
            $query->where('numero', 'like', '%' . $filters['numero'] . '%');
        }
        if (!empty($filters['tipo'])) {
            $query->where('tipo', 'like', '%' . $filters['tipo'] . '%');
        }
        if (!empty($filters['moneda'])) {
            $query->where('moneda', 'like', '%' . $filters['moneda'] . '%');
        }

        // Obtener el total de registros
        $total = $query->count();

        // Asegurarse de que la página no sea mayor que el total de páginas disponibles
        $maxPages = (int) ceil($total / $paginate);
        $page = min($page, $maxPages); // Limitar la página a la última página disponible

        // Aplicar la paginación y retornar los resultados
        return $query->paginate($paginate, ['*'], 'page', $page);
    }

    /**
     * @param string[] $xmlContents
     * @param User $user
     * @return Voucher[]
     */
    public function storeVouchersFromXmlContents(array $xmlContents, User $user): array
    {
        $vouchers = [];
        foreach ($xmlContents as $xmlContent) {
            $vouchers[] = $this->storeVoucherFromXmlContent($xmlContent, $user);
        }

        VouchersCreated::dispatch($vouchers, $user);

        return $vouchers;
    }

    public function storeVoucherFromXmlContent(string $xmlContent, User $user): Voucher
    {
        $xml = new SimpleXMLElement($xmlContent);

        $issuerName = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name')[0];
        $issuerDocumentType = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $issuerDocumentNumber = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $receiverName = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0];
        $receiverDocumentType = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $receiverDocumentNumber = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $totalAmount = (string) $xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount')[0];

        // Extraer información adicional
        $id = (string) $xml->xpath('//cbc:ID')[0];
        // Dividir el valor en serie y número correlativo
        list($serie, $numero) = explode('-', $id);
        $tipo = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0];
        $moneda = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0];

        $voucher = new Voucher([
            'issuer_name' => $issuerName,
            'issuer_document_type' => $issuerDocumentType,
            'issuer_document_number' => $issuerDocumentNumber,
            'receiver_name' => $receiverName,
            'receiver_document_type' => $receiverDocumentType,
            'receiver_document_number' => $receiverDocumentNumber,
            'total_amount' => $totalAmount,
            'serie' => $serie,
            'numero' => $numero,
            'tipo' => $tipo,
            'moneda' => $moneda,
            'xml_content' => $xmlContent,
            'user_id' => $user->id,
        ]);
        $voucher->save();

        foreach ($xml->xpath('//cac:InvoiceLine') as $invoiceLine) {
            $name = (string) $invoiceLine->xpath('cac:Item/cbc:Description')[0];
            $quantity = (float) $invoiceLine->xpath('cbc:InvoicedQuantity')[0];
            $unitPrice = (float) $invoiceLine->xpath('cac:Price/cbc:PriceAmount')[0];

            $voucherLine = new VoucherLine([
                'name' => $name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'voucher_id' => $voucher->id,
            ]);

            $voucherLine->save();
        }

        return $voucher;
    }

    // OBTENER MONTOS TOTALES ACUMULADOS POR MONEDA
    public function getMontosPorMoneda(string $userId): array
    {
        // Consultar los montos totales por moneda para el usuario
        $totales = Voucher::where('user_id', $userId)
            ->selectRaw('moneda, sum(total_amount) as total')
            ->groupBy('moneda')
            ->get();

        // Formatear los resultados
        return [
            'PEN' => $totales->where('moneda', 'PEN')->sum('total'),
            'USD' => $totales->where('moneda', 'USD')->sum('total'),
        ];
    }
}
