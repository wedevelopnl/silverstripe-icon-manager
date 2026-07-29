# Silverstripe Icon Manager

Upload and manage custom SVG icons across a SilverStripe site, and select them
anywhere with a previewing dropdown field.

## Requirements

* PHP 8.3+
* SilverStripe 6
* See `composer.json` for the full list

## Installation

```bash
composer require wedevelopnl/silverstripe-icon-manager
```

Then run a `dev/build`. The CMS gains an **Icons** section where icons can be
added and managed.

### Further configuration

* [Configuration](docs/configuration.md)
* [Updating](docs/updating.md)

## License

See [License](LICENSE)

## Maintainers

* [WeDevelop](https://www.wedevelop.nl/) <development@wedevelop.nl>

## Development and contribution

Pull requests are welcome. For major changes, please open an issue first to
discuss what you would like to change. See [CONTRIBUTING.md](CONTRIBUTING.md).

### Getting started

Development runs against a throwaway SilverStripe 6 application in Docker. You
need [Docker](https://docker.com) and [Task](https://taskfile.dev/installation).

```bash
task up          # build and start the testbed, prints the URL
task test        # run every test suite
task qa          # full QA: PHPStan, Rector, coverage, lint, typecheck, build
task --list      # see everything available
```

The testbed's default admin login is `admin` / `admin`. Frontend assets are built
with `npm run build`; `client/dist` is committed and CI verifies it matches source.
