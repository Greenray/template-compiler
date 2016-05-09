<?php
# PHP Template Compiler v1.0
# Copyright (c) 2016 Victor Nabatov greenray.spb@gmail.com
# Main template
die();?>

<!DOCTYPE html>
<html lang="__locale__">
<head>
    <meta charset="__encoding__">
    <meta http-equiv="Content-Type" content="text/html; charset=__encoding__" />
    <meta name="resource-type" content="document" />
    <meta name="document-state" content ="dynamic" />
    <title>__Example__</title>
    <link rel="stylesheet" type="text/css" href="{STYLES}normalize.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="{STYLES}style.css" media="screen" />
</head>
<body>
<div id="wrapper">
    <div class="header center">__Example__</div>
    <!-- INCLUDE menu -->
    <div class="page">
        <div id="layout-root">
            <div id="layout-left" class="justify">
                <h2>Lorem ipsum</h2>
                <p>Quisque quis vestibulum turpis. Sed venenatis ipsum laoreet elit pulvinar vitae pharetra massa dignissim. Curabitur ligula sapien, auctor ut porttitor a, ultricies lobortis dui. Suspendisse lacinia tellus a diam rutrum rhoncus.</p>
            </div>
            <div id="layout-right" class="justify">
                <h1>Lorem ipsum</h1>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam arcu ligula, faucibus eu imperdiet eu, bibendum sit amet augue. Sed turpis sem, interdum sit amet egestas a, mattis non libero. Suspendisse tristique nisi sed justo accumsan vel mattis nulla fermentum. Etiam varius est id mi fermentum aliquam.</p>
            </div>
            <div id="layout-center" class="justify">
                <h2>Lorem ipsum</h2>
                <p>Quisque quis vestibulum turpis. Sed venenatis ipsum laoreet elit pulvinar vitae pharetra massa dignissim. Curabitur ligula sapien, auctor ut porttitor a, ultricies lobortis dui. Suspendisse lacinia tellus a diam rutrum rhoncus.</p>
                <!-- INCLUDE content -->
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="footer">
    <div class="valid center">
        <a href="http://www.php.net" class="valid-icon valid-icon-php"></a>
        <a href="http://creativecommons.org/licenses/by-sa/4.0/" class="valid-icon valid-icon-license"></a>
    </div>
    <div class="language center">
        <p>__Change the language__</p>
        <form name="interface" method="post" class="center">
            <select name="language" onchange="document.forms['interface'].submit()" title="__Language__">
            <!-- FOREACH $languages -->
                <option value="$languages.language" <!-- IF !empty($languages.selected) -->selected<!-- END -->>$languages.language</option>
            <!-- END -->
            </select>
        </form>
    </div>
</div>
</body>
</html>
