<?php

declare(strict_types=1);

namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Mail — PHPMailer Wrapper
 */
final class Mail
{
    /**
     * Send an email.
     *
     * @param string $to
     * @param string $subject
     * @param string $body
     * @return bool
     */
    public static function send(string $to, string $subject, string $body): bool
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = 2; // Enable verbose debug output
            $mail->Debugoutput = function($str, $level) {
                $logFile = STORAGE_PATH . '/logs/app.log';
                $entry = sprintf("[%s] SMTP DEBUG: %s\n", date('Y-m-d H:i:s'), trim($str));
                file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
            };

            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST', 'localhost');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
            $mail->Port       = (int) env('MAIL_PORT', 587);

            // Recipients
            $mail->setFrom(env('MAIL_FROM_ADDRESS', 'noreply@example.com'), env('MAIL_FROM_NAME', 'App'));
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            return $mail->send();
        } catch (Exception $e) {
            $logFile = STORAGE_PATH . '/logs/app.log';
            $entry = sprintf("[%s] MAIL ERROR: %s\n", date('Y-m-d H:i:s'), $mail->ErrorInfo);
            file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
            return false;
        }
    }
}
