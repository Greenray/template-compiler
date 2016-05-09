<?php
/**
 * PHP Template Compiler.
 *
 * @version   1.0
 * @author    Victor Nabatov <greenray.spb@gmail.com>
 * @copyright (c) 2016 Victor Nabatov
 * @license   Creative Commons Attribution-ShareAlike 4.0 International
 * @file      template.class.php
 * @package   Template
 * @overview  Completely separated from php code.
 *            Handles special tags and replaces them with commands of the php interpreter.
 *            It is able recursively execute functions and directives:
 *            FOREACH, IF, ELSE, ELSEIF, SWITCH, CASE, BREAK, DEFAULT, INCLUDE, CONTINUE.
 *            There is a possibility of compressing and caching the result.
 *            Requires PHP 5.4+
 */

class TEMPLATE {

    /** @var string Template content */
    private $code = '';

	/** @var string Name of the template file that is executing */
	private $file = '';

    /** @var array Current system locale */
	private $translation = [];

	/** @var integer Line of the template content */
	private $line = 0;

	/** @var array Array of the template options */
	private $options = [];

    /** @var array Template statements */
    private $statements = [
        'BREAK'    => '<?php break;?>',
        'CONTINUE' => '<?php continue;?>',
        'DEFAULT'  => '<?php default: ?>',
        'ELSE'     => '<?php } else { ?>',
        'END'      => '<?php } ?>'
    ];

    /** @var array Temporary variable for code processing */
    private $temp = [];

	/** @var array Array of the template variables */
	public $vars = [];

	/**
	 * Class constructor.
     *
	 * @param array $template Template file
     * @param array $options  Template parser options
	 */
	public function __construct($template, $options = []) {
		$this->options   = [
            'compact'    => TRUE,
            'cache_page' => TRUE,
            'cache_css'  => TRUE,
            'expired'    => 600,
            'language'   => 'english'
		];
        $this->options = array_replace($this->options, $options);
        $lang = [];

        include LANGUAGES.$this->options['language'].'.php';

        $this->translation = $lang;
        $this->getTemplate($template);
	}

    /**
	 * Reads template.
     *
	 * @param string $template Template filename with full path
	 */
	private function getTemplate($template) {
        $this->file = $template;
        $template   = basename($template).'.tpl.php';
        if (file_exists(TEMPLATES.$template)) {
            $this->code = file_get_contents(TEMPLATES.$template);
            #
            # Remove php code from template
            #
            $this->code = preg_replace("#<\?php(.*?)\?>\n{1,}#is", '', $this->code);
        }
	}

    /**
     * Sets a variables (plain or array) of the template.
     *
     * @param mixed $var   Variable name
     * @param mixed $value Variable value
     */
    public function set($var, $value = '') {
        if (is_array($var))
             $this->vars = array_merge($this->vars, $var);
        else $this->vars[$var] =$value;
    }

    /**
	 * Compiles the template file.
     *
	 * @return string Compiled template
	 */
	public function parse() {
        $old_file = $this->file;
		$old_line = $this->line;
		$code = '';
        #
		# Get code stored in cache file
        #
        $result = ($this->options['cache_page']) ? $this->getFromCache($this->file) : FALSE;
		if (!$result) {
            #
			# Extract and parse template
            #
            $this->line = 0;
            $tpl   = [];
            preg_match_all('#<!-- INCLUDE (.+?) -->#', $this->code, $match, PREG_SET_ORDER);
            if (!empty($match)) {
                foreach($match as $key => $file) {
                    $data = file_get_contents(TEMPLATES.$file[1].'.tpl.php');
                    $data = preg_replace("#<\?php(.*?)\?>\n{1,}#is", '', $data);
                    $this->code = str_replace($file[0], $data, $this->code);
                }
            }
            $lines = explode(LF, $this->code);
            $count = sizeof($lines);

            do {
                $tpl[$this->line] = '<?php $this->line='.($this->line + 1).';?>'.$this->parseLine($lines[$this->line]);
                ++$this->line;
            } while($this->line < $count);

            $code = preg_replace('#\[^;]?>([\s]*)<\?php#', '$1', implode(LF, $tpl));
            $code = preg_replace_callback("#\{([\-\w]+)\}#is",  [&$this, 'value'], $code);

/*
            To use css compression you need to download css-optimizer from https://github.com/Greenray/css-optimizer

            preg_match_all("#\<link rel=\"stylesheet\" type=\"text/css\" href=\"(.*?)\" media=\"screen\" /\>#is", $code, $matches);
            foreach($matches[1] as $key => $file) {
                $CSS  = new CSS($this->options['css_cache']);
                $code = str_replace($matches[0][$key], '<style type="text/css">'.$CSS->compress($file).'</style>', $code);
            }
*/
            #
            # Execute php code
            #
            ob_start();
            eval('?>'.$code.'<?php return TRUE; ?>');
            $result = ob_get_contents();
            $result = preg_replace_callback("#__(.*?)__#is", [&$this, 'translate'], $result);
            ob_end_clean();
            #
            # Store the data in the cache
            #
            if ($this->options['cache_page']) $this->toCache($this->file, $result);
            $this->line = $old_line;
            $this->file = $old_file;
        }
        return $this->compact($result);
	}

    /**
	 * Parses a line of code.
     *
	 * @param  string $code Line of code
	 * @return string       Code transformed in php code
	 */
	private function parseLine($code) {
        #
		# Empty line
        #
		if (!trim($code)) return $code;
        #
		# Get template expressions
        #
        preg_match_all('#<!-- ([A-Z]+)+? *([ \S]*?) -->#', $code, $match, PREG_SET_ORDER);
        if (!empty($match)) {
            foreach($match as $key => $expr) {
                $tmp = '#:'.$this->randomString(6).':#';
                if (empty($expr[2])) {
                    $this->temp[$tmp] = str_replace($expr[0], $this->statements[$expr[1]], $expr[0]);
                } else {
                    switch($expr[1]) {
                        case 'IF':
                            $this->temp[$tmp] = str_replace($expr[0], $this->_if($expr[2], FALSE), $expr[0]);
                        break;
                        case 'ELSEIF':
                            $this->temp[$tmp] = str_replace($expr[0], $this->_if($expr[2], TRUE), $expr[0]);
                        break;
                        case 'ELSE':
                            $this->temp[$tmp] = str_replace($expr[0], '<?php } else { $this->line='.($this->line + 1).';?>', $expr[0]);
                        break;
                        case 'FOREACH':
                            $this->temp[$tmp] = str_replace($expr[0], $this->_foreach($expr[2]), $expr[0]);
                        break;
                        case 'SWITCH':
                            $this->temp[$tmp] = str_replace($expr[0], $this->_switch($expr[2], FALSE), $expr[0]);
                        break;
                        case 'CASE':
                            $this->temp[$tmp] = str_replace($expr[0], $this->_case($expr[2], FALSE), $expr[0]);
                        break;
                    }
                }
                $code = str_replace($expr[0], $tmp, $code);
            }
        }
        #
		# Transform template variables to php code
        # The templates are:
        # $var, $var.index        - will be converted to $this-vars[$var], $var['index'] - ex.: <h1>$var.title</h1>
        # [$var], [$var.index]    - will be converte to $this-vars[$var], $var['index']  - ex.: width:[$var.with]px
        # __$var_, __$var.index__ - will be translated
        #
		$code = preg_replace_callback(
            '#\[*\$(?:[\w]+\.)?[\w]*[^_\W]\]*#',
            function($match) {
                return '<?php echo '.$this->createVar($match[0]).';?>';
            },
            $code
        );
        #
		# Transform saved template expressions to php code
        #
        foreach($this->temp as $key => $value) {
            $code = str_replace($key, $value, $code);
        }
        $this->temp = [];
        return $code;
	}

    /**
	 * Creates variable.
     *
	 * @param  string $code Template basic code
	 * @return string       PHP code
	 */
	private function createVar($code) {
        $code = str_replace(['[', ']'], '', $code);
		return preg_replace_callback(
            '#\$(?:([\w]*)\.)?([\w]*)#',
            function($match) {
                if (empty($match[1]))
                     return '$this->vars[\''.$match[2].'\']';
                else return '$'.$match[1].'[\''.$match[2].'\']';
            },
            $code
        );
	}

    /**
	 * Trasnforms the FOREACH structure in php code.
     * T
     *
	 * @param  string $param Parameters for FOREACH structure
	 * @return string        PHP code
	 */
	private function _foreach($param) {
        $param = explode(' ', $param);
        if (strpos($param[0], '.') !== FALSE) {
            $values   = explode('.', $param[0]);
            $array    = $values[0].'[\''.$values[1].'\']';
            $param[0] = '$'.$values[1];
        } else {
            $array = $this->createVar($param[0]);
        }
        if (empty($params[1]))
             return '<?php foreach('.$array.' as '.$param[0].') { ?>';
        else return '<?php foreach('.$array.' as '.$param[0].' => '.$param[1].' { ?>';
	}

    /**
	 * Transforms the IF structure in php code.
     * <pre>
     * The template is:
     *   <!-- IF !empty($var) -->
     * </pre>
	 * @param  string  $code   Code for IF structure
	 * @param  boolean $elseif Flag indicating if the structure is IF or ELSEIF
	 * @return string          PHP code
	 */
	private function _if($code, $elseif) {
		$code = $this->createVar($code);
		$else = '';
		if ($elseif) {
			$else = 'else';
			$code = '($this->line='.($this->line + 1).')&&'.$code;
		}
		return '<?php '.$else.'if ('.$code.') { ?>';
	}

    /**
	 * Transforms the SWITCH structure in php code.
     * <pre>
     * The template is:
     *   <!-- SWITCH $var -->
     * </pre>
	 * @param  string $param Code for the SWITCH structure
	 * @return string        PHP code
	 */
	private function _switch($param) {
		$param = $this->createVar($param);
		return '<?php switch('.$param.') { ?>';
	}

    /**
	 * Transforms the CASE structure in php code.
     * <pre>
     * The template is:
     *   <!-- CASE $var -->
     * </pre>
	 * @param  string $param Parameter for CASE structure
	 * @return string        PHP code
	 */
	private function _case($param) {
		return '<?php case (($this->line='.($this->line + 1).')&&FALSE).'.$this->createVar($param).': ?>';
	}

    /**
     * Localization.
     * <pre>
     * The template is:
     *   __string__
     * </pre>
     * Array variable $matches contains:
     *  - $matches[0] = part of template between control structures including them;
     *  - $matches[1] = part of template between control structures excluding them.
     *
     * @param  array  $match Matches for translation
     * @return string        Parsed string
     */
    private function translate($match) {
        return str_replace($match[0], (empty($this->translation[$match[1]]) ? $match[1] : $this->translation[$match[1]]), $match[0]);
    }

    /**
     * Replaces constants and global variables with their values.
     * <pre>
     * The template is:
     *   {CONST} - global constant
     * </pre>
     *
     * @param  array  $match Matches for constants
     * @return string        Parsed string
     */
    private function value($match) {
        if (defined($match[1])) {
            return str_replace($match[0], constant($match[1]), $match[0]);
        }
    }

    /**
	 * Gets a data from the cache.
     *
	 * @param  string $file Page name
     * @return mixed        Page from cache
	 */
	public function getFromCache($file) {
        $file  = md5($file);
        if (file_exists(CACHE.$file)) {
            #
            # Visitor has changed website language
            #
            if ($this->options['expired'] === 0) {
                return FALSE;
            }
            if ((filemtime(CACHE.$file) + $this->options['expired']) > time()) {
                return file_get_contents(CACHE.$file);
            }
        }
        return FALSE;
	}

    /**
     * Compacts compiled template.
     *
     * @param  string $data Compiled template
     * @return string       Code without newlines and extra spaces
     */
    private function compact($data) {
        if ($this->options['compact']) {
            #
            # Remove two or more consecutive spaces
            #
            $data = preg_replace('# {2,}#', '', $data);
            #
            # Remove newline characters and tabs
            #
            return str_replace(["\r\n", "\r", "\n", "\t"], '', $data);
            return $data;
        }
        return $data;
    }

    /**
	 * Places the compiled data into cache.
     *
	 * @param string $file Page name
	 * @param string $data Page to store in the cache
	 */
	public function toCache($file, $data) {
        $file = md5($file);
        $data = $this->compact($data);
        file_put_contents(CACHE.$file, $data, LOCK_EX);
	}

    /**
     * Generates random string.
     *
     * @param  integer $num_chars The lenght of string to generate
     * @return string             Generated string
     */
    private function randomString($num_chars) {
        $chars = [
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
            '0','1','2','3','4','5','6','7','8','9','_'
        ];
        $max_chars = sizeof($chars) - 1;
        $result = '';
        for ($i = 0; $i < $num_chars; $i++) {
            $result .= $chars[mt_rand(0, $max_chars)];
        }
        return $result;
    }
}
