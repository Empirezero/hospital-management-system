<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Login &mdash; Terapia</title>

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
            --blush: #F2A6A6;
            --ink: #1F2937;
            --paper: #FAFBFC;
            --line: #E4E9ED;
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
            grid-template-columns: 1.05fr 1fr;
        }

        @media (max-width: 900px) {
            .auth-shell {
                grid-template-columns: 1fr;
            }

            .brand-panel {
                min-height: 280px;
            }
        }

        /* ── Left brand panel ───────────────────────────── */
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

        /* signature element: breathing asterisk constellation */
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
            width: 220px;
            height: 220px;
            animation-delay: 0s;
        }

        .pulse-ring.r2 {
            width: 340px;
            height: 340px;
            animation-delay: 0.6s;
        }

        .pulse-ring.r3 {
            width: 460px;
            height: 460px;
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
            width: 92px;
            height: 92px;
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
            max-width: 380px;
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
            font-size: 2.4rem;
            line-height: 1.18;
            margin: 0 0 0.9rem;
            color: #FFFFFF;
        }

        .brand-copy p {
            font-size: 0.95rem;
            line-height: 1.6;
            color: rgba(234, 246, 248, 0.72);
            margin: 0;
        }

        .brand-foot {
            position: relative;
            z-index: 2;
            font-size: 0.78rem;
            color: rgba(234, 246, 248, 0.45);
        }

        /* ── Right form panel ───────────────────────────── */
        .form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
        }

        .form-wrap {
            width: 100%;
            max-width: 380px;
        }

        .form-wrap h2 {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-weight: 500;
            font-size: 1.9rem;
            margin: 0 0 0.35rem;
            color: var(--navy);
        }

        .form-wrap .sub {
            color: #6B7684;
            font-size: 0.92rem;
            margin-bottom: 2.1rem;
        }

        .field {
            position: relative;
            margin-bottom: 1.5rem;
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
            border-bottom-color: #E4685D;
        }

        .field .invalid-feedback {
            display: block;
            color: #E4685D;
            font-size: 0.78rem;
            margin-top: 0.35rem;
        }

        .field-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
        }

        .field-row a {
            font-size: 0.8rem;
            color: var(--teal-dark);
            text-decoration: none;
        }

        .field-row a:hover {
            text-decoration: underline;
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.4rem 0 1.9rem;
            font-size: 0.85rem;
            color: #6B7684;
        }

        .remember-row input {
            accent-color: var(--teal);
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

        .copyright {
            text-align: center;
            margin-top: 2.5rem;
            font-size: 0.75rem;
            color: #A6AEB8;
        }
    </style>
</head>

<body>

    <div class="auth-shell">

        <!-- Brand panel -->
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
                <div class="eyebrow">Staff &amp; Patient Portal</div>
                <h1>Care, coordinated<br>in one place.</h1>
                <p>Sign in to manage appointments, records, and recovery — wherever you are on the team.</p>
            </div>

            <div class="brand-foot">&copy; {{ date('Y') }} Terapia Physiotherapy Center</div>
        </div>

        <!-- Form panel -->
        <div class="form-panel">
            <div class="form-wrap">
                <h2>Welcome back</h2>
                <div class="sub">Enter your details to access your dashboard.</div>

                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf

                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" type="email" class="@error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" tabindex="1" required autofocus>
                        @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <div class="field-row">
                            <label for="password" style="margin-bottom:0;">Password</label>
                            <a href="{{ route('password.request') }}">Forgot password?</a>
                        </div>
                        <input id="password" type="password" class="@error('password') is-invalid @enderror"
                            name="password" tabindex="2" required style="margin-top:0.4rem;">
                        @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <label class="remember-row">
                        <input type="checkbox" name="remember" tabindex="3" {{ old('remember') ? 'checked' : '' }}>
                        Remember me
                    </label>

                    <button type="submit" class="btn-pill" tabindex="4">Login</button>
                </form>

                <div class="form-foot">
                    Don't have an account? <a href="{{ route('register') }}">Create one</a>
                </div>
                <div class="copyright">Secure staff and patient access</div>
            </div>
        </div>

    </div>

    <script src="{{ asset('admin/assets/modules/jquery.min.js') }}"></script>
</body>

</html>