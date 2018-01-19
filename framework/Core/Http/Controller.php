<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 18.01.18
 */

namespace Artifly\Core\Http;

use Artifly\Core\Component\Template\TemplateEngine;


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
    private $templateEngine;
//endregion Fields

//region SECTION: Constructor
    /**
     * Controller constructor.
     *
     * @param TemplateEngine $templateEngine
     */
    public function __construct(TemplateEngine $templateEngine)
    {
        //todo: придумать как заинъектить сюда без конструктора
        $this->templateEngine = $templateEngine;
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
//endregion Protected
}