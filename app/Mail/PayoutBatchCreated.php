<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PayoutBatchCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $batchId;
    public $merchantId;
    public $totalAmount;
    public $totalCount;

    /**
     * Create a new message instance.
     */
    public function __construct($batchId, $merchantId, $totalAmount, $totalCount)
    {
        $this->batchId = $batchId;
        $this->merchantId = $merchantId;
        $this->totalAmount = $totalAmount;
        $this->totalCount = $totalCount;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Payout Batch: {$this->batchId}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payout.batch',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
