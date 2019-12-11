<?php
/**
 * @package     B2Broker\Interfaces
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace B2Broker\Interfaces;


interface SingleModelInQueue
{
    /**
     * Should return class name of job payload
     * @return string
     */
    public function getClassName();

    /**
     * Should return model primary key of job payload
     * @return integer
     */
    public function getModelId();
}
