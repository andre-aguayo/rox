<?php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    private string $resetUrl;

    public function __construct(string $resetUrl)
    {
        $this->resetUrl = $resetUrl;
    }

    public function build(): self
    {
        return $this->subject('Password Reset Request')
            ->view('emails.password_reset')
            ->with([
                'resetUrl' => $this->resetUrl,
            ]);
    }
}
