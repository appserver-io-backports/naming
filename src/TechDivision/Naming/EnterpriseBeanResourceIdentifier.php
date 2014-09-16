<?php

/**
 * TechDivision\Naming\EnterpriseBeanResourceIdentifier
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

use TechDivision\Properties\PropertiesInterface;

/**
 * This is a resource identifier implementation that supports a JNDI like
 * syntax to create a resource identifier for enterprise beans.
 *
 * @category  Library
 * @package   TechDivision_Naming
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_Naming
 */
class EnterpriseBeanResourceIdentifier extends ResourceIdentifier
{

    /**
     * The key for the local business interface.
     *
     * @var string
     */
    const LOCAL_INTERFACE = 'local';

    /**
     * The key for the remote business interface.
     *
     * @var string
     */
    const REMOTE_INTERFACE = 'remote';

    /**
     * Identifier for property name 'contextName'.
     *
     * @var string
     */
    const PROPERTY_CONTEXT_NAME = 'contextName';

    /**
     * Identifier for property name 'className'.
     *
     * @var string
     */
    const PROPERTY_CLASS_NAME = 'className';

    /**
     * Identifier for property name 'interface'.
     *
     * @var string
     */
    const PROPERTY_INTERFACE = 'interface';

    /**
     * Identifier for property name 'indexFile'.
     *
     * @var string
     */
    const PROPERTY_INDEX_FILE = 'indexFile';

    /**
     * The array with the members we want to parse from a URL.
     *
     * @var array
     */
    protected $supportedMembers = array(
        EnterpriseBeanResourceIdentifier::PROPERTY_CONTEXT_NAME,
        EnterpriseBeanResourceIdentifier::PROPERTY_CLASS_NAME,
        EnterpriseBeanResourceIdentifier::PROPERTY_INDEX_FILE,
        EnterpriseBeanResourceIdentifier::PROPERTY_INTERFACE
    );

    /**
     * Returns the array with the supported members.
     *
     * @return array The array with the supported members
     */
    protected function getSupportedMembers()
    {
        return $this->supportedMembers;
    }

    /**
     * Sets the name of the index file.
     *
     * @param string $indexFile The name of the index file
     *
     * @return void
     */
    public function setIndexFile($indexFile)
    {
        $this->setValue(EnterpriseBeanResourceIdentifier::PROPERTY_INDEX_FILE, $indexFile);
    }

    /**
     * Returns the name of the index file.
     *
     * @return string|null The name of the index file
     */
    public function getIndexFile()
    {
        return $this->getValue(EnterpriseBeanResourceIdentifier::PROPERTY_INDEX_FILE);
    }

    /**
     * Sets the context name.
     *
     * @param string $contextName The context name
     *
     * @return void
     */
    public function setContextName($contextName)
    {
        $this->setValue(EnterpriseBeanResourceIdentifier::PROPERTY_CONTEXT_NAME, $contextName);
    }

    /**
     * Returns the context name.
     *
     * @return string|null The context name
     */
    public function getContextName()
    {
        return $this->getValue(EnterpriseBeanResourceIdentifier::PROPERTY_CONTEXT_NAME);
    }

    /**
     * Sets the enterprise beans class name.
     *
     * @param string $className The enterprise bean class name
     *
     * @return void
     */
    public function setClassName($className)
    {
        $this->setValue(EnterpriseBeanResourceIdentifier::PROPERTY_CLASS_NAME, $className);
    }

    /**
     * Returns the enterprise beans class name.
     *
     * @return string|null The enterprise bean class name
     */
    public function getClassName()
    {
        return $this->getValue(EnterpriseBeanResourceIdentifier::PROPERTY_CLASS_NAME);
    }

    /**
     * Sets the name of the interface.
     *
     * @param string $interface The name of the interface
     *
     * @return void
     */
    public function setInterface($interface)
    {
        $this->setValue(EnterpriseBeanResourceIdentifier::PROPERTY_INTERFACE, $interface);
    }

    /**
     * Returns the name of the interface.
     *
     * @return string|null The name of the interface
     */
    public function getInterface()
    {
        return $this->getValue(EnterpriseBeanResourceIdentifier::PROPERTY_INTERFACE);
    }

    /**
     * Queries whether the resource identifier requests a local interface or not.
     *
     * @return boolean TRUE if resource identifier requests a local interface
     */
    public function isLocal()
    {
        return $this->getInterface() === EnterpriseBeanResourceIdentifier::LOCAL_INTERFACE;
    }

    /**
     * Queries whether the resource identifier requests a remote interface or not.
     *
     * @return boolean TRUE if resource identifier requests a remote interface
     */
    public function isRemote()
    {
        return $this->getInterface() === EnterpriseBeanResourceIdentifier::REMOTE_INTERFACE;
    }

    /**
     * create a new resource identifier with the URL parts from the passed properties.
     *
     * @param \TechDivision\Properties\PropertiesInterface $properties The configuration properties
     *
     * @return \TechDivision\Naming\EnterpriseBeanResourceIdentifier The initialized instance
     */
    public static function createFromProperties(PropertiesInterface $properties)
    {
        return new EnterpriseBeanResourceIdentifier($properties->toIndexedArray());
    }
}
