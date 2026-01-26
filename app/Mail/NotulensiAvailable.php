<?php

namespace App\Mail;

use App\Models\Kunjungan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotulensiAvailable extends Mailable
{
    use Queueable, SerializesModels;

    public $namaPenerima;
    public $kunjungan;
    public $token;

    /**
     * Create a new message instance.
     * $namaPenerima bisa berisi nama tamu atau nama karyawan
     */
    public function __construct(string $namaPenerima, Kunjungan $kunjungan, string $token)
    {
        $this->namaPenerima = $namaPenerima;
        $this->kunjungan = $kunjungan;
        $this->token = $token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notulensi Rapat Tersedia - Buku Tamu Digital',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notulensi-view',
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
