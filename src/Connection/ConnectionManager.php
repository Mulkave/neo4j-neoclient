<?php

/**
 * This file is part of the "-[:NEOXYGEN]->" NeoClient package
*
* (c) Neoxygen.io <http://neoxygen.io>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*
*/

namespace Neoxygen\NeoClient\Connection;

use Neoxygen\NeoClient\Connection\Connection,
    Neoxygen\NeoClient\Exception\InvalidConnectionException;

class ConnectionManager
{

    /**
     * @var array Array of all registered connections
     */
    private $connections;

    /**
     * @var string The alias of the default connection
     */
    private $defaultConnection;

    /**
     * @var array Array containing connections fallbacks configs
     */
    private $fallbacks;

    /**
     * Initialize connections array
     */
    public function __construct()
    {
        $this->connections = array();
        $this->fallbacks = array();
    }

    /**
     * @return array An array of the registered connections with the form 'alias' => Connection Object
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     *
     * Register a new Collection
     *
     * @param Connection $connection
     */
    public function registerConnection(Connection $connection)
    {
        $this->connections[$connection->getAlias()] = $connection;
    }

    /**
     * @param  string|null                              $alias The connection's alias
     * @return Neoxygen\NeoClient\Connection\Connection The requested connection
     * @throws InvalidConnectionException               When the connection does not exist
     */
    public function getConnection($alias = null)
    {
        $message = null;

        if (null === $alias && empty($this->connections)) {
            $message = sprintf('There is no connection configured');
        } elseif (null !== $alias && !array_key_exists($alias, $this->connections)) {
            $message = sprintf('The connection with alias "%s" is not configured', $alias);
        }
        if ($message) {
            throw new InvalidConnectionException($message);
        }

        if (null === $alias) {
            return $this->getDefaultConnection();
        }

        return $this->connections[$alias];
    }

    /**
     * @return Neoxygen\NeoClient\Connection\Connection                The default Connection if defined, the first connection in the connections array otherwise
     * @throws Neoxygen\NeoClient\Exception\InvalidConnectionException If no connections are configured
     */
    public function getDefaultConnection()
    {
        if (!$this->defaultConnection && empty($this->connections)) {
            throw new InvalidConnectionException('There are no connections configured');
        }

        if (!$this->defaultConnection) {
            reset($this->connections);

            return current($this->connections);
        }

        return $this->getConnection($this->defaultConnection);
    }

    /**
     * @param string $alias The alias of the connection to be set as default
     */
    public function setDefaultConnection($alias)
    {
        if (!array_key_exists($alias, $this->connections)) {
            throw new InvalidConnectionException(sprintf('The connection "%s" is not configured', $alias));
        }

        $this->defaultConnection = $alias;
    }

    /**
     * Returns whether or not a connection exist for a given alias
     *
     * @param  string $alias The connection's alias to verify the existence
     * @return bool
     */
    public function hasConnection($alias)
    {
        return array_key_exists($alias, $this->connections);
    }

    /**
     * Defines a fallback connection for a given collection
     *
     * @param  string The connection alias to have a fallback
     * @param  string The fallback connection alias
     * @throws Neoxygen\NeoClient\Exception\InvalidConnectionException If one of the connections is not defined
     */
    public function setFallbackConnection($connectionAlias, $fallbackAlias)
    {
        if (!$this->hasConnection($connectionAlias) || !$this->hasConnection($fallbackAlias)) {
            throw new InvalidConnectionException('The fallback connection can not be set, one of the connections does not exist');
        }

        $this->fallbacks[$connectionAlias] = $fallbackAlias;
    }

    /**
     * Returns whether or not a given connection has a fallback connection
     *
     * @param  string $connectionAlias The connection alias
     * @return bool
     */
    public function hasFallbackConnection($connectionAlias)
    {
        return array_key_exists($connectionAlias, $this->fallbacks);
    }

    /**
     * Returns the fallback connection for a given connection alias
     *
     * @param  string The connection alias
     * @return Neoxygen\NeoClient\Connection\Connection
     * @throws Neoxygen\NeoClient\Exception\InvalidConnectionException If the connection has no fallback
     */
    public function getFallbackConnection($connectionAlias)
    {
        if (!$this->hasFallbackConnection($connectionAlias)) {
            throw new InvalidConnectionException(sprintf('The connection "%s" has no defined fallback'));
        }

        return $this->getConnection($this->fallbacks[$connectionAlias]);
    }

}
