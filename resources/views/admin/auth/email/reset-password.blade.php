<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827;">
    <h2>Password Reset</h2>
    <p>You requested a password reset for your admin account.</p>
    <p>Click the link below to reset your password:</p>
    <p>
        <a href="{{ url('/admin/reset-password/' . ($token ?? '')) }}">Reset Password</a>
    </p>
    <p>If you did not request this, you can ignore this email.</p>
</body>
</html>
