# Database Plugin

The **Database** Plugin is for [Grav CMS](http://github.com/getgrav/grav) version 1.6+. This is a simple that allows with simple interactions with **PDO** for database access.  The intention is this plugin should be used in conjunction with other plugins.  For example both **Views** and **Likes-Ratings** plugin utilize this plugin to manage SQLite database interactions.

## Installation

Installing the Database plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

It has a requirement of the Grav **Database** plugin as it stores the views in a simple, file-based sqlite database file.  This will automatically be installed if you use GPM.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install database

## Requirements

Other than standard Grav requirements, this plugin does have some extra requirements.  For speed and scalability Database utilizes a flat-file database to store its tracking information.  This is handled automatically by the plugin, but you do need to ensure you have the following installed on your server:

* **Grav 1.6+** or later 
* **SQLite3** Database
* **PHP pdo** Extension
* **PHP pdo_sqlite** Driver

| PHP by default comes with **PDO** and the vast majority of linux-based systems already come with SQLite.  

### Installation of SQLite on Mac systems

SQLite actually comes pre-installed on your Mac, but you can upgrade it to the latest version with Homebrew:

Install [Homebrew](https://brew.sh/)

```shell
$ /usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
```

Install SQLite with Homebrew

```shell
$ brew install sqlite
```

### Installation of SQLite on Windows systems

Download the appropriate version of SQLite from the [SQLite Downloads Page](https://www.sqlite.org/download.html).  

Extract the downloaded ZIP file and run the `sqlite3.exe` executable.


## Usage

The plugin will intialize a `Database` class in the Grav container, and you can use this to create a new connection or reference an existing connection.  I will use some examples from the `Views` database to illustrate how it can be used:

```php
/** @var PDO */
    protected $db;

    protected $config;
    protected $path = 'user-data://views';
    protected $db_name = 'views.db';
    protected $table_total_views = 'total_views';

    public function __construct($config)
    {
        $this->config = new Config($config);
        $db_path = Grav::instance()['locator']->findResource($this->path, true, true);

        // Create dir if it doesn't exist
        if (!file_exists($db_path)) {
            Folder::create($db_path);
        }

        $connect_string = 'sqlite:' . $db_path . '/' . $this->db_name;

        $this->db = Grav::instance()['database']->connect($connect_string);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (!$this->db->tableExists($this->table_total_views)) {
            $this->createTables();
        }
    }
```

Here you can see a connection string to a `sqlite` database in the `user/data/views` folder will be used.  Then you simply `Grav::instance()['database']->connect($connect_string)` to initialize and connect to the database. If the tables do not exists, we use a local `createTables()` function to create them:

```php
    public function createTables()
    {
        $commands = [
            "CREATE TABLE IF NOT EXISTS {$this->table_total_views} (id VARCHAR(255) PRIMARY KEY, count INTEGER DEFAULT 0)",
        ];

        // execute the sql commands to create new tables
        foreach ($commands as $command) {
            $this->db->exec($command);
        }
    }
```

We just `exec()` the SQL command to create the tables if they don't exist.

To make simple queries you can follow the example of the `get()` method:

```php
    public function get($id)
    {
        $query = "SELECT count FROM {$this->table_total_views} WHERE id = :id";

        $statement = $this->db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_STR);
        $statement->execute();

        $results = $statement->fetch();

        return $results['count'] ?? 0;
    }
```

Just a simple SQL query with a prepared statement used to bind query values, in this case the ID of the row we are looking for.  The field we are looking for is returned from the function.

The main methods that the `Database` class understands are: `select`, `selectall`, `update`, `delete`, `insert`.  However, you can use any PDO command.