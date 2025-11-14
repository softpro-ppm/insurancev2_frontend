<section>
    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
            @error('current_password', 'updatePassword')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="update_password_password">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password" />
            @error('password', 'updatePassword')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
            @error('password_confirmation', 'updatePassword')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: flex; align-items: center; gap: 16px;">
            <button type="submit" class="btn-primary">
                <i class="fas fa-lock"></i> {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    style="color: #10B981; font-size: 14px; font-weight: 500;"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
