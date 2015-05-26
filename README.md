## JHFrame
JHFrame is a custom web framework for PHP, designed for traditional
server-side-view-creation MVC patterns but written for modern 5.5+ PHP. It could
be classified as "partially batteries-included", having a lighter footprint and
feature-set than enterprise-scale frameworks like Laravel or Rails but much more
than micro-frameworks like Lumen or Sinatra.

Features include:
- Simple dynamic injection of all dependencies
- Traits for granular code inheritance without heavy parent class chains
- Interfaces for all major services
- Unit Testing through PHPUnit
- PSR-3 compatible logging
- Semantic routing
- Templates with metadata
- Cacheing
- Argument-based form creation and validation
- Simple repository-pattern data access/modification
- Chainable SQL query creator
- Optional permission-based authentication for routes
- And more


## Development
JHFrame grew gradually out of a single major project of mine and has gone
through a large number of revisions and rewrites as I have improved as a coder
and attempted to implement better design patterns.

JHFrame is fully usable, fast, and has a reasonably comprehensive feature-set,
with some caveats. Public publishing is primarily intended to serve as a code
example for me, but please feel free to use, experiment, and borrow from it for
your own projects, and contribute if you find it useful.


## Caveats
The project JHFrame arose from is a low-traffic site that is data-driven but
with a low change frequency, so most design decisions were made with that in
mind.  It is built in a server-side page-centric manner not primarily intended
for a heavy client single-page js application + server REST-api style, though it
could be adapted to such.

While I have worked to make it as agnostic as possible, as this grew out of the
needs of a single project and abstracting it into a standalone framework
primarily came as an academic/personal exercise (and of course for the benefits
of decoupling core logic from app-specific logic), some parts may
unintentionally remain insufficiently abstract for a true general-use framework.


## Installation and Use
JHFrame is available through [Composer](https://getcomposer.org/)

Add `"rakelley/jhframe": "^1.0.0"` to the `require` section of your project's
`composer.json`

For usage, please see the documentation and example application


## Testing
```
phpunit -c test/phpunit.xml test/
```


## License
JHFrame is licensed under [the MIT License](http://opensource.org/licenses/MIT)
