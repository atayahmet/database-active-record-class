Database active record class
===================

> **Available fields:**

> - This library completly  is written for composer package
> - This will works with vendor autoload
> - Codeigniter active record class interface used
> - These library use simple and fast of

Let's start!
----

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