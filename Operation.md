Operation manual
=================

# Configuration

You need to set some environment variables in order to configure the idOS API, such as in the following example:

* `IDOS_VERSION`: indicates the version of idOS API (default: '1.0');
* `IDOS_DEBUG`: indicates whether to enable debugging (default: false);
* `IDOS_LOG_FILE`: is the path for the generated log file (default: 'log/api.log');
* `IDOS_GEARMAN_SERVERS`: a list of gearman servers that the idOS API will register on (default: 'localhost:4730');
* `IDOS_SQL_DRIVER`: indicates the SQL database driver to use (default: 'psql');
* `IDOS_SQL_HOST`: the SQL database server host name (default: 'localhost');
* `IDOS_SQL_PORT`: the SQL database server port (default: 5432);
* `IDOS_SQL_NAME`: the SQL database name (default: 'idos-api');
* `IDOS_SQL_USER`: the username used to authenticate within the SQL server (default: 'idos-api');
* `IDOS_SQL_PASS`: the password used to authenticate within the SQL server (default: 'idos-api');
* `IDOS_NOSQL_DRIVER`: indicates the NoSQL database driver to use (default: 'mongodb');
* `IDOS_NOSQL_HOST`: the NoSQL database server host name (default: 'localhost');
* `IDOS_NOSQL_PORT`: the NoSQL database server port (default: 27017);
* `IDOS_OPTIMUS_PRIME`: the prime number for `optimus` library (default: 0);
* `IDOS_OPTIMUS_INVERSE`: the inverse of the prime number for `optimus` library (default: 0);
* `IDOS_OPTIMUS_RANDOM`: a random number for `optimus` library (default: 0);
* `IDOS_SECURE_KEY`: the secure key for `php-encryption` library (default: '');

You may also set these variables using a `.env` file in the project root.

# Running

In order to run the idOS API you should setup a web server supporting PHP 7.1.
