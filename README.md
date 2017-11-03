# Slim REST base - A Slim 3 skeleton
This is an app skeleton for the Slim PHP Micro-Framework to get started quickly building a REST API, with CRUD, Sorting, Pagination &filtering

## Features
- [Eloquent ORM](https://github.com/illuminate/database)
- Authentication ([Sentinel](https://github.com/cartalyst/sentinel))
- Validation ([Respect](https://github.com/Respect/Validation) + [Slim Validation](https://github.com/awurth/slim-validation))
- Logs ([Monolog](https://github.com/Seldaek/monolog))
- Console commands for updating the database schema and creating users
- A RESTful router

## Installation
``` bash
$ composer create-project -s dev yoganandgopalan/slim-skeleton [app-name]
```
### Download vendor packages
``` bash
$ composer update
```

## Features
### Create database tables
``` bash
$ php bin/console db
```

### Create users
``` bash
$ php bin/console user:create
```
Use `--admin` option to set the user as admin

### Dump routes
Execute the following command at the project root to print all routes in your terminal
``` bash
$ php bin/console routes
```

Use --markdown or -m option to display routes in markdown format
``` bash
$ php bin/console routes -m > API.md
```

If you're using [Oh My Zsh](https://github.com/robbyrussell/oh-my-zsh), you can install the symfony2 plugin, which provides an alias and autocompletion:
``` bash
# Without Symfony2 plugin
$ php bin/console db

# With Symfony2 plugin
$ sf db
```

## Note
You might want to replace the authentication part with a real OAuth implementation


## Sort, Filter, Limits


#### Custom identification columns

If you pass an array as the second parameter to `parseSingle`, there now have to be column/value pairs.
This allows us to pass multiple conditions like:

```php
SortFilter::parseSingle($books, array('id_origin' => 'Random Bookstore Ltd', 'id' => 1337));
```

### URL parsing

Url parsing currently supports:
* Limit the fields
* Filtering
* Full text search
* Sorting
* Define limit and offset

There are two kind of api resources supported, a single object and a collection of objects.

#### Single object

If you handle a GET request on a resource representing a single object like for example `/api/books/1`, use the `parseSingle` method.

**parseSingle($request, $queryBuilder, $identification, [$queryParams]):**
* **$request**: Request from controller
* **$queryBuilder**: Query builder object, Eloquent model or Eloquent relation
* **$identification**: An integer used in the `id` column or an array column/value pair(s) (`array('isbn' => '1234')`) used as a unique identifier of the object.
* **$queryParams**: An array containing the query parameters. If not defined, the original GET parameters are used.

```php
SortFilter::parseSingle($request, $book, 1);
```

#### Collection of objects

If you handle a GET request on a resource representing multiple objects like for example `/api/books`, use the `parseMultiple` method.

**parseMultiple($request ,$queryBuilder, $fullTextSearchColumns, [$queryParams]):**
* **$request**: Request from controller
* **$queryBuilder**: Query builder object, Eloquent model or Eloquent relation
* **$fullTextSearchColumns**: An array which defines the columns used for full text search.
* **$queryParams**: An array containing the query parameters. If not defined, the original GET parameters are used.

```php
SortFilter::parseMultiple($request, $book, array('title', 'isbn', 'description'));
```

#### Result

Both `parseSingle` and `parseMultiple` return a `Result` object with the following methods available:

**getBuilder():**
Returns the original `$queryBuilder` with all the functions applied to it.

**getResult():**
Returns the result object returned by Laravel's `get()` or `first()` functions.

**getResultOrFail():**
Returns the result object returned by Laravel's `get()` function if you expect multiple objects or `firstOrFail()` if you expect a single object.

**getResponse($resultOrFail = false):**
Returns a Laravel `Response` object including body, headers and HTTP status code.
If `$resultOrFail` is true, the `getResultOrFail()` method will be used internally instead of `getResult()`.

**cleanup($cleanup):**
If true, the resulting array will get cleaned up from unintentionally added relations. Such relations can get automatically added if they are accessed as properties in model accessors. The global default for the cleanup can be defined using the config option `cleanup_relations` which defaults to `false`.
```php
SortFilter::parseSingle($books, 42)->cleanup(true)->getResponse();
```

#### Filtering
Every query parameter, except the predefined functions `_fields`, `_sort`, `_limit`, `_offset` and `_q`, is interpreted as a filter. Be sure to remove additional parameters not meant for filtering before passing them to `parseMultiple`.

```
/api/books?title=The Lord of the Rings
```
All the filters are combined with an `AND` operator.
```
/api/books?title-lk=The Lord*&created_at-min=2014-03-14 12:55:02
```
The above example would result in the following SQL where:
```sql
WHERE `title` LIKE "The Lord%" AND `created_at` >= "2014-03-14 12:55:02"
```
Its also possible to use multiple values for one filter. Multiple values are separated by a pipe `|`.
Multiple values are combined with `OR` except when there is a `-not` suffix, then they are combined with `AND`.
For example all the books with the id 5 or 6:
```
/api/books?id=5|6
```
Or all the books except the ones with id 5 or 6:
```
/api/books?id-not=5|6
```

The same could be achieved using the `-in` suffix:
```
/api/books?id-in=5,6
```
Respectively the `not-in` suffix:
```
/api/books?id-not-in=5,6
```


##### Suffixes
Suffix        | Operator      | Meaning
------------- | ------------- | -------------
-lk           | LIKE          | Same as the SQL `LIKE` operator
-not-lk       | NOT LIKE      | Same as the SQL `NOT LIKE` operator
-in           | IN            | Same as the SQL `IN` operator
-not-in       | NOT IN        | Same as the SQL `NOT IN` operator
-min          | >=            | Greater than or equal to
-max          | <=            | Smaller than or equal to
-st           | <             | Smaller than
-gt           | >             | Greater than
-not          | !=            | Not equal to

#### Sorting
Two ways of sorting, ascending and descending. Every column which should be sorted descending always starts with a `-`.
```
/api/books?_sort=-title,created_at
```

#### Fulltext search
Two implementations of full text search are supported.
You can choose which one to use by changing the `fulltext` option in the config file to either `default` or `native`.

***Note:*** When using an empty `_q` param the search will always return an empty result.

**Limited custom implementation (default)**

A given text is split into keywords which then are searched in the database. Whenever one of the keyword exists, the corresponding row is included in the result set.

```
/api/books?_q=The Lord of the Rings
```
The above example returns every row that contains one of the keywords `The`, `Lord`, `of`, `the`, `Rings` in one of its columns. The columns to consider in full text search are passed to `parseMultiple`.

**Native MySQL implementation**

If your MySQL version supports fulltext search for the engine you use you can use this advanced search in the api handler.  
Just change the `fulltext` config option to `native` and make sure that there is a proper fulltext index on the columns you pass to `parseMultiple`.

Each result will also contain a `_score` column which allows you to sort the results according to how well they match with the search terms. E.g.

```
/api/books?_q=The Lord of the Rings&_sort=-_score
```

You can adjust the name of this column by modifying the `fulltext_score_column` setting in the config file.

#### Limit the result set
To define the maximum amount of datasets in the result, use `_limit`.
```
/api/books?_limit=50
```
To define the offset of the datasets in the result, use `_offset`.
```
/api/books?_offset=20&_limit=50
```
Be aware that in order to use `offset` you always have to specify a `limit` too. MySQL throws an error for offset definition without a limit.
