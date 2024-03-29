<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 18.01.18
 */

namespace Artifly\Core;


use Artifly\Core\Component\Configuration\Configuration;
use Artifly\Core\Component\Configuration\ConfigurationManager;
use Artifly\Core\Component\Container\Container;
use Artifly\Core\Component\ORM\DBConnector;
use Artifly\Core\Component\ORM\EntityManager;
use Artifly\Core\Component\ORM\Exception\ORMException;
use Artifly\Core\Component\ORM\MysqlAdapter;
use Artifly\Core\Component\Router\DispatchedRoute;
use Artifly\Core\Component\Router\Router;
use Artifly\Core\Component\Template\TemplateEngine;
use Artifly\Core\Http\ActionHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;
use Twig_Loader_Filesystem;

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
    /**
     * @var string
     */
    private $frameworkPath = '';
    /**
     * @var string
     */
    private $corePath = '';
    /**
     * @var string
     */
    private $templatesPath = '';
    /**
     * @var TemplateEngine
     */
    private $templateEngine = null;
    /**
     * @var ConfigurationManager
     */
    private $configurationManager = null;
    /**
     * @var Logger
     */
    private $logger = null;
//endregion Fields

//region SECTION: Constructor
    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->parsePaths();

        $this->prepareConfiguration();

        $this->prepareLogger();

        $this->registerContainer();
        $this->registerTemplateEngine();
        $this->parseRequest();

        $entityManager = $this->connectoToDatabase();

        /**
         * Register base services in container
         */
        $this->container->addInstance($this->request);
        $this->container->addInstance($this->templateEngine);
        $this->container->addInstance($entityManager);
        $this->container->addInstance($this->logger);
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
        $dispatchedRoute = $router->dispatch($this->request->getPathInfo(), $this->request->getMethod());
        switch ($dispatchedRoute->getDispatchType()) {
            case DispatchedRoute::ROUTE_FOUNDED:
                $handlerType = $dispatchedRoute->getHandler() instanceof \Closure ? ActionHandler::CLOSURE_TYPE : ActionHandler::CONTROLLER_TYPE;
                $handler     = new ActionHandler($dispatchedRoute->getHandler(), $handlerType);
                $handler->setContainer($this->container);
                $content = $handler->execute($dispatchedRoute->getArgs());
                $this->printContent($content);
                break;
            default:
                $this->print404();
        }
    }
//endregion Public

//region SECTION: Private
    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function print404(): void
    {
        $response = new Response($this->templateEngine->render('404.html'), 404);
        $response->send();
    }

    /**
     * @param     $url
     * @param int $code
     */
    private function redirect($url, $code = 302): void
    {
        $response = new RedirectResponse($url, $code);
        $response->send();
    }

    /**
     * @param $content
     */
    private function printContent($content)
    {
        $response = $content instanceof Response ? $content : new Response($content, 200);
        $response->send();
    }

    /**
     * @return EntityManager
     * @throws ORMException
     */
    private function connectoToDatabase(): EntityManager
    {
        $mysqlAdapter = new MysqlAdapter(
            'localhost',
            'afw',
            'root',
            '12345'
        );

        $connector = new DBConnector($mysqlAdapter);
        $connector->connect();
        if (!$connector->isConnected()) {
            throw new ORMException('DB Connection failed');
        }

        $entityManager = new EntityManager($connector);

        $this->writeDebugLog('Connection to database is successful');
        return $entityManager;
    }

    private function parsePaths(): void
    {
        $this->corePath      = dirname(__FILE__);
        $this->frameworkPath = $this->corePath.'/../';
        $this->templatesPath = $this->frameworkPath.'../templates';
    }

    private function registerContainer(): void
    {
        $this->container = new Container();
        $this->writeDebugLog('Container initialized');
    }

    private function parseRequest(): void
    {
        $this->request = Request::createFromGlobals();
        $this->writeDebugLog('Request is created');
    }

    private function registerTemplateEngine(): void
    {
        $twigArgs = [];
        if ($this->configurationManager->getConfiguration()->getMode() === Configuration::PROD_MODE) {
            $twigArgs['cache'] = $this->getTemplateEngineCacheDir();
        }
        $loader               = new Twig_Loader_Filesystem($this->templatesPath);
        $twig                 = new Twig_Environment($loader, $twigArgs);
        $this->templateEngine = new TemplateEngine($twig);
        $this->writeDebugLog('Template engine is initialized');
    }

    private function getTemplateEngineCacheDir()
    {
        return $this->frameworkPath.'/../var/cache/twig';
    }

    private function prepareConfiguration(): void
    {
        $configYamlPath             = $this->frameworkPath.'/../config/app.yaml';
        $this->configurationManager = new ConfigurationManager();
        $this->configurationManager->parse($configYamlPath);
    }

    /**
     * @param string $message
     */
    private function writeDebugLog(string $message)
    {
        if ($this->isDevMode()) {
            $this->logger->debug($message);
        }
    }

    /**
     * @return string
     */
    private function getLogPath(): string
    {
        return sprintf('%s/var/log/%s.log',$this->frameworkPath.'/..', $this->configurationManager->getConfiguration()->getMode());
    }

    private function prepareLogger()
    {
        $this->logger = new Logger($this->configurationManager->getConfiguration()->getAppName());
        $logLevel     = $this->getMinimalLogLevel();
        $this->logger->pushHandler(new StreamHandler($this->getLogPath(), $logLevel));
        $this->writeDebugLog('Logger is ready');
    }

    /**
     * @return bool
     */
    private function isDevMode(): bool
    {
        return $this->configurationManager->getConfiguration()->getMode() === Configuration::DEV_MODE;
    }

    /**
     * @return int
     */
    private function getMinimalLogLevel(): int
    {
        if ($this->isDevMode()) {
            $logLevel = Logger::DEBUG;
        } else {
            $logLevel = Logger::ERROR;
        }

        return $logLevel;
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