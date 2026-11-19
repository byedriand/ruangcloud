<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Email tautan reset kata sandi berbahasa Indonesia, dikirim oleh
 * broker kata sandi Laravel (lihat User::sendPasswordResetNotification).
 * Di Docker lokal, email ini tertangkap Mailpit (http://localhost:8025).
 */
class ResetPasswordNotification extends Notification
{
    public function __construct(public string $token)
    {
    }

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ]);

        return (new MailMessage)
            ->subject('Atur Ulang Kata Sandi RuangCloud')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun RuangCloud kamu.')
            ->action('Atur Ulang Kata Sandi', $url)
            ->line('Tautan ini berlaku selama 60 menit dan hanya dapat dipakai satu kali.')
            ->line('Jika kamu tidak meminta pengaturan ulang ini, abaikan saja email ini -- kata sandi kamu tidak akan berubah.');
    }
}
