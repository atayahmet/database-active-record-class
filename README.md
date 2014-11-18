Database active record
===================

> **Available fields:**

> - This library completly  is written for composer package
> - This will works with vendor autoload
> - Codeigniter active record class interface used
> - These library use simple and fast of

Let's start!
----

Database Configuration
---

First let's start with the database settings.

database configuration files in the **Db** folders -> **config.php**


```
$current = 'mysql:connect1';

$db = array(
	'mysql' => array(
		'connect1' => array(
			'hostname' => 'localhost',
			'username' => 'root',
			'password' => '',
			'database' => '',
			'dbprefix' => ''

		)
	)
);
```

The **$current** variable is the driver you want to use as the active and allows you to use the database connection.

**Example:**

Up when I want to define a second database connection settings you need to do the following.

```
	'connect2' => array(
			'hostname' => 'localhost',
			'username' => 'root',
			'password' => '',
			'database' => '',
			'dbprefix' => ''

		)
```

and my **$current** variable have been:

```
$current = 'mysql:connect2'; 
```

We can define the connection as we want it that way.

> **Note:**
> ***mysql*** needs to be defined for the identification of the considered prospective.

We first make the database connection
---


Add our workspace our library
```
use Db\Query as DB;
```

We install the library and also have set a alias. I chose the DB alias.

**A simple database query:**

```
DB::select('*');
DB::get('example_table');
```

We questioned directly above our table without specifying any criteria query.
We can do the same query in the following way:

```
DB::select('*')->get('example_table');
```

**SELECT:**
```
Method 1:
DB::select('*')->get('example_table');

Method 2:
DB::select('examle_type.*')->get('example_type');

Method 3:
DB::select('example_type.id');
DB::select('example_type.name')->get('example_type');
```