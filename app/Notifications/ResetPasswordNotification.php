<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Restablecer contraseña — SIGPA')
            ->greeting('Hola, ' . $notifiable->name)
            ->line('Recibimos una solicitud para restablecer la contraseña de tu cuenta.')
            ->action('Restablecer contraseña', $url)
            ->line('Este enlace expirará en ' . config('auth.passwords.users.expire') . ' minutos.')
            ->line('Si no solicitaste este cambio, puedes ignorar este correo.')
            ->salutation('— SIGPA · Sistema Integral de Gestión de Préstamos Académicos');
    }
}
