<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- So that mobile will display zoomed in -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- enable media queries for windows phone 8 -->
    <meta name="format-detection" content="telephone=no"> <!-- disable auto telephone linking in iOS -->
    <title>Send Email</title>
    <style>
        .content {
            box-shadow: 0px 2px 3px grey;
            width: 600px;
            margin: auto;
            background: rgb(255, 255, 255);
            border-radius: 4px;
            padding: 40px;
        }

        .img {
            border: 1px dashed grey;
            margin-top: 20px;
        }
        .img img{
            width: 500px;
        }
    </style>
</head>

<body style="background:rgb(255, 232, 232);padding:100px;">
    <div class="content">
        {{-- $title come from array $mailData --}}
        <h1 style="text-align: center;">{{ $title ?? '' }}</h1>
        <p style="text-align: center;">Click <a href="{{ $link ?? ''}}"><b>link</b></a> to reset your password</p>
    </div>
</body>

</html>
