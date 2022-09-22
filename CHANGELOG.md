
Changelog
=========

Unreleased
----------

[Full Changelog](https://github.com/contao-bootstrap/panel/compare/master...develop)

2.1.2 (2020-01-21)
------------------

[Full Changelog](https://github.com/contao-bootstrap/panel/compare/2.1.1...2.1.2)

### Changed

 - Allow `symfony/templating ^5.0`
 - Avoid potential security issues of `symfony/dependency-injection`

### Fixed

 - Panel single element is not rendered in the backend

2.1.1 (2019-07-22)
------------------

[Full Changelog](https://github.com/contao-bootstrap/panel/compare/2.1.0...2.1.1)

### Fixed

 - Fix broken add image to panel single element
 - Add missing role attributes to accordion group, panel single and panel start element

2.1.0 (2019-01-29)
------------------

[Full Changelog](https://github.com/contao-bootstrap/panel/compare/2.0.4...2.1.0)

### Added

 - Add new class `ContaoBootstrap\Panel\Components\ContentElement\AbstractPanelStartElement`
 - Add new panel single content element

### Fixed

 - Fix travis configuration

2.0.4 (2018-08-31)
------------------

[Full Changelog](https://github.com/contao-bootstrap/panel/compare/2.0.3...2.0.4)

 - Fix missing css id of group element if no custom id is given (@kehr-solutions)
 - Fix expanded checking in panel start template (@kehr-solutions)
 - Add css id and class to panel start element (@kehr-solutions)

2.0.3 (2018-08-24)
------------------

[Full Changelog](https://github.com/contao-bootstrap/panel/compare/2.0.2...2.0.3)

 - Require netzmacht/contao-toolkit and rewrite content elements to achieve Symfony v4 support.
 - Mark classes as final. Infrastructure classes are not meant to be inherited.

2.0.2 (2018-08-07)
------------------

[Full Changelog](https://github.com/contao-bootstrap/panel/compare/2.0.1...2.0.2)

 - Fix missing closing div tag for panel group.
 - Fix invalid CHANGELOG.md (leftover copy of grid)

2.0.1 (2018-07-19)
------------------

 - Fix broken headline tag in ce_bs_panel_start
