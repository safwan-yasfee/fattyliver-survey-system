<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Invitation</title>
</head>
<body>
    <h1>Fatty Liver Research</h1>

    <p>Hello {{ $user->name }},</p>

    <p>
        An administrator has created a staff account for you.
        Use the link below to set your password.
    </p>

    <p>
        <a href="{{ $url }}">Set Up My Account</a>
    </p>

    <p>
        Copy this address into your browser if needed:
    </p>

    <p>{{ $url }}</p>

    <p>
        This link expires according to the application's
        password-reset expiry setting. Use the newest invitation
        if multiple invitations were issued.
    </p>

    <p>
        Your account also requires Admin approval before you can sign in.
        If you were not expecting this invitation, contact the administrator.
    </p>
</body>
</html>