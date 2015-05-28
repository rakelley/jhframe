#JHFrame Overview


## Routes
Rather than using an arbitrary routing lookup table, routes with JHFrame are
semanticly tied to `RouteController`s.  A route of `/foo/bar` matches to a `bar`
route on the `foo` `RouteController`.  The trade-off in flexibility from
arbitrary routing is offset by making it easier to create new routes without
conflicts and to very easily reason about where URIs are pointed to.  Each
`RouteController` can have an unlimited number of routes with literal or regex
matching and with any HTTP verb, each route being paired with a public method on
the `RouteController`.

A "Name" `RouteController` should be located at `/src/<appname>/routes/Name/`.
The `View`s and `Action`s owned by "Name" `RouteController` should be at
`/src/<appname>/routes/Name/views/` and `/src/<appname>/routes/Name/actions/`
respectively.

The `Router` is responsible for converting a URI into a `RouteController` name
and route string, ensuring the `RouteController` exists, and querying the
`RouteController` for a method that matches the route string, then calling that
method if one is found.

`RouteController`s are meant to be thin.  The `RouteController`s route methods
will typically just pass a view or action to the `ViewController` or
`ActionController` for handling, but through traits can perform actions such as
authenticating access rights and validating a set of inputs before handing over
control.


## Views
`View`s with JHFrame are full classes in any case where a view is dynamic, and
could be classified as "thick but dumb".  `View`s are expected to fetch their
own data, validate their own user input, define any included sub-views, and
generate their own dynamic markup, but rather than magically doing this by
themselves this is handled via interfaces and an Inversion of Control pattern by
the `ViewController` class.  A `View` implements one or more interfaces which
define which public methods need to be called to prepare the generated content
and the `ViewController` calls them and converts the completed view into a
`Renderable` resource.

If a view is completely static, it can be included as a plain html file via the
`FlatView` class.

`FormView`s are a specialized sub-type of `View` which uses pre-defined field
arguments to simplify construction of HTML5 forms with built-in sanitizing.

JHFrame supports `Template`s, which are specialized `View`s covering headers,
footers, and other page-agnostic site-wide components.  `Template`s can accept
metadata from `View`s in order to fill in e.g. page titles and descriptions.


## Actions
`Action`s are generally any route endpoint which involves the altering or
validation of data rather than the creation of markup.  Like `View`s, `Action`s
are handled through an IoC pattern by the `ActionController` which steps through
the `Action`'s methods and produces a `Renderable` resource.

The most common subtype is `FormAction`s, which validate the input from a paired
`FormView` and then do something with that input.


## Data
Data and resources like files are handled in JHFrame with a Repository pattern.
Each `Repository` class covers one or more `Model`s, `FileHandler`s, or other
data-accessing classes with a simple public API which can be accessed by `View`s
and `Action`s.  Non-`Repository` classes should always use the responsible
`Repository` to read/write data and never directly access these data-providing
classes.

For example, a `UserRepository` might provide simple methods for `View`s and
`Action`s to read/write a user's profile via one `Model`, read/write a user's
permissions via another `Model`, and delete their account via another.  The
`Repository` groups together related data-based actions without requiring any
using class to know the underlying structure.

`Model`s are intended to be simple APIs for database tables.  They are solely
responsible for running defined queries against their table with any arguments
provided by their controlling `Repository`.  Any non-query logic should be
handled by the `Repository`, not the `Model`.  A set of traits for `Model`s is
provided which covers many standard query types, and custom queries can either
be manually written or created through the `Database`'s chained query-builder.


## Dependencies
All dependency resolution and class creation is handled by the `ServiceLocator`.
This service has three main components: class resolution, class creation, and
class storage.

### Class Resolution
The `ServiceLocator` keeps a key/value table matching class and interface names
to actual class names.  This table is primed at application initialization
through the `CLASSES` config group, and can be updated at any point through the
`Override` method.  All dependencies on KEY or requests to create a KEY will
result in an object of class VALUE.

### Class Creation
All class dependencies in the constructor will be handled (via reflection) at
the time the class is created.  This injection uses the class resolution table.
If a dependency is not defined in the resolution table, it is assumed to be the
expected class name.

Example

```
class Foo
{
    __construct(Bar $bar, Baz $baz)
    {

    }
}
```

If a `Foo` is created, the `Foo` will have a `Bar` and `Baz` injected
automatically (or a sub-class of `Bar` or `Baz` if defined in the class
resolution table).

In the rare cases in which constructor-based injection is not appropriate, you
can access the `ServiceLocator` by using the `ServiceLocatorAware` trait and
manually `Make` a dependency.

### Class Storage
By default, only a single instance of a class will ever be created.  It is
stored in the `ServiceLocator` and shared between all other classes which depend
on it.  This reduces overhead and encourages creation of stateless classes.

If needed, you can get a new unattached instance by passing the `getNew`
method the existing shared object or a class name.  If you instead need to
ensure that only a single shared instance is ever possible to create, you can
use the provided `ISingleton` interface and `Singleton` trait.


## Default Project Structure and App Initialization

```
/ - Project Root Directory
/cache - Default Shared Disk Cache Directory
/logs - Default Shared Log Directory
/src/app-name - Source Directory for Application "app-name"
/public_html - Default Public Directory
/test/app-name - Test Directory for Application "app-name"
/vendor - Directory for Composer dependencies (including JHFrame)
```
You may have however many applications under `/src` and `/test` you like, each
with its own directory.  This allows you to, for example, run parallel
applications with shared code picked depending on sub-domain.

Your initial file (`index.php` or equivalent) needs to define the
`JHFRAME_ROOTDIR` constant pointing to your project's root directory, include
the Composer autoloader, create a `Bootstrapper`, and call the `Bootstrapper`'s
`Bootstrap` method with the application name and any optional arguments.  The
method will return a fully prepared `App` with all config files loaded and a
ready-to-use `ServiceLocator`.

For HTTP requests, you can then call the `App`'s `serveRequest` method.  For
scripts, you can use the `getLocator` method to access the `ServiceLocator` to
`Make` whatever classes you need.

Note that you can overwrite the defaults for your application's cache, log, and
public directories in its config.


## Application Configuration
Configuration is based on grouped sets of key/value pairs.  JHFrame provides a
set of default configuration groups and values, but you may overwrite these and
specify any arbitary additional groups or keys in your application specific
config.

The default groups are:
- `APP` - application values, such as base url, logging level, whether HTTPS is
required
- `CLASSES` - used to set the `ServiceLocator`'s intial class resolution table
- `ENV` - environment values, such as specific directories, log files, and
environment type

All configuration values are stored in the `Config` class at app initialization,
accessible via constructor injection, the `App`'s `getConfig` method, or the
`ConfigAware` trait.

Your application-specific config files are expected at `/src/<appname>/conf`.
You should create a separate file for each environment (development, production,
etc) you need a different configuration for.  The `Bootstrapper` will load the
file matching the `environment` argument if one was passed or will attempt to
detect the environment based on which files are present (see
`Bootstrapper::deriveEnvironment`).  The configuration file is expected to
return a multi-level array of groups and their values, which will be merged into
the existing config values.

There is an additional special group, `PHP`, which is not stored in `Config` but
is used for any `ini_set`s which need to be performed at initialization.


## Final Notes
JHFrame includes a rich array of services and traits covering everything from
abstracting file system and i/o access to make testing dead simple, to session
creation, view cacheing, and logging.  All classes and methods should be
reasonably well documented, so happy exploring.
