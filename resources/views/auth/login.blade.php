<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - TNVS Fleet & Transportation Management System (Team 7)</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #0F172A;
        }

        .login-card {
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .login-header {
            background: #0F172A;
            color: white;
            padding: 2.5rem 2rem 2rem 2rem;
            text-align: center;
            border-bottom: 3px solid #4F46E5;
        }

        .login-body {
            padding: 2.25rem 2rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid #E2E8F0;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #4F46E5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        .btn-signin {
            background: #4F46E5;
            color: white;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border: none;
            width: 100%;
            transition: all 0.2s ease;
        }

        .btn-signin:hover {
            background: #4338CA;
            color: white;
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35);
        }

        .demo-btn {
            font-size: 0.8rem;
            border-radius: 8px;
            padding: 0.4rem 0.6rem;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="login-card">
    <!-- Header -->
    <div class="login-header" style="border-bottom: 3px solid #10B981;">
        <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-20 p-3 rounded-circle mb-3">
            <i class="bi bi-shield-shaded text-success fs-2"></i>
        </div>
        <h4 class="fw-bold mb-1">GREEN GSM</h4>
        <p class="text-white-50 small mb-0">Fleet & Transportation Management System</p>
    </div>

    <!-- Body -->
    <div class="login-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 small mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 small mb-3" role="alert">
                <i class="bi bi-exclamation-circle-fill me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold small text-muted">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted"><i class="bi bi-envelope"></i></span>
                    <input type="email" id="emailInput" name="email" class="form-control border-start-0 rounded-end-3" value="admin@greengsm.com" required>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label fw-semibold small text-muted mb-0">Password</label>
                    <small class="text-muted" style="font-size: 11px;">Default: password</small>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted"><i class="bi bi-lock"></i></span>
                    <input type="password" id="passwordInput" name="password" class="form-control border-start-0 rounded-end-3" value="password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-signin mb-4" style="background: #10B981;">
                <i class="bi bi-box-arrow-in-right me-2"></i> Sign In to Green GSM
            </button>
        </form>

        <!-- Capstone Defense Quick Role Selector -->
        <div class="pt-3 border-top">
            <span class="d-block text-muted small fw-semibold text-center mb-2">⚡ Quick Sign-In Options (Team 7 Internal Staff):</span>
            <div class="d-flex flex-wrap gap-1 justify-content-center">
                <button type="button" onclick="fillRole('admin@greengsm.com')" class="btn btn-sm btn-outline-success demo-btn">Admin</button>
                <button type="button" onclick="fillRole('fleetmanager@greengsm.com')" class="btn btn-sm btn-outline-primary demo-btn">Fleet Manager</button>
                <button type="button" onclick="fillRole('dispatcher@greengsm.com')" class="btn btn-sm btn-outline-info demo-btn">Dispatcher</button>
                <button type="button" onclick="fillRole('finance@greengsm.com')" class="btn btn-sm btn-outline-warning demo-btn">Finance</button>
                <button type="button" onclick="fillRole('operations@greengsm.com')" class="btn btn-sm btn-outline-secondary demo-btn">Operations</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function fillRole(email) {
    document.getElementById('emailInput').value = email;
    document.getElementById('passwordInput').value = 'password';
}
</script>
</body>
</html>
