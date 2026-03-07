<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode | <?php echo htmlspecialchars(getSetting('site_name', 'NexSoft Hub')); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #041024;
            --ink-soft: #0d1f3b;
            --cyan: #14b8b3;
            --mint: #7be7d8;
            --paper: #edf3fb;
            --card: rgba(255, 255, 255, 0.08);
            --line: rgba(255, 255, 255, 0.22);
            --font-head: 'Syne', sans-serif;
            --font-body: 'Space Grotesk', sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: var(--font-body);
            color: var(--paper);
            background:
                radial-gradient(circle at 14% 18%, rgba(20, 184, 179, 0.18), transparent 34%),
                radial-gradient(circle at 80% 20%, rgba(123, 231, 216, 0.17), transparent 30%),
                radial-gradient(circle at 45% 82%, rgba(34, 211, 238, 0.12), transparent 28%),
                linear-gradient(145deg, var(--ink) 0%, var(--ink-soft) 100%);
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .shell {
            width: min(980px, 100%);
            border: 1px solid var(--line);
            border-radius: 28px;
            overflow: hidden;
            backdrop-filter: blur(8px);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.03));
            box-shadow: 0 20px 70px rgba(2, 6, 23, 0.6);
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            border-bottom: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.04);
            font-size: 0.85rem;
        }

        .pulse {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--mint);
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--mint);
            box-shadow: 0 0 0 0 rgba(123, 231, 216, 0.7);
            animation: ping 1.6s infinite;
        }

        @keyframes ping {
            0% { box-shadow: 0 0 0 0 rgba(123, 231, 216, 0.7); }
            70% { box-shadow: 0 0 0 14px rgba(123, 231, 216, 0); }
            100% { box-shadow: 0 0 0 0 rgba(123, 231, 216, 0); }
        }

        .content {
            padding: clamp(26px, 5vw, 58px);
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: clamp(20px, 4vw, 40px);
            align-items: center;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--line);
            background: rgba(20, 184, 179, 0.12);
            color: var(--mint);
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        h1 {
            margin: 14px 0 14px;
            font-family: var(--font-head);
            font-size: clamp(2rem, 5vw, 3.6rem);
            line-height: 1.02;
            letter-spacing: -0.02em;
        }

        p {
            margin: 0;
            color: rgba(237, 243, 251, 0.82);
            max-width: 52ch;
            line-height: 1.65;
        }

        .actions {
            margin-top: 22px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            border: 1px solid var(--line);
            color: var(--paper);
            padding: 11px 16px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.92rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.25s ease;
        }

        .btn.primary {
            background: linear-gradient(120deg, var(--cyan), #0f8dff);
            border-color: transparent;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .panel {
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 18px;
            background: var(--card);
        }

        .bars {
            margin-top: 14px;
            display: grid;
            gap: 10px;
        }

        .bar {
            height: 9px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.15);
            overflow: hidden;
        }

        .bar > span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--cyan), var(--mint));
            animation: slide 2.2s ease-in-out infinite alternate;
        }

        .bar:nth-child(1) > span { width: 72%; }
        .bar:nth-child(2) > span { width: 48%; }
        .bar:nth-child(3) > span { width: 84%; }

        @keyframes slide {
            from { transform: translateX(-6%); }
            to { transform: translateX(6%); }
        }

        .foot {
            border-top: 1px solid var(--line);
            padding: 14px 18px;
            font-size: 0.82rem;
            color: rgba(237, 243, 251, 0.72);
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }

        @media (max-width: 840px) {
            .content {
                grid-template-columns: 1fr;
            }

            .shell {
                border-radius: 22px;
            }
        }
    </style>
</head>
<body>
    <section class="shell">
        <div class="topbar">
            <span class="pulse"><span class="dot"></span> Maintenance in progress</span>
            <span><?php echo htmlspecialchars(getSetting('site_name', 'NexSoft Hub')); ?></span>
        </div>

        <div class="content">
            <div>
                <span class="badge"><i class="bi bi-tools"></i> Temporary Downtime</span>
                <h1>We are tuning things up for a better experience.</h1>
                <p>
                    Our team is currently applying updates and performance improvements. The website will be available again shortly. Thank you for your patience.
                </p>
                <div class="actions">
                    <a class="btn primary" href="<?php echo baseUrl(); ?>"><i class="bi bi-arrow-clockwise"></i> Refresh Page</a>
                    <a class="btn" href="<?php echo baseUrl('?route=contact'); ?>"><i class="bi bi-envelope"></i> Contact Support</a>
                </div>
            </div>

            <div class="panel">
                <div style="font-weight:700;letter-spacing:0.01em;">Deployment Checklist</div>
                <div style="margin-top:6px;font-size:0.9rem;color:rgba(237,243,251,0.74);">Infrastructure checks are running.</div>
                <div class="bars">
                    <div class="bar"><span></span></div>
                    <div class="bar"><span></span></div>
                    <div class="bar"><span></span></div>
                </div>
            </div>
        </div>

        <div class="foot">
            <span>Status Code: 503 Service Unavailable</span>
            <span><?php echo date('M d, Y h:i A'); ?></span>
        </div>
    </section>
</body>
</html>
