<?php

declare(strict_types=1);

namespace App\Notifications\Person;

use App\Enums\NotifierType;
use App\Models\Person;
use App\Services\Telegram\InvitationLinkToJoinChatbot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationToJoinChatBot extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->afterCommit();
    }

    /**
     * Determine if the notification should be sent.
     * @psalm-api
     */
    public function shouldSend(Person $notifiable): bool
    {
        return (($notifiable->notifier_type === NotifierType::TELEGRAM)
            and ($notifiable->email !== null)
            and $notifiable->telegram_chat_invite_token
            and !$notifiable->joinedToChatbot());
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     * @psalm-api
     */
    public function toMail(Person $notifiable): MailMessage
    {
        $invitationLink = InvitationLinkToJoinChatbot::createFromPerson($notifiable);

        return (new MailMessage())
            ->subject(__('Invitation to Telegram ChatBot'))
            ->greeting(__('Hello') . '!')
            ->line(__('Join InsuranceContract ChatBot on Telegram') . '.')
            ->action('Join', $invitationLink);
    }

    /**
     * @psalm-api
     */
    public function onSent(Person $notifiable): void
    {
        $notifiable->markThatInviteToJoinChatBotHasBeenSent(now());
        $notifiable->save();
    }
}
