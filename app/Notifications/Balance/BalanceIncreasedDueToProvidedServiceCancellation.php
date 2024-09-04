<?php

declare(strict_types=1);

namespace App\Notifications\Balance;

use App\Enums\NotifierType;
use App\Models\Balance;
use App\Models\Person;
use App\Models\ProvidedService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\WithoutRelations;
use NotificationChannels\Telegram\TelegramMessage;

class BalanceIncreasedDueToProvidedServiceCancellation extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        #[WithoutRelations]
        private readonly Balance $balance,
        #[WithoutRelations]
        private readonly ProvidedService $providedService
    ) {
        $this->afterCommit();
    }

    /**
     * Determine if the notification should be sent.
     * @psalm-api
     */
    public function shouldSend(Person $notifiable): bool
    {
        return match ($notifiable->notifier_type) {
            NotifierType::EMAIL => true,
            NotifierType::TELEGRAM => $notifiable->joinedToChatbot(),
            default => false
        };
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(Person $notifiable): array
    {
        return match ($notifiable->notifier_type) {
            NotifierType::EMAIL => ['mail'],
            NotifierType::TELEGRAM => ['telegram'],
            default => []
        };
    }

    /**
     * Get the mail representation of the notification.
     * @psalm-api
     */
    public function toMail(): MailMessage
    {
        return (new MailMessage())
            ->subject(__('Provision of services under an insurance contract'))
            ->greeting(__('Hello') . '!')
            ->line(__('The service provided was canceled') . ':')
            ->line(__('Date of service') . ': ' . $this->providedService->date_of_service->isoFormat('D MMMM YYYY'))
            ->line(__('Service name') . ': ' . $this->providedService->service_name)
            ->line(__('Quantity') . ': ' . $this->providedService->quantity)
            ->line(__('Price') . ': ' . $this->providedService->price)
            ->line(__('Amount') . ': ' . $this->providedService->amount)
            ->line('')
            ->line(__('Remaining service balance') . ': ' . $this->balance->balance);
    }

    /**
     * @psalm-api
     */
    public function toTelegram()
    {
        return TelegramMessage::create()
            ->content(__('Provision of services under an insurance contract'))
            ->line('')
            ->line(__('Hello') . '!')
            ->line(__('The service provided was canceled') . ':')
            ->line(__('Date of service') . ': ' . $this->providedService->date_of_service->isoFormat('D MMMM YYYY'))
            ->line(__('Service name') . ': ' . $this->providedService->service_name)
            ->line(__('Quantity') . ': ' . $this->providedService->quantity)
            ->line(__('Price') . ': ' . $this->providedService->price)
            ->line(__('Amount') . ': ' . $this->providedService->amount)
            ->line('')
            ->line(__('Remaining service balance') . ': ' . $this->balance->balance);
    }
}
