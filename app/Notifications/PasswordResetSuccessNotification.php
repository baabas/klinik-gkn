<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetSuccessNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Password Anda Telah Berhasil Direset')
                    ->greeting('Halo, ' . $notifiable->name . '!')
                    ->line('Password Anda telah berhasil direset.')
                    ->line('Jika Anda tidak melakukan reset password, segera hubungi administrator untuk mengamankan akun Anda.')
                    ->line('Detail reset password:')
                    ->line('Waktu: ' . now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') . ' WIB')
                    ->line('Email: ' . $notifiable->email)
                    ->action('Login ke Sistem', url('/login'))
                    ->line('Terima kasih telah menggunakan sistem Klinik GKN.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
