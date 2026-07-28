---
applyTo: '**/*.php'
---

# PHP Conventions

- Every file starts with `declare(strict_types=1);`.
- PHPStan level max with Silverstan; 100% type coverage for return, param,
  property, constant and declare. No `ignoreErrors`, no baseline.
- **Never add a param type to an override of an untyped framework signature** —
  PHP forbids narrowing an inherited parameter type. Return types may be added.
  This is why `IconDropdownField` overrides `getAttributes()` (no params) rather
  than `Field($properties = [])`.
- Config statics carry `/** @config */` and a `@var` tag for arrays.
- `BuildTask` in SS6: `protected static string $commandName` (not `$segment`),
  `protected string $title`, `protected static string $description`, and
  `protected function execute(InputInterface $input, PolyOutput $output): int`.
  Invoked as `sake tasks:<commandName>`. Never claim the `-f` short flag.
