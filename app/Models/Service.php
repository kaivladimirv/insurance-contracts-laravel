<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use App\Models\Traits\SerializeDate;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Company $company
 */
#[ScopedBy(CompanyScope::class)]
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;
    use SerializeDate;

    protected $fillable = ['name'];
    protected $hidden = ['company_id'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contractServices(): HasMany
    {
        return $this->hasMany(ContractService::class);
    }
}
