<?php
header("HTTP/1.1 503 Service Temporarily Unavailable");
header("Status: 503 Service Temporarily Unavailable");
header("Retry-After: 3600");
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Asmodine - Site en Maintenance</title>
    <meta name="description" content="Maintenance en cours - Nous vous invitons à revenir plus tard.">
    <style>
        img {
            max-height: 100%;
            max-width: 100%;
            width: auto;
            height: auto;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
        }
        .blog {
		   position: absolute:
		   top: 0;
		   left: 0;
		   right: 0;
		   bottom: 0;
		   width: 100%;
		   height: 100%;
		   text-indent: -9999px;
		}
    </style>
</head>
<body>
    <a class="blog" href="http://blog.asmodine.com"><img src="/img/maintenance.png"></a>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-114131767-1', 'auto');
    ga('send', 'pageview');
</script>
</body>
</html>