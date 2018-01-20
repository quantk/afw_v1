<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 18.01.18
 */

namespace Artifly\Core\Http;

use Artifly\Core\Component\Container\Container;
use Artifly\Core\Component\Template\TemplateEngine;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class Controller
 *
 * @package Artifly\Core
 */
class Controller
{
//region SECTION: Fields
    /**
     * @var TemplateEngine
     */
    protected $templateEngine;
    /**
     * @var Container
     */
    protected $container;
//endregion Fields

//region SECTION: Constructor
    /**
     * @param TemplateEngine $templateEngine
     * @param Container      $container
     */
    public function setDefaultDependencies(
        TemplateEngine $templateEngine, Container $container
    )
    {
        $this->templateEngine = $templateEngine;
        $this->container = $container;
    }
//endregion Constructor

//region SECTION: Protected
    /**
     * @return TemplateEngine
     */
    protected function getTemplateEngine()
    {
        return $this->templateEngine;
    }

    /**
     * @param $serviceId
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function get($serviceId)
    {
        return $this->container->get($serviceId);
    }

    /**
     * @param       $data
     * @param int   $code
     * @param array $headers
     * @param bool  $json
     *
     * @return JsonResponse
     */
    protected function json($data, $code = 200, $headers = [], $json = true)
    {
        return new JsonResponse($data, $code, $headers, $json);
    }

    /**
     * @param $templateName
     * @param $args
     *
     * @return string
     */
    protected function render($templateName, $args = [])
    {
        return $this->templateEngine->render($templateName, $args);
    }
//endregion Protected
}