<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 20.01.18
 */

namespace App\Model;


use Artifly\Core\Component\ORM\AbstractEntity;
use Artifly\Core\Component\ORM\Annotation\Field;

/**
 * Class User
 *
 * @package App\Model
 */
class User extends AbstractEntity
{
    /**
     * @Field(type="string")
     * @var string
     */
    private $name;
    /**
     * @Field(type="string")
     * @var string
     */
    private $lastname;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }
}