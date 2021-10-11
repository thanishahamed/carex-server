<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organ Available</title>
</head>
<body>
    <h1> An Organ is available! Please click below link and help someone or get help by our comunity now! </h1>
    <h3> Thank you {{$name}} for supporting Care-X Social Services </h3>
    <p> Please click the below link to view service </p>
    <a href = "{{ $link }}" > Click here to view and approve donation </a>
    <br><br><br>
    If the link doesn't work, please copy below url and paste in your url bar of the web browser. 
    Thank You!
    <br><br>
    {{ $link }}
</body>
</html>