<?php
echo '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL;
?>
<opml version="1.0">
    <head>
        <text>OPML-Export</text>
    </head>
    <body>
        <outline isOpen="true" id="1" text="">
            @yield('content')
        </outline>
    </body>
</opml>
