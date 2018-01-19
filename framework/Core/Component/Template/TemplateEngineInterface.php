<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 19.01.18
 */

namespace Artifly\Core\Component\Template;


interface TemplateEngineInterface
{
    const TEMPLATES_DIR = 'templates';

    /**
     * @param $template
     * @param $args
     *
     * @return mixed
     */
    public function render($template, $args);
}