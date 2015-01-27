# PTX Task Manager

## Problem to be solved

You should create web application. This application should be simple task manager. Consisting of:

* Create Task
* Edit Task
* Report Time
* Close Task
* List Tasks

Your solution have to be JS based client. That communicates with PHP based backend. Over JSON based API (REST or 
REST like). This backend should write and read data from/to at least two persistent data storing layers based on configuration. Those layers have to have same access interface. One of those persistent layers have to be SQL database. Second persistent layer you are free to chose (not SQL database). Using OOP on LAMP architecture. No need for data validation only sanitize to avoid security risks.

### Result

You should not spent more than 8 hours on this test. Send us code in git repository. Including Readme file with deploy instructions and any data files necessary for deployment.

### Optional (plus)

These are nice to have and will yield bonus points but its perfectly ok not to meet them:

* With repository send us link where the task manager is deployed.
* Use Hack lang (in strict mode as much as possible) instead of PHP
* Use Coffeescript instead of JS (but compiled into native JS)
* Create code documentation
* API Unit Tests

## Solution

Simple tasks manager using jQuery and PHP to operate. In can be set to use MySQL database or to work over JSON files. 

### Select what storage to use
To set up what storage should be use, you have to set up constant `STORAGE_TYPE` properly

```
// /Config/boostrap.php
define('STORAGE_TYPE', 'json'); // system will use JSON storage
define('STORAGE_TYPE', 'mysql'); // system will use MySQL storage
```

### SetUp for MySQL

If you want to use MySQL as storage, you have to prepare database, create proper tables and set up the application. 

#### 1. SQL Dump
The application needs only 1 table to operate and its dump can be found `/Config/Sql/tasks.sql`.

#### 2. Constant for connection

Apart this, you have to set up constant for MySQL in `/Config/bootstrap`

```
define('DB_DRIVER', 'mysql'); 
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tomas_tasks');
define('DB_CHARSET', 'utf8');
```

#### 3. DibiPHP installation
System uses dibiphp as layer to connect and operate with MySQL database. The easiest way to do it is via `composer` The `composer.json` file is in the main directory, so we need to just run following command

``` 
composer install
```

## Tests
Tests are written for PHPUnit and are stored under `/test` folder. If you want to test application with MySQL database, you have to create copy of `tasks` table and name it as `tasks_test`.

## Project Online
The project can be viewed under: http://tasks.ptx.cz/index.php

Tomas Pavlatka


