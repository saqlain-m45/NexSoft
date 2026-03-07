<?php require_once __DIR__ . '/../components/header.php'; ?>

<section
    style="min-height: 80vh; display: flex; align-items: center; justify-content: center; background: #0B1F3B; position: relative; overflow: hidden; padding: 60px 0;">
    <!-- Animated Background Elements -->
    <div class="shape"
        style="position: absolute; top: -10%; left: -5%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(14,165,164,0.15) 0%, transparent 70%); border-radius: 50%; animation: float 10s infinite alternate;">
    </div>
    <div class="shape"
        style="position: absolute; bottom: -10%; right: -5%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(14,165,164,0.1) 0%, transparent 70%); border-radius: 50%; animation: float 15s infinite alternate-reverse;">
    </div>

    <div class="container text-center" style="position: relative; z-index: 10;">
        <div class="coming-soon-badge"
            style="display: inline-flex; align-items: center; gap: 10px; background: rgba(14,165,164,0.1); border: 1px solid rgba(14,165,164,0.3); color: #0EA5A4; padding: 8px 24px; border-radius: 50px; font-weight: 700; margin-bottom: 30px; font-size: 0.9rem; animation: fadeInUp 0.8s ease;">
            <span class="pulse"
                style="width: 8px; height: 8px; background: #0EA5A4; border-radius: 50%; display: inline-block;"></span>
            FUTURE OPPORTUNITIES
        </div>

        <h1
            style="color: white; font-size: Clamp(2.5rem, 8vw, 5rem); font-weight: 900; line-height: 1; margin-bottom: 25px; letter-spacing: -2px; animation: fadeInUp 1s ease;">
            Internships <br> <span style="color: transparent; -webkit-text-stroke: 1px rgba(255,255,255,0.3);">Coming
                Soon</span>
        </h1>

        <p
            style="color: rgba(255,255,255,0.6); font-size: 1.25rem; max-width: 600px; margin: 0 auto 40px; line-height: 1.7; animation: fadeInUp 1.2s ease;">
            We're building a world-class internship program to mentor the next generation of tech leaders. Stay tuned
            for updates!
        </p>

        <div class="action-buttons"
            style="display: flex; gap: 15px; justify-content: center; animation: fadeInUp 1.4s ease;">
            <a href="/NexSoft/?route=contact" class="btn"
                style="background: #0EA5A4; color: white; padding: 15px 40px; border-radius: 50px; font-weight: 700; transition: 0.3s ease;">Notify
                Me</a>
            <a href="/NexSoft/" class="btn"
                style="background: transparent; color: white; border: 1px solid rgba(255,255,255,0.2); padding: 15px 40px; border-radius: 50px; font-weight: 700; transition: 0.3s ease;">Back
                to Home</a>
        </div>
    </div>
</section>

<style>
    @keyframes float {
        0% {
            transform: translate(0, 0) rotate(0deg);
        }

        100% {
            transform: translate(30px, 30px) rotate(10deg);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .pulse {
        animation: pulse-ring 2s infinite;
    }

    @keyframes pulse-ring {
        0% {
            box-shadow: 0 0 0 0 rgba(14, 165, 164, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(14, 165, 164, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(14, 165, 164, 0);
        }
    }

    .coming-soon-badge {
        box-shadow: 0 10px 30px rgba(14, 165, 164, 0.1);
    }

    .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        filter: brightness(1.1);
    }
</style>

<?php require_once __DIR__ . '/../components/footer.php'; ?>