<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordResetNotification extends Notification
{
    protected $code;
    protected $ip;
    protected $browser;
    protected $os;
    protected $time;

    public function __construct($code, $ip, $browser, $os, $time)
    {
        $this->code = $code;
        $this->ip = $ip;
        $this->browser = $browser;
        $this->os = $os;
        $this->time = $time;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $fullname = $notifiable->fullname ?? 'User';
        $username = explode('@', $notifiable->routeNotificationFor('mail'))[0] ?? $fullname;

        $subject = 'Your Password Reset Code';

        // Get the custom email template from global settings
        $emailTemplate = gs('email_template');

        // Build the email body with shortcodes
        $emailBody = $this->replaceShortcodes($emailTemplate, [
            'site_name' => gs('site_name'),
            'site_url' => url('/'),
            'fullname' => $fullname,
            'username' => $username,
            'subject' => $subject,
            'message' => "Your password reset verification code is <strong>{$this->code}</strong>.<br><br>"
                . "Time: <strong>{$this->time}</strong><br>"
                . "If you did not request this, please ignore this email.",
        ]);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.custom_template', [
                'emailBody' => $emailBody,
                'subject' => $subject,
                'fullname' => $fullname,
                'username' => $username,
                'message' => $emailBody,
                'site_name' => gs('site_name'),
                'site_url' => url('/'),
            ]);
    }

    /**
     * Replace shortcodes in email template
     */
    private function replaceShortcodes($template, $data)
    {
        foreach ($data as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
            $template = str_replace('{{ ' . $key . ' }}', $value, $template);
        }

        return $template;
    }
}
