<?php
# PHP Template Compiler v1.0
# Copyright (c) 2016 Victor Nabatov greenray.spb@gmail.com
# Menu template
die();?>

<div class="main-menu center">
    <ul class="menu">
    <!-- FOREACH $menu -->
        <li>
            <a href="$menu.link">__$menu.name__</a>
            <!-- IF !empty($menu.sections) -->
                <ul>
                <!-- FOREACH $menu.sections -->
                    <li><a href="$sections.link" style="width:[$sections.width]px">__$sections.title__</a></li>
                <!-- END -->
                </ul>
            <!-- END -->
        </li>
    <!-- END -->
    </ul>
</div>
