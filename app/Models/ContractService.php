<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LimitType;
use App\Models\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property LimitType $limit_type
 * @property float $limit_value
 * @property int $contract_id
 * @property int $service_id
 * @property Contract $contract
 * @property Service $service
 */
class ContractService extends Model
{
    use HasFactory;
    use SerializeDate;

    protected $fillable = [
        'limit_type',
        'limit_value',
        'service_id'
    ];
    protected $hidden = ['contract_id'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'limit_type' => LimitType::class,
        'limit_value' => 'double'
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
