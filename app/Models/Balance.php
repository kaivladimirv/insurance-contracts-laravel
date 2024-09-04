<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LimitType;
use App\Models\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

/**
 * @property int $service_id
 * @property LimitType $limit_type
 * @property float $balance
 * @property InsuredPerson $insuredPerson
 * @method static Builder byContractAndService(int $contract_id, int $service_id)
 */
class Balance extends Model
{
    use HasFactory;
    use SerializeDate;

    protected $fillable = [
        'service_id',
        'limit_type',
        'balance'
    ];

    protected $casts = [
        'limit_type' => LimitType::class,
        'balance' => 'double'
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function insuredPerson(): BelongsTo
    {
        return $this->belongsTo(InsuredPerson::class);
    }

    /**
     * @psalm-api
     */
    public function scopeByContractAndService(Builder $query, int $contractId, int $serviceId): Builder
    {
        return $query->where('contract_id', '=', $contractId)
            ->where('service_id', '=', $serviceId);
    }

    public function add(float $value): void
    {
        $this->assertValueIsGreaterThanZero($value);

        $this->balance += $value;
    }

    public function subtract(float $value): void
    {
        $this->assertValueIsGreaterThanZero($value);

        $this->balance -= $value;
    }

    private function assertValueIsGreaterThanZero(float $value): void
    {
        if ($value <= 0) {
            throw new InvalidArgumentException(__('Value must be greater than zero'));
        }
    }
}
