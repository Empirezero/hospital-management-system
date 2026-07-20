<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Register &mdash; Terapia</title>

    <link rel="stylesheet" href="{{ asset('admin/assets/modules/fontawesome/css/all.min.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;1,500&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --teal: #2FBAD3;
            --teal-dark: #1D8FA5;
            --navy: #0B1E33;
            --navy-deep: #08151F;
            --ink: #1F2937;
            --paper: #FAFBFC;
            --line: #E4E9ED;
            --danger: #E4685D;
            --success: #3BB273;
            --warning: #E0A03A;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', -apple-system, sans-serif;
            color: var(--ink);
            background: var(--paper);
        }

        .auth-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1.15fr;
        }

        @media (max-width: 900px) {
            .auth-shell {
                grid-template-columns: 1fr;
            }

            .brand-panel {
                min-height: 240px;
            }
        }

        .brand-panel {
            position: relative;
            background: linear-gradient(160deg, var(--navy) 0%, var(--navy-deep) 65%, #062430 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem 3.5rem;
            overflow: hidden;
            color: #EAF6F8;
        }

        .brand-mark-row {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            position: relative;
            z-index: 2;
        }

        .brand-mark-row .glyph {
            width: 30px;
            height: 30px;
            color: var(--teal);
        }

        .brand-mark-row span {
            font-family: 'Playfair Display', serif;
            font-size: 1.35rem;
            letter-spacing: 0.02em;
        }

        .pulse-field {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .pulse-ring {
            position: absolute;
            border: 1px solid rgba(47, 186, 211, 0.28);
            border-radius: 50%;
            animation: breathe 4.5s ease-in-out infinite;
        }

        .pulse-ring.r1 {
            width: 200px;
            height: 200px;
            animation-delay: 0s;
        }

        .pulse-ring.r2 {
            width: 310px;
            height: 310px;
            animation-delay: 0.6s;
        }

        .pulse-ring.r3 {
            width: 420px;
            height: 420px;
            animation-delay: 1.2s;
        }

        @keyframes breathe {

            0%,
            100% {
                transform: scale(0.92);
                opacity: 0.35;
            }

            50% {
                transform: scale(1.04);
                opacity: 0.85;
            }
        }

        .asterisk-glyph {
            position: relative;
            z-index: 2;
            width: 84px;
            height: 84px;
            color: var(--teal);
            animation: glow 4.5s ease-in-out infinite;
            filter: drop-shadow(0 0 18px rgba(47, 186, 211, 0.35));
        }

        @keyframes glow {

            0%,
            100% {
                opacity: 0.75;
            }

            50% {
                opacity: 1;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .pulse-ring,
            .asterisk-glyph {
                animation: none;
            }
        }

        .brand-copy {
            position: relative;
            z-index: 2;
            max-width: 360px;
        }

        .brand-copy .eyebrow {
            text-transform: uppercase;
            letter-spacing: 0.18em;
            font-size: 0.72rem;
            color: var(--teal);
            font-weight: 600;
            margin-bottom: 0.9rem;
        }

        .brand-copy h1 {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            font-size: 2.15rem;
            line-height: 1.2;
            margin: 0 0 0.9rem;
            color: #FFFFFF;
        }

        .brand-copy p {
            font-size: 0.93rem;
            line-height: 1.6;
            color: rgba(234, 246, 248, 0.72);
            margin: 0;
        }

        .brand-list {
            position: relative;
            z-index: 2;
            margin-top: 1.6rem;
            padding: 0;
            list-style: none;
        }

        .brand-list li {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.86rem;
            color: rgba(234, 246, 248, 0.78);
            margin-bottom: 0.6rem;
        }

        .brand-list li i {
            color: var(--teal);
            font-size: 0.9rem;
        }

        .brand-foot {
            position: relative;
            z-index: 2;
            font-size: 0.78rem;
            color: rgba(234, 246, 248, 0.45);
        }

        .form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
        }

        .form-wrap {
            width: 100%;
            max-width: 440px;
        }

        .form-wrap h2 {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-weight: 500;
            font-size: 1.85rem;
            margin: 0 0 0.35rem;
            color: var(--navy);
        }

        .form-wrap .sub {
            color: #6B7684;
            font-size: 0.92rem;
            margin-bottom: 2rem;
        }

        .field-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 1.25rem;
        }

        @media (max-width: 480px) {
            .field-grid {
                grid-template-columns: 1fr;
            }
        }

        .field {
            position: relative;
            margin-bottom: 1.4rem;
        }

        .field label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #4B5563;
            margin-bottom: 0.4rem;
            letter-spacing: 0.01em;
        }

        .field input {
            width: 100%;
            border: none;
            border-bottom: 1.5px solid var(--line);
            background: transparent;
            padding: 0.55rem 0.1rem;
            font-size: 0.98rem;
            font-family: 'Inter', sans-serif;
            color: var(--ink);
            outline: none;
            transition: border-color 0.25s ease;
        }

        .field input:focus {
            border-bottom-color: var(--teal);
        }

        .field input.is-invalid {
            border-bottom-color: var(--danger);
        }

        .field .invalid-feedback {
            display: block;
            color: var(--danger);
            font-size: 0.78rem;
            margin-top: 0.35rem;
        }

        .pw-meter {
            display: flex;
            gap: 4px;
            margin-top: 0.5rem;
        }

        .pw-meter .bar {
            height: 3px;
            flex: 1;
            border-radius: 2px;
            background: var(--line);
            transition: background 0.25s ease;
        }

        .pw-meter-label {
            font-size: 0.74rem;
            color: #8A93A0;
            margin-top: 0.35rem;
            min-height: 1em;
        }

        .terms-row {
            display: flex;
            align-items: flex-start;
            gap: 0.55rem;
            margin: 0.3rem 0 1.9rem;
            font-size: 0.84rem;
            color: #6B7684;
            line-height: 1.5;
        }

        .terms-row input {
            accent-color: var(--teal);
            margin-top: 0.2rem;
        }

        .terms-row a {
            color: var(--teal-dark);
            text-decoration: none;
        }

        .terms-row a:hover {
            text-decoration: underline;
        }

        .btn-pill {
            width: 100%;
            border: none;
            background: var(--teal);
            color: #fff;
            font-weight: 600;
            font-size: 0.98rem;
            padding: 0.85rem 1.5rem;
            border-radius: 999px;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.15s ease;
        }

        .btn-pill:hover {
            background: var(--teal-dark);
            transform: translateY(-1px);
        }

        .btn-pill:focus-visible {
            outline: 3px solid rgba(47, 186, 211, 0.4);
            outline-offset: 2px;
        }

        .form-foot {
            text-align: center;
            margin-top: 1.8rem;
            font-size: 0.88rem;
            color: #6B7684;
        }

        .form-foot a {
            color: var(--teal-dark);
            font-weight: 600;
            text-decoration: none;
        }

        .form-foot a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="auth-shell">

        <div class="brand-panel">
            <div class="brand-mark-row">
                <svg class="glyph" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round">
                    <path d="M12 2v20M4.2 6l15.6 12M19.8 6L4.2 18" />
                </svg>
                <span>Terapia</span>
            </div>

            <div class="pulse-field" aria-hidden="true">
                <div class="pulse-ring r1"></div>
                <div class="pulse-ring r2"></div>
                <div class="pulse-ring r3"></div>
                <svg class="asterisk-glyph" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <path d="M12 2v20M4.2 6l15.6 12M19.8 6L4.2 18" />
                </svg>
            </div>

            <div class="brand-copy">
                <div class="eyebrow">Join Terapia</div>
                <h1>Start your recovery, on your schedule.</h1>
                <p>Create an account to book appointments, track your treatment, and message your care team directly.</p>
            </div>

            <ul class="brand-list">
                <li><i class="fas fa-check" aria-hidden="true"></i> Book and manage appointments</li>
                <li><i class="fas fa-check" aria-hidden="true"></i> View your visit history and results</li>
                <li><i class="fas fa-check" aria-hidden="true"></i> Secure messaging with your provider</li>
            </ul>

            <div class="brand-foot">&copy; {{ date('Y') }} Terapia Physiotherapy Center</div>
        </div>

        <div class="form-panel">
            <div class="form-wrap">
                <h2>Create your account</h2>
                <div class="sub">Takes less than a minute.</div>

                <form method="POST" action="{{ route('register') }}" novalidate>
                    @csrf

                    <div class="field">
                        <label for="name">Full name</label>
                        <input id="name" type="text" class="@error('name') is-invalid @enderror"
                            name="name" value="{{ old('name') }}" required autofocus>
                        @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" type="email" class="@error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required>
                        @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field-grid">
                        <div class="field">
                            <label for="password">Password</label>
                            <input id="password" type="password" class="@error('password') is-invalid @enderror"
                                name="password" required>
                            <div class="pw-meter" id="pw-meter">
                                <div class="bar"></div>
                                <div class="bar"></div>
                                <div class="bar"></div>
                                <div class="bar"></div>
                            </div>
                            <div class="pw-meter-label" id="pw-meter-label"></div>
                            @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="field">
                            <label for="password-confirm">Confirm password</label>
                            <input id="password-confirm" type="password" name="password_confirmation" required>
                        </div>
                    </div>

                    <label class="terms-row">
                        <input type="checkbox" name="agree" id="agree" required>
                        <span>I agree to the <a href="#">terms of service</a> and <a href="#">privacy policy</a>.</span>
                    </label>

                    <button type="submit" class="btn-pill">Create account</button>
                </form>

                <div class="form-foot">
                    Already have an account? <a href="{{ route('login') }}">Sign in</a>
                </div>
            </div>
        </div>

    </div>

    <script src="{{ asset('admin/assets/modules/jquery.min.js') }}"></script>
    <script>
        (function() {
            var input = document.getElementById('password');
            var bars = document.querySelectorAll('#pw-meter .bar');
            var label = document.getElementById('pw-meter-label');
            var colors = ['var(--danger)', 'var(--warning)', 'var(--teal)', 'var(--success)'];
            var labels = ['Weak', 'Fair', 'Good', 'Strong'];

            if (!input) return;

            input.addEventListener('input', function() {
                var val = input.value;
                var score = 0;
                if (val.length >= 8) score++;
                if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
                if (/\d/.test(val)) score++;
                if (/[^A-Za-z0-9]/.test(val)) score++;
                if (val.length === 0) score = 0;

                bars.forEach(function(bar, i) {
                    bar.style.background = i < score ? colors[Math.max(score - 1, 0)] : 'var(--line)';
                });
                label.textContent = val.length === 0 ? '' : labels[Math.max(score - 1, 0)];
            });
        })();
    </script>
</body>

</html>