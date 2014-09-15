<?php

/**
 * TechDivision\Naming\EnterpriseBeanResourceIdentifierTest
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

use TechDivision\Properties\Properties;
/**
 * This is the test for the EnterpriseBeanResourceIdentifier class.
 *
 * @category Library
 * @package TechDivision_Naming
 * @author Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link https://github.com/techdivision/TechDivision_Naming
 */
class EnterpriseBeanResourceIdentifierTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Specifys application, remote interface.
     *
     * @var string
     */
    const IDENTIFIER_GLOBAL_REMOTE = 'php:global/example/UserProcessor/remote';

    /**
     * Specifys application, local interface.
     *
     * @var string
     */
    const IDENTIFIER_GLOBAL_LOCAL = 'php:global/example/UserProcessor/local';

    /**
     * Specifys application, if only one interface has been defined.
     *
     * @var string
     */
    const IDENTIFIER_GLOBAL = 'php:global/example/UserProcessor';

    /**
     * Actual application, remote interface.
     *
     * @var string
     */
    const IDENTIFIER_APP_REMOTE = 'php:app/UserProcessor/remote';

    /**
     * Actual application, local interface.
     *
     * @var string
     */
    const IDENTIFIER_APP_LOCAL = 'php:app/UserProcessor/local';

    /**
     * Actual application, if only one interface has been defined.
     *
     * @var string
     */
    const IDENTIFIER_APP = 'php:app/UserProcessor';

    /**
     * The resource identifier instance we want to test.
     *
     * @var \TechDivision\Naming\EnterpriseBeanResourceIdentifier
     */
    protected $resourceIdentifier;

    /**
     * Initialize the instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->resourceIdentifier = new EnterpriseBeanResourceIdentifier();
    }

    /**
     * Checks if an exception has been thrown when an invalid URL has been passed.
     *
     * @return void
     * @expectedException        \TechDivision\Naming\NamingException
     * @expectedExceptionMessage Can't match URL a_invalid_url to a valid resource identifier
     */
    public function testWithInvalidUrlMatchException()
    {
        $this->resourceIdentifier->populateFromUrl('a_invalid_url');
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with global scope and remote interface.
     *
     * @return void
     */
    public function testWithDefaultPropertiesPopulatedWithGlobalScopeAndRemoteInterface()
    {

        // create default properties
        $defaultProperties = new Properties();
        $defaultProperties->setProperty('indexFile', 'index.pc');
        $defaultProperties->setProperty('contextName', 'example');

        // create the identifier and initialize it from default properties
        $resourceIdentifier = EnterpriseBeanResourceIdentifier::createFromProperties($defaultProperties);

        // populate the identifier with the data of the passed URL
        $resourceIdentifier->populateFromUrl(EnterpriseBeanResourceIdentifierTest::IDENTIFIER_GLOBAL_REMOTE);

        // check the data from the resource identifier
        $this->assertSame($resourceIdentifier->getIndexFile(), 'index.pc');
        $this->assertSame($resourceIdentifier->getInterface(), 'remote');
        $this->assertSame($resourceIdentifier->getClassName(), 'UserProcessor');
        $this->assertSame($resourceIdentifier->getContextName(), 'example');
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with global scope and remote interface.
     *
     * @return void
     */
    public function testPopulateGlobalScopeWithRemoteInterface()
    {

        // populate the identifier with the data of the passed URL
        $this->resourceIdentifier->populateFromUrl(EnterpriseBeanResourceIdentifierTest::IDENTIFIER_GLOBAL_REMOTE);

        // check the data from the resource identifier
        $this->assertSame($this->resourceIdentifier->getIndexFile(), null);
        $this->assertSame($this->resourceIdentifier->getInterface(), 'remote');
        $this->assertSame($this->resourceIdentifier->getClassName(), 'UserProcessor');
        $this->assertSame($this->resourceIdentifier->getContextName(), 'example');
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with global scope and local interface.
     *
     * @return void
     */
    public function testPopulateGlobalScopeWithLocalInterface()
    {

        // populate the identifier with the data of the passed URL
        $this->resourceIdentifier->populateFromUrl(EnterpriseBeanResourceIdentifierTest::IDENTIFIER_GLOBAL_LOCAL);

        // check the data from the resource identifier
        $this->assertSame($this->resourceIdentifier->getIndexFile(), null);
        $this->assertSame($this->resourceIdentifier->getInterface(), 'local');
        $this->assertSame($this->resourceIdentifier->getClassName(), 'UserProcessor');
        $this->assertSame($this->resourceIdentifier->getContextName(), 'example');
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with global scope without interface.
     *
     * @return void
     */
    public function testPopulateGlobalScopeWithoutInterface()
    {

        // populate the identifier with the data of the passed URL
        $this->resourceIdentifier->populateFromUrl(EnterpriseBeanResourceIdentifierTest::IDENTIFIER_GLOBAL);

        // check the data from the resource identifier
        $this->assertSame($this->resourceIdentifier->getIndexFile(), null);
        $this->assertSame($this->resourceIdentifier->getInterface(), null);
        $this->assertSame($this->resourceIdentifier->getClassName(), 'UserProcessor');
        $this->assertSame($this->resourceIdentifier->getContextName(), 'example');
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with app scope and remote interface.
     *
     * @return void
     */
    public function testPopulateAppScopeWithRemoteInterface()
    {

        // populate the identifier with the data of the passed URL
        $this->resourceIdentifier->populateFromUrl(EnterpriseBeanResourceIdentifierTest::IDENTIFIER_APP_REMOTE);

        // check the data from the resource identifier
        $this->assertSame($this->resourceIdentifier->getIndexFile(), null);
        $this->assertSame($this->resourceIdentifier->getInterface(), 'remote');
        $this->assertSame($this->resourceIdentifier->getClassName(), 'UserProcessor');
        $this->assertSame($this->resourceIdentifier->getContextName(), null);
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with app scope and local interface.
     *
     * @return void
     */
    public function testPopulateAppScopeWithLocalInterface()
    {

        // populate the identifier with the data of the passed URL
        $this->resourceIdentifier->populateFromUrl(EnterpriseBeanResourceIdentifierTest::IDENTIFIER_APP_LOCAL);

        // check the data from the resource identifier
        $this->assertSame($this->resourceIdentifier->getIndexFile(), null);
        $this->assertSame($this->resourceIdentifier->getInterface(), 'local');
        $this->assertSame($this->resourceIdentifier->getClassName(), 'UserProcessor');
        $this->assertSame($this->resourceIdentifier->getContextName(), null);
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with app scope without interface.
     *
     * @return void
     */
    public function testPopulateAppScopeWithoutInterface()
    {

        // populate the identifier with the data of the passed URL
        $this->resourceIdentifier->populateFromUrl(EnterpriseBeanResourceIdentifierTest::IDENTIFIER_APP);

        // check the data from the resource identifier
        $this->assertSame($this->resourceIdentifier->getIndexFile(), null);
        $this->assertSame($this->resourceIdentifier->getInterface(), null);
        $this->assertSame($this->resourceIdentifier->getClassName(), 'UserProcessor');
        $this->assertSame($this->resourceIdentifier->getContextName(), null);
    }
}
