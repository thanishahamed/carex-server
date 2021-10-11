<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successfull</title>
</head>
<body>
    <h1> Thank You {{$name}} For Registering With Care-X Social Services </h1>
    <h3> One more step to go... </h3>
    <p> Please click this link to verify your email! </p>
    <a href = "{{ $link }}" > Click here to verify your email </a>
</body>
</html>