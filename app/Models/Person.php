<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NotifierType;
use App\Enums\TelegramChatStatus;
use App\Models\Traits\SerializeDate;
use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $last_name
 * @property string $first_name
 * @property string $middle_name
 * @property string $email
 * @property string $phone_number
 * @property ?NotifierType $notifier_type
 * @property string $telegram_chat_invite_token
 * @property Carbon $telegram_invite_date_for_chat
 * @property ?TelegramChatStatus $telegram_chat_status
 * @property string $telegram_chat_id
 */
class Person extends Model
{
    use HasFactory;
    use SerializeDate;
    use Notifiable;

    protected $table = 'persons';
    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'email',
        'phone_number',
        'notifier_type',
    ];
    protected $hidden = ['company_id'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'notifier_type' => NotifierType::class,
        'telegram_invite_date_for_chat' => 'datetime:Y-m-d H:i:s',
        'telegram_chat_status' => TelegramChatStatus::class
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @psalm-api
     */
    public function insuredPersons(): HasMany
    {
        return $this->hasMany(InsuredPerson::class);
    }

    public function getFullName(): string
    {
        return Arr::join([$this->last_name, $this->first_name, $this->middle_name], ' ');
    }

    /**
     * @psalm-api
     */
    public function routeNotificationForTelegram(): string
    {
        return $this->telegram_chat_id;
    }

    public function inviteToJoinChatBotHasBeenSent(): bool
    {
        return $this->telegram_chat_status !== null;
    }

    public function markThatInviteToJoinChatBotHasBeenSent(Carbon $dateTime): void
    {
        if (!$this->telegram_chat_invite_token) {
            throw new DomainException(__('The invitation token to join the chatbot is not installed'));
        }

        $this->telegram_invite_date_for_chat = $dateTime;
        $this->telegram_chat_status = TelegramChatStatus::INVITATION_SENT;
    }

    /**
     * @psalm-api
     */
    public function joinToChatbot(string $chatId): void
    {
        $this->telegram_chat_status = TelegramChatStatus::JOINED;
        $this->telegram_chat_id = $chatId;
    }

    /**
     * @psalm-api
     */
    public function leaveChatbot(): void
    {
        $this->telegram_chat_status = TelegramChatStatus::LEAVE;
        $this->telegram_chat_id = null;
    }

    public function joinedToChatbot(): bool
    {
        return ($this->telegram_chat_status === TelegramChatStatus::JOINED);
    }
}
