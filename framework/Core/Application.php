<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 18.01.18
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
//region SECTION: Fields
    /**
     * @var Request
     */
    private $request;
    /**
     * @var Container
     */
    private $container;
//endregion Fields

//region SECTION: Constructor
    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->request   = Request::createFromGlobals();
        $this->container = new Container();
        $this->container->addInstance($this->request);
    }
//endregion Constructor

//region SECTION: Public
    /**
     * @param Router $router
     *
     * @throws Exception\ControllerResponseError
     */
    public function run(Router $router)
    {
        $this->container->addInstance($router);
        $dispatchedRoute = $router->dispatch($this->request);
        switch ($dispatchedRoute->getDispatchType()) {
            case DispatchedRoute::ROUTE_FOUNDED:
                $content = call_user_func_array($dispatchedRoute->getHandler(), $dispatchedRoute->getArgs());
                $this->printContent($content);
                break;
            default:
                $this->redirectTo404();
        }
    }

    /**
     * @param string $url
     * @param int    $code
     */
    private function redirect(string $url, $code = 302)
    {
        header(sprintf('Location: %s', $url), true, $code);
    }


    private function redirectTo404(): void
    {
        $this->redirect('/404');
    }
//endregion Public

//region SECTION: Private
    /**
     * @param $content
     */
    private function printContent($content)
    {
        echo $content;
    }
//endregion Private

//region SECTION: Getters/Setters
    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }
//endregion Getters/Setters
}