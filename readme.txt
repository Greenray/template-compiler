Template Compiler.
==================

----- FEACHURES -----

Handles special tags and replaces them with commands of the php interpreter.
It is able recursively execute functions and directives:
FOREACH, IF, ELSE, ELSEIF, SWITCH, CASE, BREAK, DEFAULT, INCLUDE, CONTINUE.

There is a possibility of compressing and caching the result.

----- REQUIREMENT -----

This program requires PHP 5.4+

----- EXAMPLE -----

/* File styles.css */

    <div class="header center">__Example__</div>
    <!-- INCLUDE menu -->
    <div class="page">...

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

----- RESULT -----

<div class="main-menu center">
        <ul class="menu">
            <li>
                <a href="#">Index</a>
            </li>
            <li>
                <a href="#">Publications</a>
                    <ul>
                        <li>
                            <a href="#" style="width:112px">News</a>
                        </li>
                        <li>
                            <a href="#" style="width:112px">Articles</a>
                        </li>
                    </ul>
                </li>
        </ul>
    </div>
    <div class="page">...

After finishing (removing newlines) the data file will be placed in one line.

The original code: https://github.com/Greenray/css-optimizer.
Copyright (C) 2016 Victor Nabatov greenray.spb@gmail.com
