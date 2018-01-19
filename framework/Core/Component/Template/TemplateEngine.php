<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 19.01.18
 */

namespace Artifly\Core\Component\Template;


use Twig_Environment;

/**
 * Class TemplateEngine
 *
 * @package Artifly\Core
 */
class TemplateEngine implements TemplateEngineInterface
{
    /**
     * @var Twig_Environment
     */
    private $engine;

    /**
     * TemplateEngine constructor.
     *
     * @param $engine
     */
    public function __construct($engine)
    {
        /**
         * todo: сделать адаптер под твиг и передавать его сюда.
         * todo: для возможности передавать сюда любого наследника TemplateEngineInterface
         **/
        $this->engine = $engine;
    }

    /**
     * @param       $template
     * @param array $args
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render($template, $args = [])
    {
        return $this->engine->render($template, $args);
    }
}