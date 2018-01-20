<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 20.01.18
 */

namespace Artifly\Core\Component\ORM;


/**
 * Class ActiveRecord
 *
 * @package Artifly\Core\Component\ORM
 */
class AbstractEntity
{
//region SECTION: Fields
    protected $excludedFields = [
        'id',
    ];
    /**
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