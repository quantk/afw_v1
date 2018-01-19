<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 18.01.18
 */

namespace App\Controller;


use Artifly\Core\Container;
use Artifly\Core\Controller;
use Artifly\Core\TemplateEngine;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 *
 * @package App\Controller
 */
class DefaultController extends Controller
{
    /**
     * @var Container
     */
    private $container;

    /**
     * DefaultController constructor.
     *
     * @param TemplateEngine $templateEngine
     * @param Container      $container
     */
    public function __construct(
        TemplateEngine $templateEngine,
        Container $container
    )
    {
        parent::__construct($templateEngine);
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @param         $name
     *
     * @return string
     */
    public function indexAction(Request $request, $name)
    {
        $name = ucfirst($name);
        return $this->getTemplateEngine()->render('index.html', [
            'user' => $name
        ]);
    }
}