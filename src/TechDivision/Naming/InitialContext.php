<?php

/**
 * TechDivision\Naming\InitialContext
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

use Rhumsaa\Uuid\Uuid;
use TechDivision\Servlet\ServletRequest;
use TechDivision\Properties\Properties;
use TechDivision\Properties\PropertiesInterface;
use TechDivision\PersistenceContainerProtocol\BeanContext;
use TechDivision\Application\Interfaces\ApplicationInterface;
use TechDivision\PersistenceContainerClient\Connection;
use TechDivision\PersistenceContainerClient\LocalConnectionFactory;
use TechDivision\PersistenceContainerClient\RemoteConnectionFactory;
use TechDivision\PersistenceContainerProtocol\Session;

/**
 * Initial context implementation to lookup enterprise beans.
 *
 * @category  Library
 * @package   TechDivision_Naming
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_Naming
 */
class InitialContext
{

    /**
     * The application instance the context is bound to.
     *
     * @var \TechDivision\Application\Interfaces\ApplicationInterface
     */
    protected $application;

    /**
     * The servlet request instance the context is bound to.
     *
     * @var \TechDivision\Servlet\ServletRequest
     */
    protected $servletRequest;

    /**
     * The default properties for the context configuration.
     *
     * @var array
     */
    protected $defaultProperties = array(
        'scheme' => 'php',
        'user'   => 'appserver',
        'pass'   => 'appserver.i0',
        'host'   => '127.0.0.1',
        'port'   => '8585'
    );

    /**
     * Initialize the initial context with the values of the passed properties.
     *
     * @param \TechDivision\Properties\PropertiesInterface $properties The configuration properties
     */
    public function __construct(PropertiesInterface $properties = null)
    {

        // initialize the default properties if no ones has been passed
        if ($properties == null) {

            // initialize the default properties
            $properties = new Properties();
            foreach ($this->defaultProperties as $key => $value) {
                $properties->setProperty($key, $value);
            }
        }

        // inject the properties
        $this->injectProperties($properties);
    }

    /**
     * The configuration properties for this context.
     *
     * @param \TechDivision\Properties\PropertiesInterface $properties The configuration properties
     *
     * @return void
     */
    public function injectProperties(PropertiesInterface $properties)
    {
        $this->properties = $properties;
    }

    /**
     * The application instance this context is bound to.
     *
     * @param \TechDivision\Application\Interfaces\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function injectApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * The servlet request instance for binding stateful session beans to.
     *
     * @param \TechDivision\Servlet\ServletRequest $servletRequest The servlet request instance
     *
     * @return void
     */
    public function injectServletRequest(ServletRequest $servletRequest)
    {
        $this->servletRequest = $servletRequest;
    }

    /**
     * Returns the initial context configuration properties.
     *
     * @return \TechDivision\Properties\PropertiesInterface The configuration properties
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Returns the application instance this context is bound to.
     *
     * @return \TechDivision\Application\Interfaces\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Returns the servlet request instance for binding stateful session beans to.
     *
     * @return \TechDivision\Servlet\ServletRequest The servlet request instance
     */
    public function getServletRequest()
    {
        return $this->servletRequest;
    }

    /**
     * Returns the requested enterprise bean instance remote/local.
     *
     * Example URL for loading a local instance of the UserProcessor session bean:
     *
     * php://user:password@127.0.0.1:9080/example/index.pc/TechDivision/Example/Services/UserProcessor?SESSID=sadf8dafs879sdfsad
     *
     * parse_url() => Output is:
     * Array
     * (
     *     [scheme] => http
     *     [host]   => 127.0.0.1
     *     [port]   => 9080
     *     [user]   => user
     *     [pass]   => password
     *     [path]   => /example/index.pc/TechDivision/Example/Services/UserProcessor
     *     [query]  => SESSID=sadf8dafs879sdfsad
     * )
     *
     * @param string $name The name of the requested enterprise bean
     *
     * @return object The requested enterprise bean instance
     * @throws \TechDivision\Example\Naming\NamingException Is thrown if we can't lookup the enterprise bean with the passed identifier
     */
    public function lookup($name)
    {

        // at least we need a resource identifier
        if ($name == null) {
            throw new NamingException(sprintf('%s expects a valid resource identifier as parameter', __METHOD__));
        }

        // a valid resource identifier always has to be string
        if (is_string($name) === false) {
            throw new NamingException(sprintf('Invalid value %s for parameter \'name\' has been found, value MUST be a string', $name));
        }

        // prepare the resource identifier for the requested enterprise bean
        $resourceIdentifier = $this->prepareResourceIdentifier($name);

        // This MUST be a remote lookup to another application and the passed name MUST be a complete URL!
        if ($resourceIdentifier->getScheme() === 'http') {
            return $this->doRemoteLookup($resourceIdentifier);
        }

        // This MUST be a local lookup to this application, the passed name CAN either be the bean class name
        // only or the complete path including application name and script file name => index.pc for example!
        if ($resourceIdentifier->getScheme() === 'php') {
            return $this->doLocalLookup($resourceIdentifier);
        }

        // throw an exception if we can't lookup the bean
        throw new NamingException(sprintf('Can\'t lookup enterprise bean with passed identifier %s', $name));
    }

    /**
     * Prepares a new resource identifier instance from the passed resource name, that has to be
     * a valid URL.
     *
     * @param string $resourceName The URL with the resource information
     *
     * @return \TechDivision\Example\Naming\ResourceIdentifier The initialized resource identifier
     * @throws \TechDivision\Example\Naming\NamingException Is thrown if we can't find the necessary application context
     */
    protected function prepareResourceIdentifier($resourceName)
    {

        // load the URL properties
        $properties = $this->getProperties();

        // initialize the resource identifier from the passed resource
        $resourceIdentifier = ResourceIdentifier::createFromProperties($properties);

        // first check if the application name and dummy index file are available
        if (!preg_match('/^http|php:\\/\\/.*/', $resourceName)) {

            // use the application context from the servlet request
            if ($this->getServletRequest() && $this->getServletRequest()->getContext()) {
                $application = $this->getServletRequest()->getContext();
            } else {
                $application = $this->getApplication();
            }

            // we need an application context for a local loolup
            if ($application == null) {
                throw new NamingException(sprintf('Can\'t find application context for local lookup on %s', $resourceName));
            }

            // prepare the resource name with application and dummy index file
            $resourceName = sprintf(
                '%s://%s:%s@%s:%d/%s/index.pc/%s',
                $properties->getProperty('scheme'),
                $properties->getProperty('user'),
                $properties->getProperty('pass'),
                $properties->getProperty('host'),
                $properties->getProperty('port'),
                $application->getName(),
                ltrim(str_replace('\\', '/', $resourceName), '/')
            );
        }

        // if yes, we've a valid path and can initialize the resource identifier
        $resourceIdentifier->populateFromUrl($resourceName);

        // return the initialized resource identifier
        return $resourceIdentifier;
    }

    /**
     * Makes a remote lookup for the URL containing the information of the requested bean.
     *
     * @param \TechDivision\Naming\ResourceIdentifier $resourceIdentifier The resource identifier with the requested bean information
     *
     * @return object The been proxy instance
     */
    protected function doRemoteLookup(ResourceIdentifier $resourceIdentifier)
    {

        // initialize the connection
        $connection = RemoteConnectionFactory::createContextConnection();
        $connection->injectPort($resourceIdentifier->getPort());
        $connection->injectAddress($resourceIdentifier->getHost());
        $connection->injectTransport($resourceIdentifier->getScheme());
        $connection->injectAppName($resourceIdentifier->getApplicationName());

        // finally try to lookup the bean
        return $this->doLookup($resourceIdentifier, $connection);
    }

    /**
     * Makes a local lookup for the bean with the passed class name.
     *
     * @param \TechDivision\Naming\ResourceIdentifier $resourceIdentifier The resource identifier with the requested bean information
     *
     * @return object The bean proxy instance
     */
    protected function doLocalLookup(ResourceIdentifier $resourceIdentifier)
    {

        // use the application context from the servlet request
        if ($this->getServletRequest() && $this->getServletRequest()->getContext()) {
            $application = $this->getServletRequest()->getContext();
        } else {
            $application = $this->getApplication();
        }

        // initialize the connection
        $connection = LocalConnectionFactory::createContextConnection();
        $connection->injectApplication($application);

        // finally try to lookup the bean
        return $this->doLookup($resourceIdentifier, $connection);
    }

    /**
     * Finally this method does the lookup for the passed resource identifier
     * using the also passed connection.
     *
     * @param \TechDivision\Naming\ResourceIdentifier             $resourceIdentifier The identifier for the requested bean
     * @param \TechDivision\PersistenceContainerClient\Connection $connection         The connection we use for loading the bean
     *
     * @return object The been proxy instance
     */
    protected function doLookup(ResourceIdentifier $resourceIdentifier, Connection $connection)
    {

        // initialize the session
        $session = $connection->createContextSession();

        // check if we've a HTTP session-ID
        if ($this->getServletRequest() && $servletSession = $this->getServletRequest()->getSession()) {
            $sessionId = $servletSession->getId(); // if yes, use it for connecting to the stateful session bean
        } else {
            $sessionId = Uuid::uuid4()->__toString(); // simulate a unique session-ID
        }

        // set the HTTP session-ID
        $session->setSessionId($sessionId);

        // load the class name from the resource identifier => that is the path information
        $className = str_replace('/', '\\', $resourceIdentifier->getPathInfo());

        // lookup and return the requested remote bean instance
        return $session->createInitialContext()->lookup($className);
    }
}
