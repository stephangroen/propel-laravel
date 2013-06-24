propel-laravel
==============

Make Propel models work with Laravel Form::model() without making it an array.

Implements ArrayAccess so Laravel can check for set properties and adds __isset() and __get() on the model to help
Laravel get the data.

All data with __get() uses the normal Propel getters. Checks are in place for normal column names and PhpNames.

Usage
-----
### build.properties

	propel.behavior.laravelmodel.class = path.to.LaravelModelBehavior

### schema.xml

```xml
<database name="mydatabase" defaultIdMethod="native">
	<behavior name="laravelmodel" />
	....
</database>
```

Integrating Propel in Laravel 4
-----
Here is a tutorial on [how to integrate Propel in Laravel 4](http://picqer.com/blog/propel-with-laravel).
