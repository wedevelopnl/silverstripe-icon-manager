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
- Config statics carry a `@var` tag for arrays. No `/** @config */` — silverstan
  treats every non-`@internal` `private static` as a config property, so the tag
  is inert. Mark a non-config `private static` with `@internal` to opt it out.
