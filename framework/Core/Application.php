<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 18.01.18
 * Time: 21:33
 */

namespace Artifly\Core;


use Symfony\Component\HttpFoundation\Request;

/**
 * Class Application
 *
 * @package Artifly\Core
 */
class Application
{
    /**
     * @var Request
     */
    private $request;

    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->request = Request::createFromGlobals();
    }

    /**
     * @param Router $router
     *
     * @throws Exception\ControllerResponseError
     */
    public function run(Router $router)
    {
        $content = $router->dispatch($this->request);
        $this->printContent($content);
    }

    /**
     * @param $content
     */
    private function printContent($content)
    {
        echo $content;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }
}