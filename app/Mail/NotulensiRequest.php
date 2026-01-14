<?php

namespace App\Mail;

use App\Models\Karyawan;
use App\Models\Kunjungan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotulensiRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $karyawan;
    public $kunjungan;
    public $token;

    /**
     * Create a new message instance.
     */
    public function __construct(Karyawan $karyawan, Kunjungan $kunjungan, string $token)
    {
        $this->karyawan = $karyawan;
        $this->kunjungan = $kunjungan;
        $this->token = $token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permintaan Pengisian Notulensi Rapat - Buku Tamu Digital',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notulensi-request',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
