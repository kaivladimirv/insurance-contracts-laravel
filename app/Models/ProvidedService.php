<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LimitType;
use App\Models\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $date_of_service
 * @property string $service_name
 * @property float $amount
 * @property float $quantity
 * @property float $price
 * @property int $service_id
 * @property LimitType $limit_type
 * @property int $insured_person_id
 * @property int $contract_id
 * @property Contract $contract
 * @property InsuredPerson $insuredPerson
 */
class ProvidedService extends Model
{
    use HasFactory;
    use SoftDeletes;
    use SerializeDate;

    protected $fillable = [
        'date_of_service',
        'service_id',
        'service_name',
        'limit_type',
        'quantity',
        'price',
        'amount'
    ];
    protected $hidden = [
        'company_id',
        'updated_at',
        'deleted_at'
    ];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'contract_id' => 'integer',
        'service_id' => 'integer',
        'date_of_service' => 'date:Y-m-d',
        'limit_type' => LimitType::class,
        'quantity' => 'double',
        'price' => 'double',
        'amount' => 'double'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function insuredPerson(): BelongsTo
    {
        return $this->belongsTo(InsuredPerson::class);
    }

    public function recalcAmount(): void
    {
        $this->amount = $this->quantity * $this->price;
    }

    public function getValue(): float
    {
        return $this->limit_type->isItQuantityLimiter() ? $this->quantity : $this->amount;
    }
}
