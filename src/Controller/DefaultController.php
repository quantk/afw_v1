<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 18.01.18
 */

namespace App\Controller;


use App\Model\User;
use Artifly\Core\Component\Container\Container;
use Artifly\Core\Component\ORM\EntityManager;
use Artifly\Core\Component\Template\TemplateEngine;
use Artifly\Core\Http\Controller;
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
     * @param Request       $request
     *
     * @param EntityManager $em
     *
     * @param               $userId
     *
     * @return string
     */
    public function indexAction(Request $request, EntityManager $em, $userId)
    {
        /* @var $user User */
        $userName = 'Анонимус';
        $user = $em->find(User::class, $userId);
        if ($user instanceof User) {
            $userName = $user->getName();
        }

        return $this->getTemplateEngine()->render('index.html', [
            'user' => $userName,
            'method' => $request->getMethod()
        ]);
    }
}