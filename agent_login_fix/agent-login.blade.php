<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Custom Styles -->
    <style>
        body {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
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
            color: #000000 !important;
            font-size: 32px !important;
            font-weight: 700 !important;
            margin-bottom: 8px !important;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3) !important;
        }
        
        .auth-subtitle {
            color: #333333 !important;
            font-size: 18px !important;
            font-weight: 600 !important;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2) !important;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            color: #000000 !important;
            font-weight: 700 !important;
            margin-bottom: 8px !important;
            font-size: 16px !important;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2) !important;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            font-size: 16px;
        }
        
        .form-input {
            width: 100% !important;
            background: #ffffff !important;
            border: 3px solid #374151 !important;
            border-radius: 12px !important;
            padding: 16px 16px 16px 48px !important;
            color: #000000 !important;
            font-size: 18px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        }
        
        .form-input::placeholder {
            color: #4b5563 !important;
            font-weight: 500 !important;
            font-size: 16px !important;
        }
        
        .form-input:focus {
            outline: none !important;
            border-color: #059669 !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.2) !important;
            color: #000000 !important;
        }
        
        .auth-button {
            width: 100% !important;
            background: linear-gradient(135deg, #10b981, #059669) !important;
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
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4) !important;
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
        
        .back-link {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .back-link a {
            color: rgba(255, 255, 255, 0.8) !important;
            text-decoration: none !important;
            font-size: 14px !important;
        }
        
        .back-link a:hover {
            color: #ffffff !important;
            text-decoration: underline !important;
        }
    </style>

    <div class="auth-container">
        <div class="logo-section">
            <div class="logo-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <h2 class="auth-title">Agent Login</h2>
            <p class="auth-subtitle">Sign in to your agent account</p>
        </div>

        <div class="back-link">
            <a href="/">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>

        <form method="POST" action="{{ route('agent.login.store') }}">
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
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username" 
                           placeholder="Enter your agent email">
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

            <button type="submit" class="auth-button">
                <i class="fas fa-sign-in-alt"></i>
                Sign In as Agent
            </button>
        </form>

        <div class="auth-link">
            Not an agent? 
            <a href="{{ route('login') }}">Admin Login</a>
        </div>
    </div>

    <!-- Include Font Awesome and Google Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</x-guest-layout>