<x-guest-layout>
    <!-- Custom Styles -->
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .auth-container {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 20px !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
            padding: 40px !important;
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo-icon {
            font-size: 32px;
            color: #ffffff;
            margin-bottom: 16px;
        }
        
        .auth-title {
            color: #ffffff !important;
            font-size: 28px !important;
            font-weight: 600 !important;
            margin-bottom: 8px !important;
        }
        
        .auth-subtitle {
            color: rgba(255, 255, 255, 0.8) !important;
            font-size: 16px !important;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            color: #ffffff !important;
            font-weight: 500 !important;
            margin-bottom: 8px !important;
            font-size: 14px !important;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            font-size: 16px;
        }
        
        .form-input {
            width: 100% !important;
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            border-radius: 12px !important;
            padding: 16px 16px 16px 48px !important;
            color: #ffffff !important;
            font-size: 16px !important;
            transition: all 0.3s ease !important;
        }
        
        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.6) !important;
        }
        
        .form-input:focus {
            outline: none !important;
            border-color: rgba(255, 255, 255, 0.5) !important;
            background: rgba(255, 255, 255, 0.15) !important;
        }
        
        .auth-button {
            width: 100% !important;
            background: linear-gradient(135deg, #4F46E5, #6366F1) !important;
            color: white !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 16px !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            margin-bottom: 20px !important;
        }
        
        .auth-button:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4) !important;
        }
        
        .auth-link {
            text-align: center;
            color: rgba(255, 255, 255, 0.8) !important;
            font-size: 14px !important;
        }
        
        .auth-link a {
            color: #ffffff !important;
            text-decoration: none !important;
            font-weight: 600 !important;
        }
        
        .auth-link a:hover {
            text-decoration: underline !important;
        }
        
        .error-msg {
            background: rgba(239, 68, 68, 0.2) !important;
            border: 1px solid rgba(239, 68, 68, 0.4) !important;
            border-radius: 8px !important;
            padding: 12px !important;
            margin-top: 8px !important;
            color: #ffffff !important;
            font-size: 14px !important;
        }
    </style>

    <div class="auth-container">
        <div class="logo-section">
            <div class="logo-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h2 class="auth-title">Create Account</h2>
            <p class="auth-subtitle">Join Insurance Management System</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <input id="name" 
                           class="form-input" 
                           type="text" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           autofocus 
                           autocomplete="name" 
                           placeholder="Enter your full name">
                </div>
                @error('name')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input id="email" 
                           class="form-input" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="username" 
                           placeholder="Enter your email">
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
                           autocomplete="new-password" 
                           placeholder="Enter your password">
                </div>
                @error('password')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input id="password_confirmation" 
                           class="form-input" 
                           type="password" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password" 
                           placeholder="Confirm your password">
                </div>
                @error('password_confirmation')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="auth-button">
                <i class="fas fa-user-plus"></i>
                Create Account
            </button>
        </form>

        <div class="auth-link">
            Already have an account? 
            <a href="{{ route('login') }}">Sign in here</a>
        </div>
    </div>

    <!-- Include Font Awesome and Google Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</x-guest-layout>
