<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    /**
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): MailMessage|Mailable
    {
        if (static::$toMailCallback !== null) {
            return parent::toMail($notifiable);
        }

        $viewData = [
            'resetUrl' => $this->resetUrl($notifiable),
            'expiresInMinutes' => config(
                'auth.passwords.'.config('auth.defaults.passwords').'.expire',
            ),
        ];

        return (new MailMessage)
            ->from(config('mail.from.address'), 'Catbalogan City Community College (CCCC)')
            ->subject('Reset your CCCC Student Checklist password')
            ->view('mail.password-reset', $viewData)
            ->text('mail.password-reset-text', $viewData);
    }
}
