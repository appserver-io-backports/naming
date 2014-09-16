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
     * Dummy test implementation.
     *
     * @return void
     */
    public function testDummy()
    {
        $this->assertTrue(true);
    }
}
