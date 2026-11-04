<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Help;
use Illuminate\Mail\Mailables\Attachment;

class HelpRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $help;
    public $originalFileName;
    public $attachmentPath;

    /**
     * Create a new message instance.
     */
    public function __construct($help, $attachmentPath = null, $originalFileName = null)
    {
        $this->help = $help;
        $this->attachmentPath = $attachmentPath;
        $this->originalFileName = $originalFileName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Help Request Submitted - ' . $this->help->name,
            from: config('mail.from.address', 'noreply@yourdomain.com'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.help-request',
            with: [
                'help' => $this->help,
                'originalFileName' => $this->originalFileName,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $attachments = [];

        if ($this->attachmentPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->attachmentPath)) {
            $filePath = \Illuminate\Support\Facades\Storage::disk('public')->path($this->attachmentPath);
            $attachments[] = Attachment::fromPath($filePath)
                ->as($this->originalFileName ?? basename($this->attachmentPath))
                ->withMime(\Illuminate\Support\Facades\Storage::disk('public')->mimeType($this->attachmentPath));
        }

        return $attachments;
    }
}