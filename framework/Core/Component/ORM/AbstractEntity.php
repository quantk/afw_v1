<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 20.01.18
 */

namespace Artifly\Core\Component\ORM;

use Artifly\Core\Component\ORM\Annotation\Field;


/**
 * Class ActiveRecord
 * @package Artifly\Core\Component\ORM
 */
class AbstractEntity
{
//region SECTION: Fields
    const STRING_TYPE = 'string';
    const INTEGER_TYPE = 'integer';
    const FOREIGN_KEY_TYPE = 'foreign_key';

    protected $excludedFields = [
        'id',
    ];
    /**
     * @Field(type="integer")
     * @var int
     */
    protected $id = null;
//endregion Fields

//region SECTION: Public
    /**
     * @return bool
     */
    public function isSaved(): bool
    {
        return $this->id !== null;
    }
//endregion Public

//region SECTION: Getters/Setters
    /**
     * @return array
     */
    public function getExcludedFields()
    {
        return $this->excludedFields;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
//endregion Getters/Setters
}