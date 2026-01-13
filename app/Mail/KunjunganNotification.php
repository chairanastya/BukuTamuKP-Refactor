<?php

namespace App\Mail;

use App\Models\Karyawan;
use App\Models\Kunjungan;
use App\Models\Tamu;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KunjunganNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $tamu;
    public $karyawan;
    public $kunjungan;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct(Tamu $tamu, Karyawan $karyawan, Kunjungan $kunjungan, string $status)
    {
        $this->tamu = $tamu;
        $this->karyawan = $karyawan;
        $this->kunjungan = $kunjungan;
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->status === 'diterima' 
            ? 'Kunjungan Anda Diterima - Buku Tamu Digital'
            : 'Kunjungan Tidak Dapat Diterima - Buku Tamu Digital';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.kunjungan-notification',
            with: [
                'tamu' => $this->tamu,
                'karyawan' => $this->karyawan,
                'kunjungan' => $this->kunjungan,
                'status' => $this->status,
            ]
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
