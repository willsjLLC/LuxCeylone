<?php

// app/Notifications/TestMailNotification.php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TestMailNotification extends Notification
{
    protected $subject;
    protected $message;
    protected $fullname;

    public function __construct($subject, $message, $fullname = 'User')
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->fullname = $fullname;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Get the custom email template from global settings
        $emailTemplate = gs('email_template');
        
        $emailBody = $this->replaceShortcodes($emailTemplate, [
            'site_name' => gs('site_name'),
            'site_url' => url('/'),
            'fullname' => $this->fullname,
            'username' => explode('@', $notifiable->routeNotificationFor('mail'))[0] ?? $this->fullname,
            'message' => $this->message,
            'subject' => $this->subject,
        ]);

        return (new MailMessage)
            ->subject($this->subject)
            ->view('emails.custom_template', [
                'emailBody' => $emailBody,
                'subject' => $this->subject,
                'fullname' => $this->fullname,
                'username' => explode('@', $notifiable->routeNotificationFor('mail'))[0] ?? $this->fullname,
                'message' => $this->message,
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