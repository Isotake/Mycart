<?php

namespace Packages\Bmcart\App;


class Template
{

    public $layout = null;

    public $vars = [];

    public $vars_escaped = [];

    public $htmlescape_whitelist = [
        'morebuy_message' => 1,
        'debug_message' => 1,
        'item_entities' => [
            'comment' => 1,
        ],
    ];
    public $htmlescape_whitelist_key = null;

    protected function setLayout ($layout)
    {

    }

//    public function setVars ($vars)
//    {
//        $this->vars = $vars;
//    }

    /**
     * @param $name
     * @param null $value
     * @return $this
     *
     * vendor/cakephp/cakephp/src/View/ViewVarsTrait.php
     */
    public function setVars($name, $value = null)
    {
        if (is_array($name)) {
            if (is_array($value)) {
                $data = array_combine($name, $value);
            } else {
                $data = $name;
            }
        } else {
            $data = [$name => $value];
        }
        $this->vars = $data + $this->vars;
    }

    public function setVarsRecursive()
    {
//        $data = [];
//        foreach ($vars as $key => $var) {
//        	if (is_array($var)) {
//        	    $this->setVarsRecursive($var);
//			} else {
//                echo $key . ' : ' . $var . '<br>';
//				$data[$key] = $this->htmlescape($var);
//			}
//		}
//        $this->vars = $data + $this->vars;

//        $white_list = $this->getEscapeWhitelistKeys();
//        var_dump($white_list);echo '<br>';

//        foreach ($this->vars as $key => $value) {
//            if (is_array($value)) {
//                array_walk_recursive($value, function($item, $k, $key) use($white_list) {
//                    if (!in_array($key, $white_list)) {
//                        echo $key . '_' . $k . ' : ' . $item . '<br>';
//                    }
//                }, $key);
//            } else {
//                if (!in_array($key, $white_list)) {
//                    $this->htmlescape_whitelist_key[] = $key;
//                    echo $key . ' : ' . $value . '<br>';
//                }
//            }
//        }

    }

    public function element ($element_file, $vars = null)
    {
        /* template_fileで、<?= $this->>element("element_file", $vars) ?> */
        return $this->evaluate("elements/{$element_file}.tpl.html", $vars);

        /* template_fileで、<?php $this->>element("element_file", $vars) ?> */
        // $this->render("elements/{$element_file}.tpl.html");
    }

    public function css ($css_file, $vars = null)
    {
        return '<style type="text/css">' . $this->evaluate("css/{$css_file}.css", $vars) . '</style>';
    }

    public function load_js ($js_files)
    {
        $output = '';
        if (count($js_files) > 0) {
            foreach ($js_files as $js_file) {
                $output.= '<script language="JavaScript" src="' . $js_file . '"></script>';
            }
        }
        return $output;
    }

    public function custom_js ($js_file, $vars = null)
    {
        return '<script language="JavaScript">' . $this->evaluate("js/{$js_file}.js", $vars) . '</script>';
    }

    /**
     * @param $tpl_file
     */
    public function show ($tpl_file, $layout_file = null)
    {
        if ($layout_file) {
            $this->layout = $layout_file;
        } else {
            $this->layout = __DIR__ . "/../templates/layouts/bmcart.tpl.html";
        }

        $content = $this->evaluate($tpl_file, $this->vars);
        $tpl = $this->vars;
        include(__DIR__ . "/../templates/layouts/bmcart.tpl.html");
    }

    /**
     * @param $tpl_file
     */
    public function render ($file, $vars = null)
    {
//        $tpl = $this;
        include(__DIR__ . "/../templates/{$file}");
    }

    /**
     * @param $tpl_file
     * @return false|string
     *
     * bigweb-ec/vendor/cakephp/cakephp/src/View/View.php
     */
    public function evaluate ($file, $vars = null)
    {
//        $tpl = $this;

        $tpl = $vars;
        ob_start();
        include(__DIR__ . "/../templates/{$file}");
        return ob_get_clean();
    }

    /**
	 *
	 */
    protected function mycartHtmlescape ($value)
	{

        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);

	}

	public function h ($value) {

        return $this->mycartHtmlescape($value);

    }

	public function getEscapeWhitelistKeys ()
    {

        foreach ($this->htmlescape_whitelist as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $key_2nd => $value_2nd) {
                    if ($value) {
                        $this->htmlescape_whitelist_key[] = $key . '_' . $key_2nd;
                    }
                }
            } else {
                if ($value) {
                    $this->htmlescape_whitelist_key[] = $key;
                }
            }
        }

    }
}