<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $contract_id
 * @property int $person_id
 * @property string $policy_number
 * @property bool $is_allowed_to_exceed_limit
 * @property Contract $contract
 * @property Person $person
 */
class InsuredPerson extends Model
{
    use HasFactory;
    use SerializeDate;

    protected $table = 'insured_persons';
    protected $fillable = [
        'person_id',
        'policy_number',
        'is_allowed_to_exceed_limit'
    ];
    protected $hidden = ['contract_id'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function providedServices(): HasMany
    {
        return $this->hasMany(ProvidedService::class);
    }
}
