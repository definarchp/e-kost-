<?php

namespace App\Models;

use Database\Factories\BillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bill extends Model
{
    /** @use HasFactory<BillFactory> */
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'bill_month',
        'amount',
        'status',
        'va_number',
        'bukti_pembayaran_path',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
        ];
    }

    public function isActiveForTenant(): bool
    {
        return in_array($this->status, ['belum_dibayar', 'menunggu_verifikasi'], true);
    }


    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
