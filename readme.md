Template Compiler.
==================

## Feachures

Handles special tags and replaces them with commands of the php interpreter.
Transforms template variables to php variables.
It is able recursively execute functions and directives:
FOREACH, IF, ELSE, ELSEIF, SWITCH, CASE, BREAK, DEFAULT, INCLUDE, CONTINUE.

There is a possibility of compressing and caching the result.

To control the compilation uses the HTML comment tags.
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

**{CONSTANT}** means **CONSTANT**

**$variable** means **$variable**

**$variable.index** means **$variable['index']**

**[$variable]** or **[$variable.index]** is used in special case, ex. **style="width:[$variable.width]px"**

**\__$variable\__** or **\__Any words\__** when you need translations into another language

## Example

    <div class="header center">__Example__</div>
        <!-- INCLUDE menu -->
    <div class="page">
    ...
    </div>

    This is template to include

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

    <div class="header center">Example</div>
    <div class="main-menu center">
        <ul class="menu">
            <li>
                <a href="#">Index</a>
            </li>
            <li>
                <a href="#">Publications</a>
                    <ul>
                        <li>
                            <a href="#" style="width:100px">News</a>
                        </li>
                        <li>
                            <a href="#" style="width:200px">Articles</a>
                        </li>
                    </ul>
                </li>
        </ul>
    </div>
    <div class="page">
    ...
    </div>

After finishing (removing newlines) the data file will be placed in one line.

The original code: (https://github.com/Greenray/css-optimizer).

#### COPYRIGHT AND LICENSE

Copyright (C) 2016 Victor Nabatov <greenray.spb@gmail.com>

This program is free software.
You can redistribute it and/or modify it under the terms of the
[Creative Commons Attribution-ShareAlike 4.0 International License](https://creativecommons.org/licenses/by-sa/4.0/legalcode) .
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

Эта программа является свободной.
Вы можете распространять и/или модифицировать ее в соответствии c условиями
[Creative Commons Attribution-ShareAlike 4.0 International License](https://creativecommons.org/licenses/by-sa/4.0/legalcode) .
Эта программа распространяется в надежде что она будет полезной, но БЕЗ КАКИХ-ЛИБО ГАРАНТИЙ;
даже без подразумеваемых гарантий КОММЕРЧЕСКОЙ ЦЕННОСТИ или ПРИГОДНОСТИ ДЛЯ КОНКРЕТНОЙ ЦЕЛИ.