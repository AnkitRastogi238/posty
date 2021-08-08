<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h2>Welcome to our Website{{ $user->name }}</h2>
    <p>
        Click <a href="/user/verify/{{$user->verifyUser->token}}">here</a> to verify email
    </p>
</body>
</html>