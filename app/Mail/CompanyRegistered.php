<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Queue\SerializesModels;

class CompanyRegistered extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(#[WithoutRelations] protected readonly Company $company)
    {
    }

    /**
     * Get the message envelope.
     * @psalm-api
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Company Registered'),
        );
    }

    /**
     * Get the message content definition.
     * @psalm-api
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.company.registered',
            with: [
                'company' => $this->company
            ],
        );
    }
}
