<?php

/**
 * TechDivision\Naming\NamingDirectory
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Library
 * @package   TechDivision_Naming
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_Naming
 */

namespace TechDivision\Naming;

use TechDivision\Storage\StackableStorage;

/**
 * Naming directory implementation.
 *
 * @category  Library
 * @package   TechDivision_Naming
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_Naming
 */
class NamingDirectory extends StackableStorage implements NamingDirectoryInterface
{

    /**
     * Binds the passed instance with the name to the naming directory.
     *
     * @param string $name  The name to bind the value with
     * @param mixed  $value The object instance to bind
     * @param array  $args  The array with the arguments
     *
     * @return void
     */
    public function bind($name, $value, array $args = array())
    {
        $this->set($name, array($value, $args));
    }

    /**
     * Binds the passed callback with the name to the naming directory.
     *
     * @param string   $name     The name to bind the callback with
     * @param callable $callback The callback to be invoked when searching for
     * @param array    $args     The array with the arguments passed to the callback when executed
     *
     * @return void
     * @see \TechDivision\Naming\NamingDirectory::bind()
     */
    public function bindCallback($name, callable $callback, array $args = array())
    {
        $this->bind($name, $callback, $args);
    }

    /**
     * Queries the naming directory for the requested name and returns the value
     * or invokes the binded callback.
     *
     * @param string $name The name of the requested value
     * @param array  $args The arguments to pass to the callback
     *
     * @return mixed The requested value
     */
    public function search($name, array $args = array())
    {

        // load the binded value/args
        list ($value, $bindArgs) = parent::get($name);

        // check if we've a callback method
        if (is_callable($value)) { // if yes, merge the params and invoke the callback
            return call_user_func_array($value, array_merge($bindArgs, $args));
        }

        // if not, simply return the value/object
        return $value;
    }

    /**
     * Finds and returns data from the naming directory.
     *
     * @param string $entryIdentifier Something which identifies the cache entry - depends on concrete cache
     *
     * @return mixed
     * @see \TechDivision\Storage\StorageInterface::get()
     */
    public function get($entryIdentifier)
    {
        list ($value, ) = parent::get($entryIdentifier);
        return $value;
    }
}
