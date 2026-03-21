# Changelog

## 5.0.0 - 2026-03-21

### Changed

- Requires PHP `8.4`
- Requires `innmind/foundation:~2.1`
- Requires `innmind/framework:~4.0`
- `Innmind\Profiler\Profiler::start()` now returns an `Innmind\Immutable\Attempt<Id>`
- `Innmind\Profiler\Profiler::mutate()` now returns an `Innmind\Immutable\Attempt<Innmind\Immutable\SideEffect>`
- `Innmind\Profiler\Profiler\Mutation\Remote\Http::sent()` now returns an `Innmind\Immutable\Attempt<Innmind\Immutable\SideEffect>`
- `Innmind\Profiler\Profiler\Mutation\Remote\Http::got()` now returns an `Innmind\Immutable\Attempt<Innmind\Immutable\SideEffect>`
- `Innmind\Profiler\Profiler\Mutation\Remote\Processes::record()` now returns an `Innmind\Immutable\Attempt<Innmind\Immutable\SideEffect>`
- `Innmind\Profiler\Profiler\Mutation\Remote\Sql::record()` now returns an `Innmind\Immutable\Attempt<Innmind\Immutable\SideEffect>`
- `Innmind\Profiler\Profiler\Mutation\AppGraph::record()` now returns an `Innmind\Immutable\Attempt<Innmind\Immutable\SideEffect>`
- `Innmind\Profiler\Profiler\Mutation\CallGraph::record()` now returns an `Innmind\Immutable\Attempt<Innmind\Immutable\SideEffect>`
- `Innmind\Profiler\Profiler\Mutation\Environment::record()` now returns an `Innmind\Immutable\Attempt<Innmind\Immutable\SideEffect>`
- `Innmind\Profiler\Profiler\Mutation\Exception::record()` now returns an `Innmind\Immutable\Attempt<Innmind\Immutable\SideEffect>`
- `Innmind\Profiler\Profiler\Mutation\Http::received()` now returns an `Innmind\Immutable\Attempt<Innmind\Immutable\SideEffect>`
- `Innmind\Profiler\Profiler\Mutation\Http::respondedWith()` now returns an `Innmind\Immutable\Attempt<Innmind\Immutable\SideEffect>`
- `Innmind\Profiler\Profiler\Mutation\Processes::record()` now returns an `Innmind\Immutable\Attempt<Innmind\Immutable\SideEffect>`

## 4.1.0 - 2024-03-10

### Added

- Support for `innmind/operating-system:~5.0`

## 4.0.0 - 2023-11-26

### Changed

- Requires `innmind/immutable:~5.2`
- Requires `innmind/operating-system:~4.1`
- Requires `innmind/framework:~2.0`

### Removed

- Support for PHP `8.1`

## 3.1.0 - 2023-09-24

### Added

- Support for `innmind/immutable:~5.0`

## 3.0.3 - 2023-05-01

### Fixed

- Missing text to display the link to download the graphs
- Fixed possible collisions between recorded elements when executed within the same second

## 3.0.2 - 2023-04-30

### Fixed

- Fix interpreting the html in raw content blocks

## 3.0.1 - 2023-04-30

### Fixed

- Fix urls when run inside another app
