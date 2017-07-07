Request rate limiter implementation for MySQL
---------------------------------------------.

Uses https://github.com/GodsDev/rate-limiter-interface 

Setting up
```php
$conn = \GodsDev\RateLimiter\RateLimiterMysql::createConnectionObj($dbConfig);
$limiter = new \GodsDev\RateLimiter\RateLimiterMysql(
        $rate, //int
        $period, //int
        $userId, //string
        $conn // \PDO connection
);
```

To avoid problems (e.g. count reset after each hit) with differently set time in PHP and MySQL identify it by
```php
//@todo put into PHPUnit test instead!
$stmt = $conn->prepare("SELECT NOW();");
$stmt->execute();
$row = $stmt->fetch();
$timestampPhp = date("Y-m-d H:i:s");
if($row[0] !== $timestampPhp){
    error_log("MySQL time {$row[0]} is different from PHP {$timestampPhp}");
}

//in case of identified troubles fix by some code like this
date_default_timezone_set('Europe/Prague');
/** or directly in php.ini
[Date]
date.timezone = "Europe/Prague"
**/
```

Usage
```php
//For quota notification calculation
$consumedFromThePast = $limiter->getHits($timestamp);

//Increments usage in time and returns number of hits allowed (compared to increment)
$consumedAmount = $limiter->inc($timestamp, $wantToConsumeAmount);

if ($consumedAmount < 1) {
    // you must wait
} else if ($wantToConsumeAmount > $consumedAmount) {
    // it may partly be executed
} else {
    // green go
}

```

Default table name is `rate_limiter` and it may be changed when calling limiter constructor.
Its structure is described in `sql/rate_limiter.sql`.

# Testing
A local file `config.local.php` with a working database connection MUST be created in order to make work `./test-mysql.sh`. Its syntax is:
```php
$config["dbConnection"] = array(
    //if you can, use localhost instead of 127.0.0.1 to speed up access on Linux. see a comment in http://php.net/manual/en/pdo.connections.php
    "dsn" => "mysql:dbname=rate_limiter_test;host=localhost",
    "user" => "root",
    "pass" => "",
);
```
