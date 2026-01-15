<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OTPMail extends Mailable
{
    use Queueable, SerializesModels;

    // 1. KITA TAMBAHKAN VARIABLE INI
    // Agar kode OTP bisa dibawa ke tampilan email
    public $otp;

    /**
     * Create a new message instance.
     */
    // 2. KITA UBAH CONSTRUCT MENERIMA INPUT $otp
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            // 3. UBAH SUBJEK EMAIL BIAR KEREN
            subject: 'Kode Login OTP Anda',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            // 4. PENTING: ARAHKAN KE FILE VIEW YANG AKAN KITA BUAT
            // Jangan 'view.name', tapi 'emails.otp'
            view: 'emails.otp',
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