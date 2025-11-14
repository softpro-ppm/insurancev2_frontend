<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Custom Styles -->
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            overflow-x: hidden;
        }
        
        /* Animated background particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }
        
        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .auth-container {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            border-radius: 24px !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.2) !important;
            padding: 48px !important;
            position: relative;
            z-index: 10;
            animation: slideUp 0.8s ease-out;
            max-width: 420px;
            width: 100%;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .logo-icon {
            font-size: 32px;
            color: #ffffff;
        }
        
        .auth-title {
            color: #1a202c !important;
            font-size: 28px !important;
            font-weight: 700 !important;
            margin-bottom: 8px !important;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .auth-subtitle {
            color: #718096 !important;
            font-size: 16px !important;
            font-weight: 500 !important;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-label {
            display: block;
            color: #2d3748 !important;
            font-weight: 600 !important;
            margin-bottom: 8px !important;
            font-size: 14px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 16px;
            transition: color 0.3s ease;
        }
        
        .form-input {
            width: 100% !important;
            background: #f7fafc !important;
            border: 2px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 16px 16px 16px 48px !important;
            color: #2d3748 !important;
            font-size: 16px !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        }
        
        .form-input::placeholder {
            color: #a0aec0 !important;
            font-weight: 400 !important;
        }
        
        .form-input:focus {
            outline: none !important;
            border-color: #667eea !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
            transform: translateY(-1px);
        }
        
        .form-input:focus + .input-icon {
            color: #667eea;
        }
        
        .auth-button {
            width: 100% !important;
            background: linear-gradient(135deg, #667eea, #764ba2) !important;
            color: white !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 16px !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            margin-bottom: 24px !important;
            position: relative;
            overflow: hidden;
        }
        
        .auth-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .auth-button:hover::before {
            left: 100%;
        }
        
        .auth-button:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3) !important;
        }
        
        .auth-button:active {
            transform: translateY(0) !important;
        }
        
        .auth-link {
            text-align: center;
            color: #718096 !important;
            font-size: 14px !important;
            font-weight: 500 !important;
        }
        
        .auth-link a {
            color: #667eea !important;
            text-decoration: none !important;
            font-weight: 600 !important;
            transition: color 0.3s ease;
        }
        
        .auth-link a:hover {
            color: #764ba2 !important;
            text-decoration: underline !important;
        }
        
        .error-msg {
            background: linear-gradient(135deg, #fed7d7, #feb2b2) !important;
            border: 1px solid #fc8181 !important;
            border-radius: 8px !important;
            padding: 12px !important;
            margin-top: 8px !important;
            color: #c53030 !important;
            font-size: 14px !important;
            font-weight: 500 !important;
        }
        
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
        }
        
        .checkbox-wrapper input[type="checkbox"] {
            width: 18px !important;
            height: 18px !important;
            accent-color: #667eea;
            cursor: pointer;
        }
        
        .checkbox-wrapper label {
            color: #4a5568 !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            margin: 0 !important;
            cursor: pointer;
        }

        .forgot-password-link {
            color: #667eea !important;
            text-decoration: none !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            text-align: center !important;
            display: block !important;
            margin-bottom: 24px !important;
            transition: color 0.3s ease;
        }

        .forgot-password-link:hover {
            color: #764ba2 !important;
            text-decoration: underline !important;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
            color: #a0aec0;
            font-size: 14px;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }
        
        .divider span {
            padding: 0 16px;
            background: rgba(255, 255, 255, 0.95);
        }
        
        /* Responsive design */
        @media (max-width: 480px) {
            .auth-container {
                margin: 20px;
                padding: 32px 24px !important;
            }
            
            .auth-title {
                font-size: 24px !important;
            }
        }
    </style>
    
    <!-- Animated background particles -->
    <div class="particles" id="particles"></div>

    <div class="auth-container">
        <div class="logo-section">
            <div class="logo-container">
                <i class="fas fa-shield-alt logo-icon"></i>
            </div>
            <h2 class="auth-title">SoftPro IMS</h2>
            <p class="auth-subtitle">Sign in to your Insurance Management System</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input id="email" 
                           class="form-input" 
                           type="email" 
                           name="email" 
                           value="{{ old('email', 'admin@insurance.com') }}" 
                           required 
                           autofocus 
                           autocomplete="username" 
                           placeholder="admin@insurance.com">
                </div>
                @error('email')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input id="password" 
                           class="form-input" 
                           type="password" 
                           name="password" 
                           required 
                           autocomplete="current-password" 
                           placeholder="Enter your password">
                </div>
                @error('password')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin: 24px 0;">
                <div class="checkbox-wrapper" style="margin: 0;">
                    <input id="remember_me" type="checkbox" name="remember">
                    <label for="remember_me">Remember me</label>
                </div>
                
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-password-link" style="margin: 0; font-size: 13px;">
                        Forgot password?
                    </a>
                @endif
            </div>

            <button type="submit" class="auth-button">
                <i class="fas fa-arrow-right" style="margin-right: 8px;"></i>
                Sign In
            </button>
        </form>
    </div>

    <!-- Include Font Awesome and Google Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- JavaScript for animated particles -->
    <script>
        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 50;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Random size between 2px and 6px
                const size = Math.random() * 4 + 2;
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                
                // Random position
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                
                // Random animation delay
                particle.style.animationDelay = Math.random() * 6 + 's';
                particle.style.animationDuration = (Math.random() * 4 + 4) + 's';
                
                particlesContainer.appendChild(particle);
            }
        }
        
        // Initialize particles when page loads
        document.addEventListener('DOMContentLoaded', createParticles);
        
        // Add smooth focus transitions
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('.input-icon').style.color = '#667eea';
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.querySelector('.input-icon').style.color = '#a0aec0';
                }
            });
        });
    </script>
</x-guest-layout>
