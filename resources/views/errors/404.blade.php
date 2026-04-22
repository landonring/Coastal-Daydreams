<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Page Not Found | Jennifer Williams</title>
        <meta
            name="description"
            content="The page you were looking for could not be found."
        >
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="alternate icon" href="/favicon.ico">
        <style>
            :root {
                --paper: #ffffff;
                --paper-soft: #f7f6f3;
                --ink: #111111;
                --muted: #6b6b6b;
                --line: rgba(17, 17, 17, 0.08);
            }

            * {
                box-sizing: border-box;
            }

            html {
                scroll-behavior: smooth;
            }

            body {
                margin: 0;
                min-height: 100vh;
                color: var(--ink);
                font-family: "Inter", ui-sans-serif, system-ui, sans-serif;
                background:
                    radial-gradient(circle at 16% 20%, rgba(247, 246, 243, 0.92), rgba(247, 246, 243, 0) 34%),
                    radial-gradient(circle at 84% 18%, rgba(242, 239, 233, 0.85), rgba(242, 239, 233, 0) 32%),
                    linear-gradient(160deg, #ffffff 0%, #fbfaf8 42%, #f4f1eb 100%);
                overflow: hidden;
            }

            body::before {
                content: "";
                position: fixed;
                inset: 0;
                pointer-events: none;
                opacity: 0.04;
                mix-blend-mode: multiply;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180' viewBox='0 0 180 180'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='1.15' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='180' height='180' filter='url(%23n)' opacity='0.9'/%3E%3C/svg%3E");
                background-size: 180px 180px;
            }

            .shell {
                position: relative;
                min-height: 100vh;
                padding: 32px 24px;
            }

            .frame {
                max-width: 1480px;
                margin: 0 auto;
                min-height: calc(100vh - 64px);
                border: 1px solid rgba(17, 17, 17, 0.05);
                border-radius: 32px;
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(20px);
                box-shadow: 0 28px 80px rgba(17, 17, 17, 0.05);
                display: flex;
                flex-direction: column;
            }

            .nav {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 24px;
                padding: 28px 44px;
            }

            .brand {
                font-family: "Playfair Display", ui-serif, Georgia, serif;
                font-size: 1.2rem;
                letter-spacing: 0.06em;
                color: var(--ink);
                text-decoration: none;
            }

            .nav-links {
                display: flex;
                align-items: center;
                gap: 28px;
                font-size: 0.72rem;
                letter-spacing: 0.28em;
                text-transform: uppercase;
            }

            .nav-links a {
                color: var(--muted);
                text-decoration: none;
                transition: color 180ms ease, opacity 180ms ease;
            }

            .nav-links a:hover {
                color: var(--ink);
            }

            .content {
                flex: 1;
                display: grid;
                align-items: center;
                grid-template-columns: minmax(0, 1.1fr) minmax(280px, 0.85fr);
                gap: 48px;
                padding: 40px 44px 56px;
            }

            .copy {
                max-width: 720px;
            }

            .eyebrow {
                margin: 0;
                font-size: 0.74rem;
                letter-spacing: 0.34em;
                text-transform: uppercase;
                color: var(--muted);
            }

            .title {
                margin: 22px 0 0;
                font-family: "Playfair Display", ui-serif, Georgia, serif;
                font-size: clamp(4rem, 11vw, 7.8rem);
                line-height: 0.96;
                letter-spacing: -0.04em;
                color: var(--ink);
            }

            .subtitle {
                margin: 26px 0 0;
                max-width: 540px;
                font-size: clamp(1.08rem, 2vw, 1.24rem);
                line-height: 1.9;
                color: var(--muted);
            }

            .actions {
                display: flex;
                flex-wrap: wrap;
                gap: 14px;
                margin-top: 34px;
            }

            .button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 52px;
                padding: 0 26px;
                border-radius: 999px;
                border: 1px solid var(--line);
                background: var(--paper);
                color: var(--ink);
                font-size: 0.72rem;
                font-weight: 600;
                letter-spacing: 0.28em;
                text-transform: uppercase;
                text-decoration: none;
                transition:
                    transform 220ms ease,
                    background-color 220ms ease,
                    color 220ms ease,
                    border-color 220ms ease,
                    box-shadow 220ms ease;
            }

            .button:hover {
                transform: translateY(-1px);
                box-shadow: 0 12px 30px rgba(17, 17, 17, 0.08);
            }

            .button.primary {
                background: var(--ink);
                border-color: var(--ink);
                color: #ffffff;
            }

            .panel {
                position: relative;
                padding: 30px;
                border-radius: 28px;
                border: 1px solid rgba(17, 17, 17, 0.06);
                background:
                    linear-gradient(180deg, rgba(255, 255, 255, 0.82), rgba(247, 246, 243, 0.88));
                box-shadow: 0 24px 70px rgba(17, 17, 17, 0.045);
                overflow: hidden;
            }

            .panel::before,
            .panel::after {
                content: "";
                position: absolute;
                border-radius: 999px;
                filter: blur(24px);
            }

            .panel::before {
                width: 180px;
                height: 180px;
                top: -36px;
                right: -24px;
                background: rgba(242, 239, 233, 0.9);
            }

            .panel::after {
                width: 140px;
                height: 140px;
                left: -18px;
                bottom: -26px;
                background: rgba(247, 246, 243, 0.92);
            }

            .panel-inner {
                position: relative;
                z-index: 1;
            }

            .panel-label {
                margin: 0;
                font-size: 0.72rem;
                letter-spacing: 0.28em;
                text-transform: uppercase;
                color: var(--muted);
            }

            .panel-card {
                margin-top: 18px;
                padding: 26px;
                border-radius: 24px;
                background: rgba(255, 255, 255, 0.85);
                border: 1px solid rgba(17, 17, 17, 0.05);
            }

            .panel-card h2 {
                margin: 0;
                font-family: "Playfair Display", ui-serif, Georgia, serif;
                font-size: 2rem;
                line-height: 1.04;
                color: var(--ink);
            }

            .panel-card p {
                margin: 16px 0 0;
                font-size: 0.98rem;
                line-height: 1.9;
                color: var(--muted);
            }

            .panel-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 22px;
            }

            .panel-meta span {
                display: inline-flex;
                align-items: center;
                min-height: 34px;
                padding: 0 14px;
                border-radius: 999px;
                background: var(--paper-soft);
                color: var(--muted);
                font-size: 0.7rem;
                letter-spacing: 0.24em;
                text-transform: uppercase;
            }

            @media (max-width: 980px) {
                body {
                    overflow: auto;
                }

                .frame {
                    min-height: auto;
                }

                .content {
                    grid-template-columns: 1fr;
                    padding-top: 12px;
                }
            }

            @media (max-width: 720px) {
                .shell {
                    padding: 14px;
                }

                .frame {
                    border-radius: 24px;
                }

                .nav,
                .content {
                    padding-left: 22px;
                    padding-right: 22px;
                }

                .nav {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .nav-links {
                    gap: 18px;
                    flex-wrap: wrap;
                }

                .panel {
                    padding: 20px;
                }

                .panel-card {
                    padding: 20px;
                }
            }
        </style>
    </head>
    <body>
        <div class="shell">
            <div class="frame">
                <header class="nav">
                    <a class="brand" href="/#top">Jennifer Williams</a>

                    <nav class="nav-links">
                        <a href="/#about">About</a>
                        <a href="/#projects">Projects</a>
                        <a href="/#footer">Contact</a>
                    </nav>
                </header>

                <main class="content">
                    <section class="copy">
                        <p class="eyebrow">Page Not Found</p>
                        <h1 class="title">404</h1>
                        <p class="subtitle">
                            The page you were looking for has drifted out of frame. Return to the portfolio and continue through the work.
                        </p>

                        <div class="actions">
                            <a class="button primary" href="/#top">Return Home</a>
                            <a class="button" href="/#projects">View Projects</a>
                        </div>
                    </section>

                    <aside class="panel" aria-hidden="true">
                        <div class="panel-inner">
                            <p class="panel-label">Quiet Direction</p>

                            <div class="panel-card">
                                <h2>Nothing here, but the path forward is clear.</h2>
                                <p>
                                    A softer route back to the homepage, the projects archive, or the contact section is just below.
                                </p>

                                <div class="panel-meta">
                                    <span>Home</span>
                                    <span>Projects</span>
                                    <span>Contact</span>
                                </div>
                            </div>
                        </div>
                    </aside>
                </main>
            </div>
        </div>
    </body>
</html>
