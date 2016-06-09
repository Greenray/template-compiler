Template Compiler.
==================

## Feachures

Handles special tags and replaces them with commands of the php interpreter.
Transforms template variables to php variables.
It is able recursively execute functions and directives:
FOREACH, IF, ELSE, ELSEIF, SWITCH, CASE, BREAK, DEFAULT, INCLUDE, CONTINUE.

There is a possibility of compressing and caching the result.

To control the compilation used the HTML comment tags.
This makes the template friendly and intuitive even for the inexperienced designer.

## Requirements

This program requires PHP 5.4+

## List of template functions

<!-- FOREACH $array -->
<!-- FOREACH $array.index -->
<!-- CONTINUE -->
<!-- IF $variable -->
<!-- IF $var1 == $var2 -->
<!-- IF !empty($varable) -->
<!-- ELSEIF $variable -->
<!-- ELSEIF $var1 == $var2 -->
<!-- ELSEIF !empty($variable) -->
<!-- ELSE -->
<!-- SWITCH $variable -->
<!-- CASE $variable -->
<!-- BREAK -->
<!-- DEFAULT -->
<!-- INCLUDE filename -->
<!-- END --> Closes FOREACH IF SWITCH

## Template constants and variables

{CONSTANT}
$variable means $variable
$variable.index means $variable['index']
[$variable] or [$variable.index] is used in special case, ex. style="width:[$variable.width]px"
__ $variable __ or __ Any word __ when you need translations into another language

## Example

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

## Result

    <div class="header center">__Example__</div>
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

The original code: (https://github.com/Greenray/css-optimizer).
Copyright (C) 2016 Victor Nabatov <greenray.spb@gmail.com>
