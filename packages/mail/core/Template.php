<?php

namespace Packages\Mail\Core;


class Template
{
    /**
     * @param $tpl_file
     */
    public function show ($tpl_file)
    {
        $tpl = $this;
        include(__DIR__ . "/../templates/{$tpl_file}");
    }

    /**
     * @param $tpl_file
     * @return false|string
     *
     * bigweb-ec/vendor/cakephp/cakephp/src/View/View.php
     */
    public function evaluate ($tpl_file)
    {
        $tpl = $this;
        ob_start();
        include(__DIR__ . "/../templates/{$tpl_file}");
        return ob_get_clean();
    }
}