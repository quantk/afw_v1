<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 18.01.18
 */

namespace App\Controller;


use App\Model\User;
use Artifly\Core\Component\ORM\EntityManager;
use Artifly\Core\Http\Controller;
use Monolog\Logger;

/**
 * Class DefaultController
 *
 * @package App\Controller
 */
class DefaultController extends Controller
{
    /**
     * @param               $userId
     *
     * @return string
     */
    public function indexAction($userId)
    {
        /* @var $em EntityManager */
        $em = $this->get(EntityManager::class);
        /* @var $user User */
        $userName = 'Анонимус';
        $user = $em->find(User::class, $userId);
        if ($user instanceof User) {
            $userName = $user->getName();
        }

        return $this->render('index.html', [
            'user' => $userName
        ]);
    }
}