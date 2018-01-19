<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 18.01.18
 * Time: 23:09
 */

namespace App\Controller;


use Artifly\Core\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request, $name)
    {
        return "<h1>Hello controller action with $name</h1>";
    }
}