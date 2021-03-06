<?php
/**
 * PHP Template Compiler v2.0.
 */

ini_set('display_errors', 1);   # It is for testing

/** Alias for DIRECTORY_SEPARATOR */
define ('DS', DIRECTORY_SEPARATOR);
/** Alias for line feed */
define ('LF', PHP_EOL);
/** Root directory */
define ('ROOT',      '.'.DS);
/** Directory for caching compiled templates */
define ('CACHE',     ROOT.'cache'.DS);
/** Translations */
define ('LANGUAGES', ROOT.'languages'.DS);
/** Example styles */
define ('STYLES',    ROOT.'css'.DS);
/** Example templates */
define ('TEMPLATES', ROOT.'templates'.DS);
/** Generator */
define('GENERATOR', 'template-compiler v2.0');
/** Copyright */
define('COPYRIGHT', '&copy; 2016 Greenray');

require_once 'template.class.php';

$options['cache_page'] = TRUE;
$options['cache_css']  = FALSE;
$options['expired']    = 3600;
$options['compact']    = TRUE;
$options['language']   = 'english';
$options['extension']  = '.tpl.php';

$locales = glob(LANGUAGES.'*.php');
if (!empty($_POST['language'])) {
    if (in_array(LANGUAGES.$_POST['language'].'.php', $locales, TRUE)) {
        $options['language'] = $_POST['language'];
        $options['expired']  = 0;
    }
}

$languages = [];
foreach ($locales as $key => $filename) {
    $file_info = pathinfo($filename);
    $languages[$key]['language'] = $file_info['filename'];
    if ($languages[$key]['language'] === $options['language']) {
        $languages[$key]['selected'] = TRUE;
    }
}
unset ($filename);

$menu    = json_decode(file_get_contents('menu.json'), TRUE);
$content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam arcu ligula, faucibus eu imperdiet eu, bibendum sit amet augue. Sed turpis sem, interdum sit amet egestas a, mattis non libero. Suspendisse tristique nisi sed justo accumsan vel mattis nulla fermentum. Etiam varius est id mi fermentum aliquam.';

$TEMPLATE = new TEMPLATE('main', $options);
$TEMPLATE->set('class',     'content');
//$TEMPLATE->set('title',     'Included content');
$TEMPLATE->set('title',     'Content');
$TEMPLATE->set('size',      '2');
$TEMPLATE->set('content',   $content);
$TEMPLATE->set('languages', $languages);
$TEMPLATE->set('menu',      $menu);

echo $TEMPLATE->parse();
