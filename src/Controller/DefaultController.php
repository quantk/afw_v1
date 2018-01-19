<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 18.01.18
 */

namespace App\Controller;


use Artifly\Core\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request, $name)
    {
        $name = ucfirst($name);
        return "<h1>Hello, {$name}. You are in controller.</h1>";
    }
}