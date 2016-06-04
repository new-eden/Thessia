<?php
namespace Thessia\Lib;

use Monolog\Logger;
use PDO;
use Psr\Http\Message\RequestInterface;

/**
 * Class Db
 * @package Rena\Lib
 */
class Db
{
    /**
     * @var bool
     */
    public $persistence = true;
    /**
     * @var int
     */
    public $timeout = 10;
    /**
     * @var int
     */
    protected $queryCount = 0;
    /**
     * @var int
     */
    protected $queryTime = 0;
    /**
     * @var array
     */
    protected $credentials = array();
    /**
     * @var array
     */
    protected $connections = array();
    /**
     * @var Timer array
     */
    protected $timers = array();
    /**
     * @var PDO
     */
    private $pdo;
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Timer
     */
    private $timer;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * Db constructor.
     * @param Cache $cache
     * @param Logger $logger
     * @param Timer $timer
     * @param Config $config
     * @param RequestInterface $requestInterface
     * @throws \Exception
     */
    function __construct(Cache $cache, Logger $logger, Timer $timer, Config $config, RequestInterface $requestInterface)
    {
        $this->cache = $cache;
        $this->logger = $logger;
        $this->timer = $timer;
        $this->config = $config;
        $this->request = $requestInterface;

        if ($this->persistence === false)
            $this->cache->persistence = false;

        $host = $config->get("unixSocket", "database") ? ";unix_socket=" . $config->get("unixSocket", "database", "/var/run/mysqld/mysqld.sock") : ";host=" . $config->get("host", "database", "127.0.0.1");
        $dsn = "mysql:dbname=" . $config->get("dbName", "database") . "{$host};charset=utf8";

        try {
            $this->pdo = new PDO($dsn, $config->get("username", "database"), $config->get("password", "database"), array(
                PDO::ATTR_PERSISTENT => $this->persistence,
                PDO::ATTR_EMULATE_PREPARES => $config->get('emulatePrepares', 'database'),
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => $config->get('useBufferedQuery', 'database', true),
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+00:00',NAMES utf8;",
            ));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param String $query
     * @param array $parameters
     * @param int $cacheTime
     * @return array
     * @throws \Exception
     */
    public function queryRow(String $query, $parameters = array(), int $cacheTime = 30)
    {
        // Get the result
        $result = $this->query($query, $parameters, $cacheTime);

        // Figure out if it has more than one result and return it
        if (sizeof($result) >= 1)
            return $result[0];

        // No results at all
        return array();
    }

    /**
     * @param String $query
     * @param array $parameters
     * @param int $cacheTime
     * @return array|bool|null
     * @throws \Exception
     */
    public function query(String $query, $parameters = array(), int $cacheTime = 30)
    {
        // Sanity check
        if (strpos($query, ';') !== false) {
            throw new \Exception('Semicolons are not allowed in queries. Use parameters instead.');
        }

        // Cache time of 0 seconds means skip all caches. and just do the query
        $key = $this->getKey($query, $parameters);

        // If cache time is above 0 seconds, lets try and get it from that.
        if ($cacheTime > 0) {
            // Try the cache system
            $result = !empty($this->cache->get($key)) ? unserialize($this->cache->get($key)) : null;
            if (!empty($result)) {
                return $result;
            }
        }

        try {
            // Start the timer
            $timer = new Timer();

            // Increment the queryCounter
            $this->queryCount++;

            // Prepare the query
            $stmt = $this->pdo->prepare($query);

            // Execute the query, with the parameters
            $stmt->execute($parameters);

            // Check for errors
            if ($stmt->errorCode() != 0)
                return false;

            // Fetch an associative array
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Close the cursor
            $stmt->closeCursor();

            // Stop the timer
            $duration = $timer->stop();
            $this->queryTime += $timer->stop();

            // If cache time is above 0 seconds, lets store it in the cache.
            if ($cacheTime > 0)
                $this->cache->set($key, serialize($result), min(3600, $cacheTime));

            // Log the query
            $this->logQuery($query, $parameters, $duration);

            // now to return the result
            return $result;

        } catch (\Exception $e) {
            // There was some sort of nasty nasty nasty error..
            throw $e;
        }
    }

    /**
     * @param String $query
     * @param array $parameters
     * @return string
     */
    public function getKey(String $query, $parameters = array())
    {
        foreach ($parameters as $key => $value) {
            $query .= "|$key|$value";
        }

        return 'Db:' . sha1($query);
    }

    /**
     * @param String $query
     * @param array $parameters
     * @param int $duration
     */
    public function logQuery(String $query, $parameters = array(), int $duration = 0)
    {
        // Don't log queries taking less than 10 seconds.
        if ($duration < 10000) {
            return;
        }

        $baseAddr = '';
        foreach ($parameters as $k => $v) {
            $query = str_replace($k, "'" . $v . "'", $query);
        }

        $uri = isset($_SERVER['REQUEST_URI']) ? "Query page: https://$baseAddr" . $_SERVER['REQUEST_URI'] . "\n" : '';
        $this->logger->info(($duration != 0 ? number_format($duration / 1000, 3) . 's ' : '') . " Query: \n$query;\n$uri");
    }

    /**
     * @param String $query
     * @param String $field
     * @param array $parameters
     * @param int $cacheTime
     * @return null
     * @throws \Exception
     */
    public function queryField(String $query, String $field, $parameters = array(), int $cacheTime = 30)
    {
        // Get the result
        $result = $this->query($query, $parameters, $cacheTime);

        // Figure out if it has no results
        if (sizeof($result) == 0)
            return null;

        // Bind the first result to $resultRow
        $resultRow = $result[0];

        // Return the result + the field requested
        return $resultRow[$field];
    }

    /**
     * @param string $query
     * @param array $parameters
     * @param string $suffix
     * @param bool $returnID
     *
     * @return bool|int|string
     *
     * @throws \Exception
     *
     **
     * Takes an insert query up to the "VALUES" string and an array with the rows to be inserted
     * The rows should be associative arrays much like when doing other db queries
     *
     * Generates a long query to insert all the rows in a single execute
     *
     * query = "INSERT INTO table (columns)"
     * parameters = array(array(":key" => value), array(":key" => value))
     * suffix = optional suffix to the query
     */
    public function multiInsert(String $query, $parameters = array(), String $suffix = "", bool $returnID = false)
    {
        $queryIndexes = array();
        $queryValues = array();

        foreach ($parameters as $rowID => $valueRow) {
            if (!is_array($valueRow)) continue;
            $tmpQuery = array();

            foreach ($valueRow as $fieldID => $fieldValue) {
                $queryValues[$fieldID . $rowID] = $fieldValue;
                $tmpQuery[] = $fieldID . $rowID;
            }

            $queryIndexes[] = '(' . implode(",", $tmpQuery) . ')';
        }

        if (count($queryValues) > 0) {
            return $this->execute($query . ' VALUES ' . implode(",", $queryIndexes) . " " . $suffix, $queryValues, $returnID);
        } else {
            return false;
        }
    }

    /**
     * @param String $query
     * @param array $parameters
     * @param bool $returnID
     * @return bool|int|string
     */
    public function execute(String $query, $parameters = array(), bool $returnID = false)
    {
        // Init the timer
        $timer = new Timer();

        // Increment the amount of queries done
        $this->queryCount++;

        // Transaction start
        $this->pdo->beginTransaction();

        // Prepare the query
        $stmt = $this->pdo->prepare($query);

        // Multilevel array handling.. not pretty but does speedup shit!
        if (isset($parameters[0]) && is_array($parameters[0]) === true) {
            foreach ($parameters as $array) {
                foreach ($array as $key => &$value)
                    $stmt->bindParam($key, $value);
                $stmt->execute();
            }
        } else
            $stmt->execute($parameters);

        // If an error happened, rollback and return false
        if ($stmt->errorCode() != 0) {
            $this->pdo->rollBack();
            return false;
        }

        // Get the inserted id
        $returnID = $returnID ? $this->pdo->lastInsertId() : 0;

        // Commitment time
        $this->pdo->commit();

        // ProjectRena\Lib\Timer stop
        $duration = $timer->stop();

        // Log the query
        $this->logQuery($query, $parameters, $duration);

        // Get the amount of rows that was affected
        $rowCount = $stmt->rowCount();

        // Close the cursor
        $stmt->closeCursor();

        // If return ID is needed, return that
        if ($returnID)
            return $returnID;

        // Return the amount of rows that got altered
        return $rowCount;
    }

    /**
     * @return int
     */
    public function getQueryCount(): int
    {
        return $this->queryCount;
    }

    /**
     * @return float
     */
    public function getQueryTime(): float
    {
        return $this->queryTime;
    }

    /**
     * @param String $name
     * @param String $query
     * @param array $parameters
     * @return bool|\mysqli_result|null
     */
    public function asyncExec(String $name, String $query, $parameters = array())
    {
        // @todo remove this..
        $key = sha1($name . $this->request->getUri()->getPath());

        if (!empty($this->cache->get($key)))
            return null;

        $host = $this->config->get("host", "database", "127.0.0.1");
        $username = $this->config->get('username', 'database');
        $password = $this->config->get('password', 'database');
        $dbName = $this->config->get('name', 'database');
        $port = $this->config->get('port', 'database', 3306);
        $socket = $this->config->get("unixSocket", "database", "/var/run/mysqld/mysqld.sock");

        // Start up the timer
        $this->timers[$name] = new Timer();

        // Start up the mysqli connection
        /** @var \mysqli $connection */
        $connection = mysqli_connect($host, $username, $password, $dbName, $port, $socket);
        $this->connections[$name] = $connection;

        // Increment the query count
        $this->queryCount++;

        // This is ugly, and dangerous
        foreach ($parameters as $key => $value)
            $query = str_replace($key, mysqli_real_escape_string($connection, $value), $query);

        return $connection->query($query, MYSQLI_ASYNC);
    }

    /**
     * @param String $name
     * @param int $cacheTime
     * @return array|bool|null
     */
    public function asyncData(String $name, int $cacheTime = 360)
    {
        // @todo remove this..
        $key = sha1($name . $this->request->getUri()->getPath());

        if ($cacheTime > 0) {
            $result = !empty($this->cache->get($key)) ? unserialize($this->cache->get($key)) : null;
            if (!empty($result))
                return $result;
        }

        if (!isset($this->connections[$name]))
            return false;

        /** @var \mysqli $connection */
        $connection = $this->connections[$name];

        do {
            $links = $errors = $reject = $this->connections;
            mysqli_poll($links, $errors, $reject, $this->timeout);
        } while (!in_array($connection, $links, true) && !in_array($connection, $errors, true) && !in_array($connection, $reject, true));

        // Stop the timer
        $this->queryTime += $this->timers[$name]->stop();

        $data = array();
        $con = $connection->reap_async_query();
        while ($row = $con->fetch_assoc())
            $data[] = $row;
        if ($cacheTime > 0)
            $this->cache->set($key, serialize($data), min(3600, $cacheTime));

        return $data;
    }
}