<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use Illuminate\Support\Facades\Validator;

/**
 * @property string $id
 * @property string $issuer_name
 * @property string $issuer_document_type
 * @property string $issuer_document_number
 * @property string $receiver_name
 * @property string $receiver_document_type
 * @property string $receiver_document_number
 * @property float $total_amount
 * @property float $serie
 * @property float $numero
 * @property float $tipo
 * @property float $moneda
 * @property string $xml_content
 * @property string $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $user
 * @property-read Collection|User[] $lines
 * @mixin Builder
 */
class Voucher extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'issuer_name',
        'issuer_document_type',
        'issuer_document_number',
        'receiver_name',
        'receiver_document_type',
        'receiver_document_number',
        'total_amount',
        'serie',
        'numero',
        'tipo',
        'moneda',
        'xml_content',
        'user_id',
    ];

    protected $casts = [
        'total_amount' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(VoucherLine::class);
    }

    /**
     * Valida y guarda el comprobante.
     *
     * @throws \Exception Si la validación falla.
     */
    public function validateAndSave(): void
    {
        // Definir reglas de validación
        $rules = [
            'issuer_name' => 'required|string|max:255',
            'issuer_document_type' => 'required|string|max:10',
            'issuer_document_number' => 'required|string|max:20',
            'receiver_name' => 'required|string|max:255',
            'receiver_document_type' => 'required|string|max:10',
            'receiver_document_number' => 'required|string|max:20',
            'total_amount' => 'required|numeric|min:0',
            'serie' => 'required|string|max:10',
            'numero' => 'required|string|max:20',
            'tipo' => 'required|string|max:10',
            'moneda' => 'required|string|max:10',
            'xml_content' => 'required|string',
        ];

        // Validar los datos actuales del modelo
        $validator = Validator::make($this->toArray(), $rules);

        if ($validator->fails()) {
            // Lanzar una excepción con los errores de validación
            throw new \Exception("Errores de validación: " . implode(', ', $validator->errors()->all()));
        }

        // Guardar el modelo
        $this->save();
    }
}
