<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;
use Override;

/**
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $name
 * @property string $email
 * @property string $password_hash
 * @property string $email_confirm_token
 * @property bool $is_email_confirmed
 * @property int $id
 * @property string $new_email
 * @property string $new_email_confirm_token
 * @method static create(array $array)
 */
class Company extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SerializeDate;

    protected $fillable = [
        'name',
    ];
    protected $hidden = [
        'password_hash',
        'email_confirm_token'
    ];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    #[Override]
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function createAccessToken(): NewAccessToken
    {
        return $this->createToken(
            'access_token',
            ['*'],
            now()->addMinutes(env('API_TOKEN_EXPIRATION_MINUTES'))
        );
    }

    public function isPasswordMatch(string $password): bool
    {
        return Hash::check($password, $this->password_hash);
    }
}
