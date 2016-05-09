<?php
/**
 * PHP Template Compiler.
 */

ini_set('display_errors', 1);   # Set this to 0 after testing

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

//require_once 'tpl.class.php';
require_once 'template.class.php';

$options['cache_page'] = TRUE;
$options['cache_css']  = FALSE;
$options['expired']    = 3600;
$options['compact']    = TRUE;
$options['language']   = 'english';

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
for ($i = 0; $i < 10; ++$i) {
    $digits[] = $i;
}

$TEMPLATE = new TEMPLATE('main', $options);
$TEMPLATE->set('class',     'content');
$TEMPLATE->set('title',     'Included content');
$TEMPLATE->set('size',      '2');
$TEMPLATE->set('content',   $content);
$TEMPLATE->set('languages', $languages);
$TEMPLATE->set('menu',      $menu);

echo $TEMPLATE->parse();
