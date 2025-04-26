<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome Aboard</title>
</head>
<body>
<h1>Hello {{ $user->name }}!</h1>

<p>Your account has been successfully created.</p>

<p><strong>Email:</strong> {{ $user->email }}</p>
<p><strong>Your password is developer123*</strong></p>
<p>you can change your password from your profile or contact an administrator.</p>

<br>
<p>We're excited to have you on board. Let's build something great together!</p>

<p>— The Team</p>
</body>
</html>
