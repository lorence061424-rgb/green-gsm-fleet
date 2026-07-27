<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green GSM - Enterprise Fleet Portal Login</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #10B981;
            --primary-hover: #059669;
            --accent: #0284C7;
            --dark-bg: #0B132B;
            --card-bg: rgba(15, 23, 42, 0.75);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at 15% 20%, #0F382C 0%, #0B132B 50%, #030712 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #F8FAFC;
            position: relative;
            overflow-x: hidden;
        }

        /* Glowing Background Ambient Orbs */
        .ambient-orb-1 {
            position: absolute;
            top: -10%;
            left: -10%;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.25) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
            filter: blur(60px);
            z-index: 0;
            pointer-events: none;
        }

        .ambient-orb-2 {
            position: absolute;
            bottom: -15%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(2, 132, 199, 0.25) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
            filter: blur(60px);
            z-index: 0;
            pointer-events: none;
        }

        .login-wrapper {
            width: 100%;
            max-width: 960px;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5), 0 0 40px rgba(16, 185, 129, 0.15);
            overflow: hidden;
            z-index: 1;
        }

        /* Left Hero Showcase Section */
        .hero-section {
            background: linear-gradient(145deg, rgba(6, 78, 59, 0.6) 0%, rgba(15, 23, 42, 0.9) 100%);
            padding: 3.5rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            position: relative;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34D399;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.825rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            width: fit-content;
        }

        .stat-box {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 1.1rem;
            transition: transform 0.2s ease;
        }

        .stat-box:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.06);
        }

        /* Form Input Section */
        .form-section {
            padding: 3.5rem 3rem;
        }

        .form-control {
            background: rgba(30, 41, 59, 0.6) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            color: #F8FAFC !important;
            border-radius: 12px;
            padding: 0.8rem 1.1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #10B981 !important;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2) !important;
        }

        .input-group-text {
            background: rgba(30, 41, 59, 0.8) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            color: #94A3B8 !important;
            border-radius: 12px 0 0 12px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
            border-radius: 12px;
            padding: 0.85rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            width: 100%;
            transition: all 0.25 ease;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.35);
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            box-shadow: 0 12px 28px rgba(16, 185, 129, 0.5);
            transform: translateY(-1px);
        }

        /* Role Quick Selection Pills */
        .role-pill {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #CBD5E1;
            padding: 0.45rem 0.85rem;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .role-pill:hover {
            background: rgba(16, 185, 129, 0.2);
            border-color: #10B981;
            color: #FFFFFF;
            transform: translateY(-1px);
        }

        @media (max-width: 991.98px) {
            .hero-section {
                display: none;
            }
            .form-section {
                padding: 2.5rem 1.75rem;
            }
        }
    </style>
</head>
<body>

    <div class="ambient-orb-1"></div>
    <div class="ambient-orb-2"></div>

    <div class="login-wrapper">
        <div class="row g-0">
            
            <!-- Left Panel: Enterprise Branding & Live Specs -->
            <div class="col-lg-6 hero-section">
                <div>
                    <div class="hero-badge mb-4">
                        <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
                        100% ALL-ELECTRIC VINFAST FLEET
                    </div>
                    
                    <h2 class="fw-extrabold text-white display-6 mb-3" style="letter-spacing: -0.03em;">
                        Green GSM Fleet Management
                    </h2>
                    
                    <p class="text-slate-300 leading-relaxed mb-4" style="font-size: 0.95rem; color: #94A3B8;">
                        Enterprise transportation telemetry, zero-emission route planning, AI fuel/kWh prediction, and transport cost optimization for Metro Manila operations.
                    </p>

                    <!-- EV Specifications Grid -->
                    <div class="row g-3 mt-2">
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="d-flex align-items-center text-success mb-1">
                                    <i class="bi bi-ev-front fs-5 me-2"></i>
                                    <span class="fw-bold small text-white">VinFast EVs</span>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">Nerio Green, VF 8, VF e34</small>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="stat-box">
                                <div class="d-flex align-items-center text-info mb-1">
                                    <i class="bi bi-cpu fs-5 me-2"></i>
                                    <span class="fw-bold small text-white">AI Engine</span>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">Gradient Descent kWh ML</small>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="stat-box">
                                <div class="d-flex align-items-center text-warning mb-1">
                                    <i class="bi bi-shield-check fs-5 me-2"></i>
                                    <span class="fw-bold small text-white">Digital Tracking</span>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">AI Cameras & Safety Score</small>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="stat-box">
                                <div class="d-flex align-items-center text-primary mb-1">
                                    <i class="bi bi-telephone-fill fs-5 me-2"></i>
                                    <span class="fw-bold small text-white">Hotline Support</span>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">(02) 7777-8080</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer branding -->
                <div class="pt-4 border-top border-secondary border-opacity-25 mt-4">
                    <div class="d-flex align-items-center justify-content-between text-muted small">
                        <span>Team 7 Scope &bull; Capstone 2026</span>
                        <span class="badge bg-success bg-opacity-20 text-success">Metro Manila Fleet Active</span>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Modern Login Form -->
            <div class="col-lg-6 form-section d-flex flex-column justify-content-center">
                
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-shield-shaded text-success fs-2 me-2"></i>
                        <span class="fs-3 fw-bold text-white">Sign In to Portal</span>
                    </div>
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 small mb-3 bg-success bg-opacity-25 text-white fw-medium shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill text-success me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 small mb-3 bg-danger bg-opacity-25 text-white fw-medium shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-white">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0 text-white"><i class="bi bi-envelope text-success"></i></span>
                            <input type="email" id="emailInput" name="email" class="form-control border-start-0 text-white fw-medium" value="admin@greengsm.com" required placeholder="name@greengsm.com">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold text-white mb-0">Password</label>
                            <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-30" style="font-size: 11px;">Default: password</span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text border-end-0 text-white"><i class="bi bi-lock text-success"></i></span>
                            <input type="password" id="passwordInput" name="password" class="form-control border-start-0 border-end-0 text-white fw-medium" value="password" required>
                            <button class="btn btn-outline-secondary border border-start-0 text-white" type="button" id="togglePassword">
                                <i class="bi bi-eye text-white" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-submit mb-4 fs-6 py-3 fw-bold">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Access Green GSM Portal
                    </button>
                </form>

                <!-- Quick Role Selector Buttons -->
                <div class="pt-3 border-top border-secondary border-opacity-30">
                    <span class="d-block text-white small fw-bold mb-2">⚡ Quick 1-Click Role Access (Internal Staff):</span>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" onclick="fillRole('admin@greengsm.com')" class="role-pill">
                            <i class="bi bi-shield-check text-success me-1"></i> System Admin
                        </button>
                        <button type="button" onclick="fillRole('fleetmanager@greengsm.com')" class="role-pill">
                            <i class="bi bi-truck text-primary me-1"></i> Fleet Manager
                        </button>
                        <button type="button" onclick="fillRole('dispatcher@greengsm.com')" class="role-pill">
                            <i class="bi bi-calendar-event text-info me-1"></i> Dispatcher
                        </button>
                        <button type="button" onclick="fillRole('finance@greengsm.com')" class="role-pill">
                            <i class="bi bi-graph-up-arrow text-warning me-1"></i> Finance Officer
                        </button>
                        <button type="button" onclick="fillRole('operations@greengsm.com')" class="role-pill">
                            <i class="bi bi-speedometer2 text-secondary me-1"></i> Operations Manager
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function fillRole(email) {
        document.getElementById('emailInput').value = email;
        document.getElementById('passwordInput').value = 'password';
    }

    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('passwordInput');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        eyeIcon.classList.toggle('bi-eye');
        eyeIcon.classList.toggle('bi-eye-slash');
    });
    </script>
</body>
</html>
