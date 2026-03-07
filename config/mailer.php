<?php
/**
 * NexSoft Hub — SMTP Email Configuration & Mailer
 * Configure your SMTP credentials below.
 * Uses PHP's native stream socket — no external libraries needed.
 */

/**
 * Send an HTML email using SMTP (prefers SMTP, falls back to mail())
 */
function sendMail(string $toEmail, string $toName, string $subject, string $htmlBody): bool
{
  // Try to include database.php if not already there, to use getSetting()
  if (!function_exists('getSetting')) {
    @include_once __DIR__ . '/database.php';
  }

  // Fetch settings from database (with defaults)
  $host = function_exists('getSetting') ? getSetting('smtp_host', 'smtp.gmail.com') : 'smtp.gmail.com';
  $port = function_exists('getSetting') ? (int)getSetting('smtp_port', '587') : 587;
  $user = function_exists('getSetting') ? getSetting('smtp_user', '') : '';
  $pass = function_exists('getSetting') ? getSetting('smtp_pass', '') : '';
  $secure = function_exists('getSetting') ? getSetting('smtp_encryption', 'tls') : 'tls';
  $from = function_exists('getSetting') ? getSetting('site_email', 'hello@nexsofthub.com') : 'hello@nexsofthub.com';
  $fromName = function_exists('getSetting') ? getSetting('site_name', 'NexSoft Hub') : 'NexSoft Hub';

  // Build raw email headers
  $headers = [
    "MIME-Version: 1.0",
    "Content-Type: text/html; charset=UTF-8",
    "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$from}>",
    "To: =?UTF-8?B?" . base64_encode($toName) . "?= <{$toEmail}>",
    "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
    "X-Mailer: NexSoft-Hub/1.0",
  ];

  // If SMTP credentials not configured, fall back to PHP mail()
  if (empty($user) || empty($pass)) {
    $headerStr = implode("\r\n", $headers);
    return @mail($toEmail, $subject, $htmlBody, $headerStr);
  }

  try {
    // Connect
    $prefix = (strtolower($secure) === 'ssl') ? 'ssl://' : '';
    $socket = @fsockopen($prefix . $host, $port, $errno, $errstr, 10);
    if (!$socket)
      throw new \RuntimeException("SMTP connect failed: $errstr ($errno)");

    // Initial response
    fgets($socket, 515);

    // EHLO
    fputs($socket, "EHLO " . gethostname() . "\r\n");
    while ($line = fgets($socket, 515)) {
      if ($line[3] === ' ')
        break;
    }

    // STARTTLS
    if (strtolower($secure) === 'tls') {
      fputs($socket, "STARTTLS\r\n");
      fgets($socket, 515);
      stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
      fputs($socket, "EHLO " . gethostname() . "\r\n");
      while ($line = fgets($socket, 515)) {
        if ($line[3] === ' ')
          break;
      }
    }

    // Auth
    fputs($socket, "AUTH LOGIN\r\n");
    fgets($socket, 515);
    fputs($socket, base64_encode($user) . "\r\n");
    fgets($socket, 515);
    fputs($socket, base64_encode($pass) . "\r\n");
    $authResp = fgets($socket, 515);
    if (substr($authResp, 0, 3) !== '235')
      throw new \RuntimeException("SMTP auth failed: $authResp");

    // Send
    fputs($socket, "MAIL FROM: <{$from}>\r\n");
    fgets($socket, 515);
    fputs($socket, "RCPT TO: <{$toEmail}>\r\n");
    fgets($socket, 515);
    fputs($socket, "DATA\r\n");
    fgets($socket, 515);

    $msg = implode("\r\n", $headers) . "\r\n\r\n" . $htmlBody;
    fputs($socket, $msg . "\r\n.\r\n");
    $resp = fgets($socket, 515);

    fputs($socket, "QUIT\r\n");
    fclose($socket);

    return substr($resp, 0, 3) === '250';

  }
  catch (\Throwable $e) {
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
 * Email template: New Course Registration (Admin Notification)
 */
function emailTemplateCourseRegistrationAdmin(array $data, string $courseTitle): string
{
    $siteName = getSetting('site_name', 'NexSoft Hub');
    return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>
  body{font-family:'Segoe UI',sans-serif;background:#f8fafc;margin:0;padding:0;}
  .wrap{max-width:600px;margin:40px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.05);border:1px solid #e2e8f0;}
  .header{background:#0B1F3B;padding:30px;text-align:center;color:white;}
  .body{padding:40px;}
  h2{color:#0B1F3B;margin-top:0;}
  .info-table{width:100%;border-collapse:collapse;margin-top:20px;}
  .info-table td{padding:12px;border-bottom:1px solid #f1f5f9;font-size:0.9rem;}
  .label{font-weight:700;color:#64748b;width:120px;}
  .footer{background:#f8fafc;padding:20px;text-align:center;font-size:0.75rem;color:#94a3b8;}
</style></head>
<body>
<div class="wrap">
  <div class="header"><h1 style="margin:0;font-size:1.5rem;">New Course Registration</h1></div>
  <div class="body">
    <p>Hello Admin,</p>
    <p>A new student has registered for a course on <strong>{$siteName}</strong>.</p>
    <table class="info-table">
      <tr><td class="label">Course:</td><td><strong>{$courseTitle}</strong></td></tr>
      <tr><td class="label">Name:</td><td>{$data['name']}</td></tr>
      <tr><td class="label">Email:</td><td>{$data['email']}</td></tr>
      <tr><td class="label">Phone:</td><td>{$data['phone']}</td></tr>
      <tr><td class="label">Message:</td><td>{$data['message']}</td></tr>
    </table>
    <p style="margin-top:30px;"><a href="http://localhost/NexSoft/admin/course_registrations.php" style="background:#0EA5A4;color:white;padding:12px 24px;text-decoration:none;border-radius:8px;font-weight:700;display:inline-block;">View in Dashboard</a></p>
  </div>
  <div class="footer">© {$siteName} Admin Notification</div>
</div>
</body></html>
HTML;
}

/**
 * Email template: Course Registration Confirmation (Student)
 */
function emailTemplateCourseRegistrationStudent(string $name, string $courseTitle): string
{
    $siteName = getSetting('site_name', 'NexSoft Hub');
    return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>
  body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;margin:0;padding:30px 0;}
  .wrap{max-width:560px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 10px 25px rgba(0,0,0,0.05);}
  .header{background:linear-gradient(135deg,#0B1F3B,#162d4f);padding:40px;text-align:center;color:white;}
  .body{padding:40px;}
  .btn{display:inline-block;background:#0EA5A4;color:white;padding:14px 30px;text-decoration:none;border-radius:50px;font-weight:700;margin-top:20px;}
  .footer{padding:20px;text-align:center;font-size:0.8rem;color:#94a3b8;}
</style></head>
<body>
<div class="wrap">
  <div class="header"><div style="font-size:1.5rem;font-weight:900;">Registration Received!</div></div>
  <div class="body">
    <h2 style="color:#0B1F3B;">Hi {$name},</h2>
    <p>Thank you for registering for the <strong>{$courseTitle}</strong> course at {$siteName}.</p>
    <p>We have received your application and our team is currently reviewing it. We will get back to you shortly with further details and availability.</p>
    <p>If you have any questions in the meantime, feel free to reply to this email.</p>
    <p>Stay tuned! 🚀</p>
  </div>
  <div class="footer">© {$siteName} · Education for Future</div>
</div>
</body></html>
HTML;
}

/**
 * Email template: Course App Status Update (Student)
 */
function emailTemplateCourseStatusUpdate(string $name, string $courseTitle, string $status): string
{
    $siteName = getSetting('site_name', 'NexSoft Hub');
    $statusColor = match($status) {
        'accepted' => '#22c55e',
        'rejected' => '#ef4444',
        'called' => '#f59e0b',
        default => '#64748b'
    };
    $statusText = strtoupper($status);
    
    $content = match($status) {
        'accepted' => "Great news! Your application for <strong>{$courseTitle}</strong> has been <strong>ACCEPTED</strong>. We'll be in touch with the schedule and next steps soon.",
        'rejected' => "We appreciate your interest in <strong>{$courseTitle}</strong>. Unfortunately, we are unable to proceed with your application at this time.",
        'called' => "We've reviewed your application for <strong>{$courseTitle}</strong> and would like to have a quick call with you. Please keep your phone reachable.",
        default => "Your application status for <strong>{$courseTitle}</strong> has been updated to <strong>{$statusText}</strong>."
    };

    return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>
  body{font-family:'Segoe UI',sans-serif;background:#f8fafc;margin:0;padding:40px 0;}
  .wrap{max-width:560px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.05);border:1px solid #e2e8f0;}
  .status-bar{background:{$statusColor};height:6px;}
  .body{padding:40px;}
  .status-badge{display:inline-block;padding:4px 12px;border-radius:50px;background:{$statusColor}20;color:{$statusColor};font-weight:700;font-size:0.75rem;margin-bottom:15px;}
  .footer{padding:20px;text-align:center;font-size:0.75rem;color:#94a3b8;background:#f8fafc;}
</style></head>
<body>
<div class="wrap">
  <div class="status-bar"></div>
  <div class="body">
    <div class="status-badge">APPLICATION UPDATE</div>
    <h2 style="color:#0B1F3B;margin-top:0;">Hello {$name},</h2>
    <p style="color:#475569;line-height:1.6;font-size:0.95rem;">{$content}</p>
    <p style="color:#475569;line-height:1.6;font-size:0.95rem;margin-top:20px;">Regards,<br><strong>{$siteName} Team</strong></p>
  </div>
  <div class="footer">© {$siteName} Team</div>
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