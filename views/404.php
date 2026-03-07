<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 | <?php echo htmlspecialchars(function_exists('getSetting') ? getSetting('site_name', 'NexSoft Hub') : 'NexSoft Hub'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;800&family=Outfit:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #071b33;
            --ink-2: #0f2b4c;
            --mint: #16d4c5;
            --blue: #45a6ff;
            --text: #eaf2ff;
            --muted: rgba(234, 242, 255, 0.78);
            --line: rgba(255, 255, 255, 0.2);
            --font-h: 'Sora', sans-serif;
            --font-b: 'Outfit', sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            color: var(--text);
            font-family: var(--font-b);
            background:
                radial-gradient(circle at 10% 12%, rgba(22, 212, 197, 0.17), transparent 35%),
                radial-gradient(circle at 88% 24%, rgba(69, 166, 255, 0.16), transparent 34%),
                linear-gradient(140deg, var(--ink) 0%, var(--ink-2) 100%);
            overflow: hidden;
        }

        .noise {
            position: fixed;
            inset: 0;
            pointer-events: none;
            opacity: 0.08;
            background-image: radial-gradient(rgba(255,255,255,.8) 1px, transparent 1px);
            background-size: 3px 3px;
        }

        .card {
            width: min(980px, 100%);
            border-radius: 26px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, rgba(255,255,255,.1), rgba(255,255,255,.04));
            box-shadow: 0 24px 70px rgba(3, 8, 20, 0.55);
            overflow: hidden;
        }

        .grid {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
        }

        .left, .right {
            padding: clamp(24px, 4.2vw, 54px);
        }

        .left {
            border-right: 1px solid var(--line);
        }

        .badge {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            border: 1px solid var(--line);
            color: var(--mint);
            background: rgba(22, 212, 197, 0.12);
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 700;
        }

        h1 {
            margin: 14px 0 12px;
            font-family: var(--font-h);
            font-size: clamp(2rem, 4.5vw, 3.4rem);
            line-height: 1.06;
            letter-spacing: -0.02em;
        }

        p {
            margin: 0;
            color: var(--muted);
            line-height: 1.62;
            max-width: 50ch;
        }

        .actions {
            margin-top: 22px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 15px;
            border-radius: 12px;
            border: 1px solid var(--line);
            text-decoration: none;
            color: var(--text);
            font-weight: 600;
            transition: 0.24s ease;
        }

        .btn.primary {
            background: linear-gradient(120deg, var(--mint), var(--blue));
            border-color: transparent;
            color: #fff;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .code {
            font-family: var(--font-h);
            font-size: clamp(4rem, 11vw, 8rem);
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.05em;
            color: transparent;
            background: linear-gradient(100deg, #ffffff 0%, #95f4ed 45%, #88c8ff 100%);
            -webkit-background-clip: text;
            background-clip: text;
            margin-bottom: 14px;
            animation: float 3.2s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        .mini {
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 14px;
            background: rgba(255,255,255,0.06);
        }

        .mini h3 {
            margin: 0 0 8px;
            font-size: 0.95rem;
            font-family: var(--font-h);
        }

        .mini ul {
            margin: 0;
            padding-left: 18px;
            color: var(--muted);
            line-height: 1.6;
            font-size: 0.92rem;
        }

        .foot {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            border-top: 1px solid var(--line);
            padding: 12px 16px;
            font-size: 0.82rem;
            color: var(--muted);
        }

        @media (max-width: 860px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .left {
                border-right: 0;
                border-bottom: 1px solid var(--line);
            }
        }
    </style>
</head>
<body>
<div class="noise"></div>

<section class="card">
    <div class="grid">
        <div class="left">
            <span class="badge"><i class="bi bi-compass"></i> Page Missing</span>
            <h1>This route took a wrong turn.</h1>
            <p>
                The page you requested does not exist, may have moved, or the URL was typed incorrectly. Use one of the options below to continue.
            </p>
            <div class="actions">
                <a class="btn primary" href="<?php echo function_exists('baseUrl') ? baseUrl() : '/NexSoft/'; ?>">
                    <i class="bi bi-house-door-fill"></i> Back to Home
                </a>
                <a class="btn" href="<?php echo function_exists('baseUrl') ? baseUrl('?route=contact') : '/NexSoft/?route=contact'; ?>">
                    <i class="bi bi-chat-dots"></i> Contact Us
                </a>
            </div>
        </div>

        <div class="right">
            <div class="code">404</div>
            <div class="mini">
                <h3><i class="bi bi-lightbulb"></i> Quick checks</h3>
                <ul>
                    <li>Check the URL spelling</li>
                    <li>Go back to the previous page</li>
                    <li>Visit homepage and navigate again</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="foot">
        <span><?php echo htmlspecialchars(function_exists('getSetting') ? getSetting('site_name', 'NexSoft Hub') : 'NexSoft Hub'); ?></span>
        <span>Error Code: 404 Not Found</span>
    </div>
</section>
</body>
</html>
