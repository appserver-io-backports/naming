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
use Phlexy\Lexer;
use Phlexy\LexerDataGenerator;
use Phlexy\LexerFactory\Stateless\UsingPregReplace;
use TechDivision\Servlet\ServletRequest;
use TechDivision\Properties\Properties;
use TechDivision\Properties\PropertiesInterface;
use TechDivision\PersistenceContainerProtocol\Session;
use TechDivision\PersistenceContainerProtocol\BeanContext;
use TechDivision\Application\Interfaces\ApplicationInterface;
use TechDivision\PersistenceContainerClient\Connection;
use TechDivision\PersistenceContainerClient\LocalConnectionFactory;
use TechDivision\PersistenceContainerClient\RemoteConnectionFactory;

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

    const T_CLASS = 0;
    const T_COLON = 1;
    const T_SCHEME = 2;
    const T_APPLICATION_SCOPE = 3;
    const T_GLOBAL_SCOPE = 4;
    const T_SEPARATOR = 5;
    const T_INTERFACE = 6;

    /**
     * The configuration properties for this context.
     *
     * @var \TechDivision\Properties\PropertiesInterface
     */
    protected $properties;

    /**
     * The lexer used to parse the JNDI style bean names.
     *
     * @param \Phlexy\Lexer $lexer the lexer instance
     */
    protected $lexer;

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
        'scheme'    => 'php',
        'user'      => 'appserver',
        'pass'      => 'appserver.i0',
        'host'      => '127.0.0.1',
        'port'      => '8585',
        'scope'     => 'app',
        'indexFile' => 'index.pc',
        'interface' => EnterpriseBeanResourceIdentifier::LOCAL_INTERFACE
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

        // create a factory for the lexer we use to parse the JNDI style bean names
        $factory = new UsingPregReplace(new LexerDataGenerator);

        // create the lexer instance and inject it
        $this->injectLexer(
            $factory->createLexer(
                array(
                    'php'           => InitialContext::T_SCHEME,
                    'global\/(\w+)' => InitialContext::T_GLOBAL_SCOPE,
                    'app'           => InitialContext::T_APPLICATION_SCOPE,
                    '\:'            => InitialContext::T_COLON,
                    '\/'            => InitialContext::T_SEPARATOR,
                    'local|remote'  => InitialContext::T_INTERFACE,
                    '\w+'           => InitialContext::T_CLASS
                )
            )
        );
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
     * The lexer used to parse the JNDI style bean names.
     *
     * @param \Phlexy\Lexer $lexer the lexer instance
     *
     * @return void
     */
    public function injectLexer(Lexer $lexer)
    {
        $this->lexer = $lexer;
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
     * Returns the lexer used to parse the JNDI style bean names.
     *
     * @return \Phlexy\Lexer The lexer instance
     */
    public function getLexer()
    {
        return $this->lexer;
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
     * php:app/UserProcessor/local
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
        if ($resourceIdentifier->isRemote()) {
            return $this->doRemoteLookup($resourceIdentifier);
        }

        // This MUST be a local lookup to this application, the passed name CAN either be the bean class name
        // only or the complete path including application name and script file name => index.pc for example!
        if ($resourceIdentifier->isLocal()) {
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
    public function prepareResourceIdentifier($resourceName)
    {

        // load the URL properties
        $properties = clone $this->getProperties();

        // lex the passed resource name
        foreach ($this->getLexer()->lex($resourceName) as $token) {

            switch ($token[0]) { // check the found type

                case InitialContext::T_SCHEME: // we found a scheme, e. g. php
                    $properties->setProperty('scheme', $token[2]);
                    break;

                case InitialContext::T_INTERFACE: // we found a interface, e. g. local
                    $properties->setProperty('interface', $token[2]);
                    break;

                case InitialContext::T_CLASS: // we found the class name, e. g. MyProcessor
                    $properties->setProperty('className', $token[2]);
                    break;

                case InitialContext::T_GLOBAL_SCOPE: // we found the scope, e. g. app or global
                    $properties->setProperty('contextName', current($token[3]));
                    break;

                default: // do nothing with the other tokens : and /
                    break;
            }
        }

        // initialize the resource identifier from the passed resource
        return EnterpriseBeanResourceIdentifier::createFromProperties($properties);
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
        $connection->injectAppName($resourceIdentifier->getContextName());

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
        $className = $resourceIdentifier->getClassName();

        // lookup and return the requested remote bean instance
        return $session->createInitialContext()->lookup($className);
    }
}
