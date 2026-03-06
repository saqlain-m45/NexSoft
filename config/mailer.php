<?php
/**
 * NexSoft Hub — SMTP Email Configuration & Mailer
 * Configure your SMTP credentials below.
 * Uses PHP's native stream socket — no external libraries needed.
 */

// ── SMTP Credentials ──────────────────────────────────────────────
define('SMTP_HOST',     'smtp.gmail.com');   // e.g. smtp.gmail.com
define('SMTP_PORT',     587);                // 587 = TLS, 465 = SSL
define('SMTP_SECURE',   'tls');              // 'tls' or 'ssl'
define('SMTP_USERNAME', 'your@gmail.com');   // Your email address
define('SMTP_PASSWORD', 'your_app_password');// App password (not login password)
define('SMTP_FROM_NAME','NexSoft Hub');
define('SMTP_FROM_EMAIL','your@gmail.com');
// ──────────────────────────────────────────────────────────────────

/**
 * Send an HTML email via SMTP (PHP native sockets — no PHPMailer needed)
 */
function sendMail(string $toEmail, string $toName, string $subject, string $htmlBody): bool
{
    $from    = SMTP_FROM_EMAIL;
    $fromName= SMTP_FROM_NAME;

    // Build raw email
    $boundary = md5(uniqid());
    $headers  = [
        "MIME-Version: 1.0",
        "Content-Type: text/html; charset=UTF-8",
        "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$from}>",
        "To: =?UTF-8?B?"   . base64_encode($toName)   . "?= <{$toEmail}>",
        "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
        "X-Mailer: NexSoft-Hub/1.0",
    ];

    // If SMTP credentials not configured, fall back to PHP mail()
    if (SMTP_USERNAME === 'your@gmail.com' || SMTP_PASSWORD === 'your_app_password') {
        $headerStr = implode("\r\n", $headers);
        return mail($toEmail, $subject, $htmlBody, $headerStr);
    }

    try {
        // Connect
        $ssl = (SMTP_SECURE === 'ssl') ? 'ssl://' : '';
        $socket = fsockopen($ssl . SMTP_HOST, SMTP_PORT, $errno, $errstr, 10);
        if (!$socket) throw new \RuntimeException("SMTP connect failed: $errstr ($errno)");

        $read = fgets($socket, 515);

        // EHLO
        fputs($socket, "EHLO " . gethostname() . "\r\n");
        while ($line = fgets($socket, 515)) { if ($line[3] === ' ') break; }

        // STARTTLS
        if (SMTP_SECURE === 'tls') {
            fputs($socket, "STARTTLS\r\n");
            fgets($socket, 515);
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            fputs($socket, "EHLO " . gethostname() . "\r\n");
            while ($line = fgets($socket, 515)) { if ($line[3] === ' ') break; }
        }

        // Auth
        fputs($socket, "AUTH LOGIN\r\n"); fgets($socket, 515);
        fputs($socket, base64_encode(SMTP_USERNAME) . "\r\n"); fgets($socket, 515);
        fputs($socket, base64_encode(SMTP_PASSWORD) . "\r\n");
        $authResp = fgets($socket, 515);
        if (substr($authResp, 0, 3) !== '235') throw new \RuntimeException("SMTP auth failed: $authResp");

        // Send
        fputs($socket, "MAIL FROM: <{$from}>\r\n"); fgets($socket, 515);
        fputs($socket, "RCPT TO: <{$toEmail}>\r\n"); fgets($socket, 515);
        fputs($socket, "DATA\r\n"); fgets($socket, 515);

        $msg  = implode("\r\n", $headers) . "\r\n\r\n" . $htmlBody;
        fputs($socket, $msg . "\r\n.\r\n");
        $resp = fgets($socket, 515);

        fputs($socket, "QUIT\r\n");
        fclose($socket);

        return substr($resp, 0, 3) === '250';

    } catch (\Throwable $e) {
        error_log("NexSoft SMTP error: " . $e->getMessage());
        return false;
    }
}

/**
 * Email template: Application Approved
 */
function emailTemplateApproved(string $name): string
{
    return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>
  body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;margin:0;padding:0;}
  .wrap{max-width:560px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);}
  .header{background:linear-gradient(135deg,#0B1F3B,#162d4f);padding:40px;text-align:center;}
  .logo{color:#0EA5A4;font-size:1.4rem;font-weight:900;letter-spacing:1px;}
  .badge{display:inline-block;background:rgba(14,165,164,0.15);border:1px solid rgba(14,165,164,0.3);color:#0EA5A4;padding:6px 18px;border-radius:50px;font-size:0.8rem;font-weight:700;margin-top:12px;}
  .body{padding:40px 32px;}
  .icon{font-size:3rem;text-align:center;margin-bottom:16px;}
  h1{color:#0B1F3B;font-size:1.5rem;margin:0 0 12px;text-align:center;}
  p{color:#6b7280;font-size:0.95rem;line-height:1.7;margin:0 0 16px;}
  .btn{display:block;width:fit-content;margin:24px auto;background:linear-gradient(135deg,#0EA5A4,#0a8f8e);color:white;text-decoration:none;padding:14px 36px;border-radius:50px;font-weight:700;font-size:0.95rem;}
  .footer{background:#f8fafc;padding:20px;text-align:center;font-size:0.78rem;color:#9ca3af;border-top:1px solid #e2e8f0;}
</style></head>
<body>
<div class="wrap">
  <div class="header">
    <div class="logo">NexSoft Hub</div>
    <div class="badge">🎉 Application Approved</div>
  </div>
  <div class="body">
    <div class="icon">✅</div>
    <h1>Congratulations, {$name}!</h1>
    <p>We are thrilled to inform you that your application to join the <strong>NexSoft Hub</strong> team has been reviewed and <strong style="color:#0EA5A4;">approved</strong>.</p>
    <p>Our team will reach out to you shortly with the next steps regarding your onboarding process. Please keep an eye on your inbox.</p>
    <p>We excited to have you on board and look forward to building amazing things together! 🚀</p>
    <a href="http://localhost/NexSoft/" class="btn">Visit NexSoft Hub</a>
  </div>
  <div class="footer">
    © 2025 NexSoft Hub · hello@nexsofthub.com<br>
    This email was sent because you applied to join our team.
  </div>
</div>
</body></html>
HTML;
}

/**
 * Email template: Application Rejected
 */
function emailTemplateRejected(string $name): string
{
    return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>
  body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;margin:0;padding:0;}
  .wrap{max-width:560px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);}
  .header{background:linear-gradient(135deg,#0B1F3B,#162d4f);padding:40px;text-align:center;}
  .logo{color:#0EA5A4;font-size:1.4rem;font-weight:900;letter-spacing:1px;}
  .badge{display:inline-block;background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);color:#f87171;padding:6px 18px;border-radius:50px;font-size:0.8rem;font-weight:700;margin-top:12px;}
  .body{padding:40px 32px;}
  .icon{font-size:3rem;text-align:center;margin-bottom:16px;}
  h1{color:#0B1F3B;font-size:1.5rem;margin:0 0 12px;text-align:center;}
  p{color:#6b7280;font-size:0.95rem;line-height:1.7;margin:0 0 16px;}
  .btn{display:block;width:fit-content;margin:24px auto;background:linear-gradient(135deg,#0EA5A4,#0a8f8e);color:white;text-decoration:none;padding:14px 36px;border-radius:50px;font-weight:700;font-size:0.95rem;}
  .footer{background:#f8fafc;padding:20px;text-align:center;font-size:0.78rem;color:#9ca3af;border-top:1px solid #e2e8f0;}
</style></head>
<body>
<div class="wrap">
  <div class="header">
    <div class="logo">NexSoft Hub</div>
    <div class="badge">Application Status Update</div>
  </div>
  <div class="body">
    <div class="icon">💼</div>
    <h1>Thank You, {$name}</h1>
    <p>Thank you for your interest in joining the <strong>NexSoft Hub</strong> team and for taking the time to submit your application.</p>
    <p>After careful review, we regret to inform you that we are unable to move forward with your application at this time. This decision is not a reflection of your skills or potential.</p>
    <p>We encourage you to keep developing your skills and to apply again in the future. We wish you all the best in your career journey. 💪</p>
    <a href="http://localhost/NexSoft/" class="btn">Visit NexSoft Hub</a>
  </div>
  <div class="footer">
    © 2025 NexSoft Hub · hello@nexsofthub.com<br>
    This email was sent because you applied to join our team.
  </div>
</div>
</body></html>
HTML;
}
