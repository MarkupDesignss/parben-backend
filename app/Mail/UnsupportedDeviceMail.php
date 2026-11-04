<?php

namespace App\Mail;

use App\Models\UnsupportedDeviceReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UnsupportedDeviceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public UnsupportedDeviceReport $report
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Unsupported Device Report — '
                . $this->report->router_brand . ' '
                . $this->report->router_model
                . ' (#' . $this->report->id . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.unsupported-device',
        );
    }
}