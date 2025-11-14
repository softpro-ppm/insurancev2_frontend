# Quick User Creation Commands for Production Server

## Method 1: Using Artisan Tinker (Recommended)
Run this command on your production server:

```bash
cd public_html/v2insurance
php artisan tinker --execute="
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Create admin user
\$user = User::create([
    'name' => 'Admin',
    'email' => 'admin@test.com',
    'password' => Hash::make('password'),
    'email_verified_at' => now(),
]);

echo 'User created: ' . \$user->email . ' / password';
"
```

## Method 2: Using the PHP Script
1. Upload the `create_user.php` file to your server
2. Run: `php create_user.php`

## Method 3: Direct Database Insert (if needed)
```bash
cd public_html/v2insurance
php artisan tinker --execute="
DB::table('users')->insert([
    'name' => 'Admin',
    'email' => 'admin@test.com',
    'password' => bcrypt('password'),
    'email_verified_at' => now(),
    'created_at' => now(),
    'updated_at' => now(),
]);
echo 'User inserted directly into database';
"
```

## Alternative Credentials to Try:
If the above doesn't work, try these common Laravel Breeze defaults:
- Email: `admin@admin.com` / Password: `password`
- Email: `admin@example.com` / Password: `password`
- Email: `test@example.com` / Password: `password`

## Check Existing Users:
To see what users exist on your server:
```bash
php artisan tinker --execute="
\$users = App\Models\User::all();
foreach(\$users as \$user) {
    echo \$user->email . ' - ' . \$user->name . PHP_EOL;
}
"
```
