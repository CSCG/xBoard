MySQLi DB Examples
====================

SETUP
---------------------

> require_once('Mysqlidb.php');

> $db = new Mysqlidb('host', 'username', 'password', 'databaseName');

INSERT
---------------------

> $insertData = array(
>     'title' => 'Inserted title',
>     'body' => 'Inserted body'
> );

> if ( $db->insert('posts', $insertData) ) echo 'success!';

SELECT
---------------------

> $results = $db->get('tableName', 'numberOfRows-optional');
> print_r($results);  contains array of returned rows

UPDATE
---------------------

> $updateData = array(
>     'fieldOne' => 'fieldValue',
>     'fieldTwo' => 'fieldValue'
> );
> $db->where('id', int);
> $results = $db->update('tableName', $updateData);

DELETE
---------------------

> $db->where('id', int);
> if ( $db->delete('posts') ) echo 'successfully deleted'; 

GENERIC QUERY
---------------------

> $results = $db->query('SELECT * from posts');
> print_r($results);  contains array of returned rows

RAW QUERY
---------------------

> $params = array(3, 'My Title');
> $resutls = $db->rawQuery("SELECT id, title, body FROM posts WHERE id = ? AND tile = ?", $params);
> print_r($results);  contains array of returned rows

will handle any SQL query

> $params = array(10, 1, 10, 11, 2, 10);
> $resutls = $db->rawQuery("
> (SELECT a FROM t1 WHERE a = ? AND B = ? ORDER BY a LIMIT ?)
> UNION
> (SELECT a FROM t2 WHERE a = ? AND B = ? ORDER BY a LIMIT ?)
> ", $params);
> print_r($results);  contains array of returned rows

WHERE
---------------------

> $db->where('id', int);
> $db->where('title', string);
> $results = $db->get('tableName');
> print_r($results);  contains array of returned rows

Optionally you can use method chaining to call where multiple times without referancing your object over an over:
> $results = $db
>     ->where('id', 1)
>     ->where('title', 'MyTitle')
>     ->get('tableName');