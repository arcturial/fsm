<?php

/*
 * This file is part of the StateMachine library. A simple
 * finite state machine implementation for PHP.
 */

namespace Arcturial\StateMachine;

/**
 * ContextLocator is base class responsible for providing context
 * required for FSM states and actions.
 * It's children should implement location delegate methods
 * @see ContextLocator::locate()
 * 
 * @category Arcturial
 * @package  Arcturial\StateMachine
 * @author   Andrey Morskoy <cyclope@ya.ru>
 */
class ContextLocator
{
    /**
     *
     * @var array Entity storage for already located items
     */
    protected $store = array();
    
    /**
     *
     * @var \stdClass Additional params for location delegates
     */
    protected $params;
    
    public function __construct($params = null)
    {
        $this->params = ($params) ? $params : new \stdClass();
    }

    /**
     * Locate resource by category.
     * Internal $params may be used for location.
     * Given entity is in store - it immediately retrieved.
     * Otherwise location process deleted to child locator function
     * 
     * @param string $category Resource category to locate
     * @return mixed Resource located or null if fails
     */
    public function locate($category)
    {
        if (empty($category)) {
            return null;
        }

        if ($result = $this->get($category)) {
            return $result;
        }

        $method = 'locate' . $category;
        if (is_callable(array($this, $method))) {
            $result = $this->$method();
            $this->put($result, $category);

            return $result;
        }

        return null;
    }

    /**
     * Put entity into locators cache
     * 
     * @param mixed $object Entity to store in locators cache
     * @param string $category Category to be stored
     */
    public function put($object, $category)
    {
        $this->store[$category] = $object;
    }

    /**
     * Get entity from locators cache
     * 
     * @param string $category
     * @return mixed Stored entity
     */
    public function get($category)
    {
        return isset($this->store[$category]) ? $this->store[$category] : null;
    }
    
    public function setParams($params)
    {
        $this->params = $params;
    }
    
    public function setParam($param, $value)
    {
        $this->params->$param = $value;
    }
}
