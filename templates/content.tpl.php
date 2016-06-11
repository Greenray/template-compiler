<?php
# PHP Template Compiler v2.0
# Copyright (c) 2016 Victor Nabatov greenray.spb@gmail.com
# Content template
die();?>

<div>
    <!-- IF $title == 'Included content' -->
        <div class="title center"><h[$size]>__$title__</h[$size]></div>
    <!-- ELSE -->
        <div class="title center"><h[$size]>__This text is included__</h[$size]></div>
    <!-- END -->
    <div class="content">
        $content
    </div>
</div>
