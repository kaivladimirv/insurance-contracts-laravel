<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\SerializeDate;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @method static create(mixed $validated)
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $number
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property float $max_amount
 * @property Company $company
 */
class Contract extends Model
{
    use HasFactory;
    use SoftDeletes;
    use SerializeDate;

    protected $fillable = [
        'number',
        'start_date',
        'end_date',
        'max_amount'
    ];
    protected $hidden = ['company_id'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'start_date' => 'datetime:Y-m-d',
        'end_date' => 'datetime:Y-m-d',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isExpiredTo(DateTimeImmutable $date): bool
    {
        $endDate = Carbon::parse($this->end_date);
        $checkDate = Carbon::parse($date);

        return $checkDate->greaterThan($endDate);
    }

    public function isIncludeInValidityPeriod(DateTimeImmutable $date): bool
    {
        $checkDate = Carbon::parse($date);

        return $checkDate->between($this->start_date, $this->end_date);
    }

    public function providedServices(): HasMany
    {
        return $this->hasMany(ProvidedService::class);
    }
}
