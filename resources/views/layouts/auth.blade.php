<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') | Fatty Liver Research</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --navy: #123746;
            --teal: #087f78;
            --muted: #6d8290;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at 10% 10%, #d8eeea, transparent 40%),
                radial-gradient(circle at 90% 90%, #dfebf5, transparent 40%),
                #f3f7f8;
            color: var(--navy);
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .auth-shell {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px 20px;
        }

        .auth-window {
            width: min(1080px, 100%);
            display: grid;
            grid-template-columns: 44% 56%;
            background: white;
            border: 1px solid #e0ebed;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 25px 80px #12374612;
        }

        .brand-panel {
            position: relative;
            overflow: hidden;
            padding: 42px;
            background: linear-gradient(145deg, #103342, #087f78);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 32px;
        }

        .brand-panel::after {
            content: "";
            position: absolute;
            width: 350px;
            height: 350px;
            border: 1px solid #ffffff18;
            border-radius: 50%;
            bottom: -180px;
            right: -120px;
            pointer-events: none;
        }

        .brand-name {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 600;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            border: 1px solid #ffffff50;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 28px;
        }

        .eyebrow {
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 11px;
            font-weight: 700;
            color: #a7e5dc;
        }

        .brand-panel h2 {
            font-size: clamp(28px, 3vw, 39px);
            font-weight: 700;
            line-height: 1.2;
            margin: 14px 0;
        }

        .brand-copy {
            color: #cce5e5;
            font-size: 14px;
            line-height: 1.8;
        }

        .medical-sketch {
            width: 100%;
            max-width: 320px;
            margin: 8px auto;
            display: block;
        }

        .brand-footer {
            font-size: 12px;
            color: #cce5e5;
        }

        .form-panel {
            padding: 44px 48px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-tabs {
            display: flex;
            gap: 5px;
            padding: 5px;
            background: #eff5f5;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .auth-tabs a {
            flex: 1;
            text-align: center;
            padding: 10px;
            text-decoration: none;
            color: var(--muted);
            border-radius: 9px;
            font-size: 14px;
            font-weight: 600;
        }

        .auth-tabs a.active {
            color: var(--teal);
            background: white;
            box-shadow: 0 2px 8px #1237460d;
        }

        .form-panel h1 {
            font-size: 29px;
            font-weight: 700;
            letter-spacing: -0.6px;
        }

        .intro {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
        }

        .form-control,
        .form-select {
            min-height: 48px;
            border-color: #dce6e9;
            border-radius: 10px;
            font-size: 14px;
            padding: 11px 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 3px #087f7818;
        }

        .btn-auth {
            width: 100%;
            background: var(--teal);
            color: white;
            border: 0;
            border-radius: 10px;
            padding: 13px;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-auth:hover,
        .btn-auth:focus-visible {
            background: #066b65;
            color: white;
        }

        .auth-link {
            color: var(--teal);
            font-weight: 600;
            text-decoration: none;
        }

        .auth-link:hover {
            text-decoration: underline;
        }

        .small-copy {
            font-size: 13px;
            color: var(--muted);
        }

        .form-panel .alert {
            font-size: 13px;
            border-radius: 10px;
        }

        .slide-content {
            --entry: -40px;
            animation: slideIn 420ms ease both;
            transition: transform 220ms ease, opacity 220ms ease;
        }

        .register-page .slide-content {
            --entry: 40px;
        }

        .slide-content.leaving {
            opacity: 0;
            transform: translateX(var(--exit, -40px));
            pointer-events: none;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(var(--entry));
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @media (max-width: 800px) {
            .auth-window {
                grid-template-columns: 1fr;
                max-width: 560px;
            }

            .brand-panel {
                padding: 24px 28px;
                gap: 0;
            }

            .brand-story,
            .brand-footer {
                display: none;
            }

            .form-panel {
                padding: 30px 28px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .slide-content {
                animation: none;
                transition: none;
            }
        }
    </style>
</head>

<body class="{{ request()->routeIs('register') ? 'register-page' : 'login-page' }}">
    <main class="auth-shell">
        <div class="auth-window">
            <aside class="brand-panel" aria-label="About this system">
                <div class="brand-name">
                    <span class="brand-icon" aria-hidden="true">+</span>
                    <span>Fatty Liver Research</span>
                </div>

                <div class="brand-story">
                    <span class="eyebrow">Clinical care · Research</span>

                    <h2>Better records.<br>Connected care.</h2>

                    <p class="brand-copy">
                        One workspace for patient information,
                        clinical visits and research follow-up.
                    </p>

                    <svg class="medical-sketch" viewBox="0 0 320 230" fill="none" aria-hidden="true">
                        <rect x="40" y="28" width="240" height="180" rx="24" fill="white" fill-opacity=".06"
                            stroke="white" stroke-opacity=".2" />

                        <path d="M75 104C92 73 121 68 150 73
                                 C174 77 189 66 214 78
                                 C236 89 251 105 245 123
                                 C241 137 220 142 202 137
                                 C179 133 165 149 146 159
                                 C124 172 101 169 85 155
                                 C70 142 67 121 75 104Z" fill="#88ded0" fill-opacity=".16" stroke="#a4eee0"
                            stroke-width="3" />

                        <path d="M165 77C172 101 167 126 146 159" stroke="#a4eee0" stroke-width="2"
                            stroke-dasharray="5 5" />

                        <path d="M62 183H111L123 165L138 195L152 177H193" stroke="white" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round" />

                        <circle cx="245" cy="55" r="23" fill="#d6faf2" fill-opacity=".15" />

                        <path d="M245 44V66M234 55H256" stroke="#cbfff1" stroke-width="3" stroke-linecap="round" />
                    </svg>
                </div>

                <div class="brand-footer">
                    Patient management &amp; study follow-up
                </div>
            </aside>

            <section class="form-panel">

                <nav class="auth-tabs">
                    <a href="{{ route('login') }}" class="active" aria-current="page">
                        Sign In
                    </a>
                </nav>

                <div class="slide-content">
                    @if (session('success'))
                        <div class="alert alert-success" role="status">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="alert alert-info" role="status">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </section>
        </div>
    </main>

    <script>
        const reduceMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        );

        document.querySelectorAll('[data-auth-slide]').forEach(link => {
            link.addEventListener('click', event => {
                if (
                    event.button !== 0 ||
                    event.ctrlKey ||
                    event.metaKey ||
                    event.shiftKey ||
                    event.altKey ||
                    reduceMotion.matches
                ) {
                    return;
                }

                const target = new URL(link.href);

                if (target.pathname === window.location.pathname) {
                    return;
                }

                event.preventDefault();

                const content = document.querySelector('.slide-content');

                content.style.setProperty(
                    '--exit',
                    document.body.classList.contains('register-page')
                        ? '40px'
                        : '-40px'
                );

                content.classList.add('leaving');

                window.setTimeout(() => {
                    window.location.assign(link.href);
                }, 220);
            });
        });

        window.addEventListener('pageshow', () => {
            document.querySelector('.slide-content')
                ?.classList.remove('leaving');
        });
    </script>
</body>

</html>