<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>AssetWise | Office Asset Management System</title>

    <!-- Bootstrap 5 + Icons + Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            color: #1e2a3e;
            scroll-behavior: smooth;
        }

        :root {
            --primary: #1a66ff;
            --primary-dark: #0a56e0;
            --primary-light: #eef3ff;
            --border-light: #eef2f6;
            --text-dark: #1e2f3e;
            --text-muted: #5a6e7c;
        }

        .btn-primary-custom {
            background-color: var(--primary);
            border: none;
            padding: 0.65rem 1.8rem;
            font-weight: 600;
            border-radius: 40px;
            transition: all 0.2s ease;
            color: white;
            box-shadow: 0 4px 8px rgba(26, 102, 255, 0.12);
        }
        .btn-primary-custom:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(26, 102, 255, 0.2);
        }
        .btn-outline-custom {
            border: 1.5px solid var(--primary);
            color: var(--primary);
            background: transparent;
            border-radius: 40px;
            padding: 0.6rem 1.7rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-outline-custom:hover {
            background-color: var(--primary-light);
            transform: translateY(-1px);
            border-color: var(--primary-dark);
            color: var(--primary-dark);
        }
        .navbar {
            backdrop-filter: blur(12px);
            background-color: rgba(255, 255, 255, 0.92);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.02);
            border-bottom: 1px solid var(--border-light);
            padding: 0.7rem 0;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.6rem;
            letter-spacing: -0.3px;
            color: var(--text-dark);
        }
        .navbar-brand span {
            color: var(--primary);
        }
        .nav-link {
            font-weight: 500;
            color: var(--text-dark);
            margin: 0 0.25rem;
            transition: 0.2s;
        }
        .nav-link:hover {
            color: var(--primary);
        }

        .carousel-item {
            background: #eef4fa;
            max-height: 560px;
        }
        .carousel-item img {
            object-fit: cover;
            height: 560px;
            width: 100%;
            filter: brightness(0.94) contrast(1.02);
        }
        .carousel-caption {
            background: linear-gradient(to top, rgba(0, 0, 0, 0.6), transparent);
            border-radius: 2rem;
            bottom: 2rem;
            padding: 1.8rem 2rem;
            left: 5%;
            right: 5%;
            max-width: 750px;
            margin: 0 auto;
            text-align: left;
        }
        .carousel-caption h5 {
            font-size: 2.4rem;
            font-weight: 700;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.02em;
        }
        .carousel-caption p {
            font-size: 1.15rem;
            font-weight: 400;
            text-shadow: 0 1px 6px rgba(0, 0, 0, 0.25);
            opacity: 0.95;
        }
        .carousel-caption .slide-tag {
            display: inline-block;
            background: rgba(26, 102, 255, 0.85);
            padding: 0.2rem 1.2rem;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        @media (max-width: 768px) {
            .carousel-item img {
                height: 380px;
            }
            .carousel-caption {
                padding: 1rem 1.2rem;
                bottom: 1rem;
            }
            .carousel-caption h5 {
                font-size: 1.4rem;
            }
            .carousel-caption p {
                font-size: 0.85rem;
            }
            .carousel-caption .slide-tag {
                font-size: 0.6rem;
                padding: 0.1rem 0.8rem;
            }
        }

        .feature-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 2rem 1.5rem;
            transition: all 0.25s ease;
            border: 1px solid var(--border-light);
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.02);
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 22px 32px -12px rgba(0, 0, 0, 0.08);
            border-color: rgba(26, 102, 255, 0.2);
        }
        .feature-icon {
            width: 64px;
            height: 64px;
            background: var(--primary-light);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        .feature-icon i {
            font-size: 2rem;
            color: var(--primary);
        }
        .section-title {
            font-weight: 700;
            font-size: 2.4rem;
            letter-spacing: -0.02em;
            color: var(--text-dark);
        }
        .section-badge {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
            padding: 0.3rem 1rem;
            border-radius: 30px;
            display: inline-block;
            font-size: 0.85rem;
        }
        .stat-card {
            background: #ffffff;
            border-radius: 28px;
            border: 1px solid var(--border-light);
            padding: 1.8rem 1rem;
            transition: 0.2s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        }
        .stat-number {
            font-size: 2.7rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1.2;
        }
        .bg-soft-accent {
            background-color: #f6fafe;
        }
        footer {
            background: #ffffff;
            border-top: 1px solid var(--border-light);
        }
        .footer-link {
            color: var(--text-muted);
            text-decoration: none;
            transition: 0.2s;
        }
        .footer-link:hover {
            color: var(--primary);
        }
        .btn-register-cta {
            background-color: var(--primary);
            color: white;
            border-radius: 40px;
            padding: 0.7rem 2rem;
            font-weight: 600;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 6px 14px rgba(26, 102, 255, 0.2);
        }
        .btn-register-cta:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            color: white;
        }
        .btn-login-outline {
            border: 1px solid #cddfe7;
            background: white;
            border-radius: 40px;
            padding: 0.55rem 1.4rem;
            font-weight: 500;
            color: var(--text-dark);
        }
        .btn-login-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #fafcff;
        }
        a {
            text-decoration: none;
        }
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: rgba(26, 102, 255, 0.4);
            border-radius: 50%;
            padding: 1.8rem;
            background-size: 50%;
        }
    </style>
</head>
<body>

    <!-- ==================== NAVBAR ==================== -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                Asset<span>Wise</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
            aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
                <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                <li class="nav-item"><a class="nav-link" href="#solutions">Solutions</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
            </ul>
            <div class="d-flex gap-2 align-items-center">
                <a href="/login" class="btn btn-login-outline">Log in</a>
                <a href="/register" class="btn btn-primary-custom">Register →</a>
            </div>
        </div>
    </div>
</nav>

<!-- ==================== SLIDER WITH REAL IMAGES ==================== -->
<div id="assetCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#assetCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#assetCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#assetCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">

        <!-- SLIDE 1 – Real image: Laptop on an office desk -->
        <div class="carousel-item active">
            <img src="https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=1900&h=650&fit=crop&crop=center" class="d-block w-100" alt="Laptop with GPS tracker in office" />
            <div class="carousel-caption d-md-block">
                <span class="slide-tag"><i class="bi bi-laptop me-1"></i> GPS Tracker</span>
                <h5>Laptop with integrated GPS tracker</h5>
                <p>Every office laptop is equipped with a tracker device – know its location at all times.</p>
            </div>
        </div>

        <!-- SLIDE 2 – Real image: Desktop screen with analytics dashboard -->
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1900&h=650&fit=crop&crop=center" class="d-block w-100" alt="Asset management dashboard with KPI cards on desktop screen" />
            <div class="carousel-caption d-md-block">
                <span class="slide-tag"><i class="bi bi-bar-chart-fill me-1"></i> Dashboard &amp; KPIs</span>
                <h5>Live asset management dashboard</h5>
                <p>Real‑time KPI cards show total assets, status, utilization, and alerts – all at a glance.</p>
            </div>
        </div>

        <!-- SLIDE 3 – Real image: Map with location pins -->
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1524661135-423995f22d0b?w=1900&h=650&fit=crop&crop=center" class="d-block w-100" alt="Google Maps showing tracked asset locations with markers" />
            <div class="carousel-caption d-md-block">
                <span class="slide-tag"><i class="bi bi-geo-alt me-1"></i> Live Map</span>
                <h5>Track every asset on the map</h5>
                <p>Each point represents an asset – real‑time positions updated via GPS and RFID trackers.</p>
            </div>
        </div>

    </div>

    <!-- Carousel controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#assetCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#assetCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- ==================== FEATURE SECTION ==================== -->
<section id="features" class="py-5 py-md-7" style="padding: 5rem 0;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-badge">Core Capabilities</span>
            <h2 class="section-title mt-3">Intelligent office asset intelligence<br /> at your fingertips</h2>
            <p class="text-muted mx-auto mt-3" style="max-width: 680px;">Designed for modern offices, IT departments, and facility managers who demand total accountability for every device.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-geo-alt-fill"></i></div>
                    <h4 class="fw-bold">Real‑Time GPS Tracking</h4>
                    <p class="text-muted mt-2">Interactive maps with live location updates. Know the precise position of laptops, printers, and high-value equipment 24/7.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-shield-lock-fill"></i></div>
                    <h4 class="fw-bold">Geofence &amp; Alerts</h4>
                    <p class="text-muted mt-2">Set custom virtual boundaries. Get instant push or email alerts whenever assets enter or exit sensitive areas.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-bar-chart-steps"></i></div>
                    <h4 class="fw-bold">Audit &amp; History Logs</h4>
                    <p class="text-muted mt-2">Full movement history, utilization reports, and tamper-proof digital trails — perfect for compliance and audits.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-phone"></i></div>
                    <h4 class="fw-bold">Multi‑Platform Sync</h4>
                    <p class="text-muted mt-2">Seamless experience across web, tablet, and mobile – manage your assets from anywhere.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-diagram-3"></i></div>
                    <h4 class="fw-bold">Smart Analytics</h4>
                    <p class="text-muted mt-2">AI-powered insights, predictive maintenance, and asset performance dashboards to reduce loss.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-cloud-check"></i></div>
                    <h4 class="fw-bold">Enterprise Security</h4>
                    <p class="text-muted mt-2">End-to-end encryption, role-based access, and SSO ready. Your data is always protected.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== STATS / TRUST ==================== -->
<section class="bg-soft-accent py-5 py-md-6">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <span class="section-badge">Performance driven</span>
                <h2 class="mt-3 fw-bold display-6" style="font-size: 2.2rem;">Trusted by forward‑thinking organizations</h2>
                <p class="text-muted mt-3">From corporate offices to large campuses, AssetWise delivers proactive asset control and significant ROI.</p>
                <div class="mt-4">
                    <a href="/register" class="btn-register-cta">Start free trial <i class="bi bi-arrow-right-short"></i></a>
                    <a href="#solutions" class="btn-outline-custom ms-2 d-inline-block">Learn more</a>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="row g-4">
                    <div class="col-sm-6">
                        <div class="stat-card text-center">
                            <div class="stat-number">99.9%</div>
                            <p class="fw-semibold mt-2 mb-0">Tracking Uptime</p>
                            <small class="text-muted">Reliable real‑time data</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="stat-card text-center">
                            <div class="stat-number">40%</div>
                            <p class="fw-semibold mt-2 mb-0">Reduction in asset loss</p>
                            <small class="text-muted">Geofence &amp; alerts efficiency</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="stat-card text-center">
                            <div class="stat-number">10k+</div>
                            <p class="fw-semibold mt-2 mb-0">Active assets tracked</p>
                            <small class="text-muted">Worldwide deployments</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="stat-card text-center">
                            <div class="stat-number">24/7</div>
                            <p class="fw-semibold mt-2 mb-0">Monitoring support</p>
                            <small class="text-muted">Dedicated assistance</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== SOLUTIONS SPOTLIGHT ==================== -->
<section id="solutions" class="py-5 py-md-6">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&h=500&fit=crop&crop=center" class="img-fluid rounded-4 shadow-sm" alt="Office asset management dashboard preview" style="border-radius: 2rem;" />
            </div>
            <div class="col-lg-6 order-lg-1 pe-lg-5">
                <span class="section-badge">Asset intelligence hub</span>
                <h2 class="fw-bold mt-2" style="font-size: 2rem;">Complete lifecycle management</h2>
                <p class="text-muted mt-3">From procurement to retirement, know every detail. Seamless integrations with ERP, real-time dashboards, and automated maintenance reminders.</p>
                <ul class="list-unstyled mt-4">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> <strong>Centralized asset registry</strong> – Barcode / RFID / GPS tags</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> <strong>Predictive maintenance alerts</strong> – avoid costly downtime</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> <strong>Custom reports &amp; exports</strong> – PDF, Excel, API access</li>
                </ul>
                <div class="mt-4">
                    <a href="/register" class="btn-primary-custom">Get started today <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== CALL TO ACTION ==================== -->
<section class="py-5" style="background: linear-gradient(120deg, #f4f9ff 0%, #ffffff 100%);">
    <div class="container text-center py-4">
        <h3 class="fw-bold display-6" style="font-size: 2rem;">Ready to take full control of your office assets?</h3>
        <p class="text-muted mx-auto mt-3" style="max-width: 600px;">Join hundreds of organizations that trust AssetWise for real-time asset visibility and security.</p>
        <div class="d-flex flex-wrap gap-3 justify-content-center mt-4">
            <a href="/register" class="btn-register-cta px-5 py-3 fs-6">Create free account →</a>
            <a href="/login" class="btn-outline-custom px-5 py-3 fs-6">Log in to dashboard</a>
        </div>
        <p class="small text-muted mt-4">No credit card required | Full-featured trial</p>
    </div>
</section>

<!-- ==================== FOOTER ==================== -->
<footer class="pt-5 pb-4">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-5">
                <a class="navbar-brand" href="/">Asset<span>Wise</span></a>
                <p class="text-muted small mt-3">Intelligent office asset tracking, geofencing, and advanced analytics for modern enterprises and institutions.</p>
                <div class="d-flex gap-3 mt-3">
                    <i class="bi bi-twitter-x text-muted"></i>
                    <i class="bi bi-linkedin text-muted"></i>
                    <i class="bi bi-github text-muted"></i>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <h6 class="fw-bold">Platform</h6>
                <ul class="list-unstyled mt-2">
                    <li><a href="#features" class="footer-link small">Features</a></li>
                    <li><a href="#solutions" class="footer-link small">Solutions</a></li>
                    <li><a href="#" class="footer-link small">Pricing</a></li>
                    <li><a href="#" class="footer-link small">Integrations</a></li>
                </ul>
            </div>
            <div class="col-md-2 col-6">
                <h6 class="fw-bold">Resources</h6>
                <ul class="list-unstyled mt-2">
                    <li><a href="#" class="footer-link small">Documentation</a></li>
                    <li><a href="#" class="footer-link small">API status</a></li>
                    <li><a href="#" class="footer-link small">Support</a></li>
                    <li><a href="#" class="footer-link small">Security</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold">Legal &amp; Access</h6>
                <ul class="list-unstyled mt-2">
                    <li><a href="#" class="footer-link small">Privacy policy</a></li>
                    <li><a href="#" class="footer-link small">Terms of service</a></li>
                    <li><a href="/login" class="footer-link small">Sign in (Existing user)</a></li>
                    <li><a href="/register" class="footer-link small">Register new account</a></li>
                </ul>
            </div>
        </div>
        <hr class="my-4" style="background-color: #e9eef3;" />
        <div class="row">
            <div class="col text-center">
                <p class="small text-muted mb-0">&copy; 2025 AssetWise — Smart Office Asset Management System. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js">
</script>
<script>
    // Smooth anchor scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId !== "#" && targetId !== "#0" && targetId !== "") {
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    // Carousel autoplay
    const carousel = document.getElementById('assetCarousel');
    if (carousel) {
        new bootstrap.Carousel(carousel, {
            interval: 5000,
            wrap: true,
            touch: true
        });
    }
</script>
</body>
</html>
