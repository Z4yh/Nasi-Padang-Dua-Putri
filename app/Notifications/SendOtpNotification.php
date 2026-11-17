<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOtpNotification extends Notification
{
    use Queueable;

    public function __construct(public string $otp) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Kode Verifikasi OTP - RM Padang Tiga Putri')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Kami menerima permintaan perubahan kata sandi pada akun Anda.')
            ->line('Gunakan 6 digit kode OTP berikut untuk menyelesaikan verifikasi:')
            ->line('## ' . $this->otp)
            ->line('Kode ini hanya berlaku selama 5 menit. Jangan berikan kode ini kepada siapa pun demi keamanan akun Anda.')
            ->salutation('Salam hormat, RM Padang Tiga Putri');
    }
}
