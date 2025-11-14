<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="max-w-md w-full">
            <div class="register-container" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 20px; padding: 40px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
                <div class="register-header" style="text-align: center; margin-bottom: 40px;">
                    <div class="logo" style="display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 20px;">
                        <i class="fas fa-shield-alt" style="font-size: 32px; color: #ffffff;"></i>
                        <h1 style="font-size: 24px; font-weight: 700; color: #ffffff; margin: 0;">Insurance MS 2.0</h1>
                    </div>
                    <h2 style="color: #ffffff; font-size: 28px; font-weight: 600; margin-bottom: 8px;">Create Account</h2>
                    <p style="color: rgba(255, 255, 255, 0.8); font-size: 16px;">Join Insurance Management System</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="name" style="display: block; color: #ffffff; font-weight: 500; margin-bottom: 8px; font-size: 14px;">Full Name</label>
                        <div class="input-group" style="position: relative;">
                            <i class="fas fa-user" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: rgba(255, 255, 255, 0.7); font-size: 16px;"></i>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" 
                                   style="width: 100%; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; padding: 16px 16px 16px 48px; color: #ffffff; font-size: 16px; transition: all 0.3s ease;"
                                   placeholder="Enter your full name">
                        </div>
                        @error('name')
                            <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); border-radius: 8px; padding: 12px; margin-top: 8px; color: #ffffff; font-size: 14px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="email" style="display: block; color: #ffffff; font-weight: 500; margin-bottom: 8px; font-size: 14px;">Email Address</label>
                        <div class="input-group" style="position: relative;">
                            <i class="fas fa-envelope" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: rgba(255, 255, 255, 0.7); font-size: 16px;"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" 
                                   style="width: 100%; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; padding: 16px 16px 16px 48px; color: #ffffff; font-size: 16px; transition: all 0.3s ease;"
                                   placeholder="Enter your email">
                        </div>
                        @error('email')
                            <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); border-radius: 8px; padding: 12px; margin-top: 8px; color: #ffffff; font-size: 14px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="password" style="display: block; color: #ffffff; font-weight: 500; margin-bottom: 8px; font-size: 14px;">Password</label>
                        <div class="input-group" style="position: relative;">
                            <i class="fas fa-lock" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: rgba(255, 255, 255, 0.7); font-size: 16px;"></i>
                            <input id="password" type="password" name="password" required autocomplete="new-password" 
                                   style="width: 100%; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; padding: 16px 16px 16px 48px; color: #ffffff; font-size: 16px; transition: all 0.3s ease;"
                                   placeholder="Enter your password">
                        </div>
                        @error('password')
                            <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); border-radius: 8px; padding: 12px; margin-top: 8px; color: #ffffff; font-size: 14px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="password_confirmation" style="display: block; color: #ffffff; font-weight: 500; margin-bottom: 8px; font-size: 14px;">Confirm Password</label>
                        <div class="input-group" style="position: relative;">
                            <i class="fas fa-lock" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: rgba(255, 255, 255, 0.7); font-size: 16px;"></i>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                                   style="width: 100%; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; padding: 16px 16px 16px 48px; color: #ffffff; font-size: 16px; transition: all 0.3s ease;"
                                   placeholder="Confirm your password">
                        </div>
                        @error('password_confirmation')
                            <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); border-radius: 8px; padding: 12px; margin-top: 8px; color: #ffffff; font-size: 14px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" style="width: 100%; background: linear-gradient(135deg, #4F46E5, #6366F1); color: white; border: none; border-radius: 12px; padding: 16px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-bottom: 20px;">
                        <i class="fas fa-user-plus"></i>
                        Create Account
                    </button>
                </form>

                <div style="text-align: center; color: rgba(255, 255, 255, 0.8); font-size: 14px;">
                    Already have an account? 
                    <a href="{{ route('login') }}" style="color: #ffffff; text-decoration: none; font-weight: 600;">Sign in here</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
        }
        input:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.5) !important;
            background: rgba(255, 255, 255, 0.15) !important;
        }
        input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
        }
        a:hover {
            text-decoration: underline !important;
        }
    </style>

    <!-- Include Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</x-guest-layout>
