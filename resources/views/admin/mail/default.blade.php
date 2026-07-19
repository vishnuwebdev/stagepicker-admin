<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="MobileOptimized" content="width">
        <meta name="HandheldFriendly" content="true">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../../favicon-32x32.png">

        <title>Acthound</title>

    </head>
    <body >
        <?php
        $post = "<div style=' width:700px; margin:0 auto'> ";
        $post .= "<div style='background:#fff; border:#888 solid 1px; border-radius:15px; margin-top:20px; padding:20px; box-shadow:0 0 8px #999'>";
        $post .= "<div style='text-align:center;border-bottom:1px solid #ccc; padding-bottom:18px;padding-top:18px; background:#fff none repeat scroll 0 0;'> ";
        $post .= "<img style=' width:250px;' src='" . url('public/assets/img/stagepicker-logo.png') . "' /></div> ";
        $post .= "<div style='font-size:16px; padding-top:20px;'>";
        echo $post;
        ?>
        @yield('content')

        <br>
        <?php
        $post = "<p>Thank you,</p>";
        $post .= "<p>The Acthound Team</p>";
        $post .= "</div> </div> </div> ";
        echo $post;
        ?>
    </body>
</html>
