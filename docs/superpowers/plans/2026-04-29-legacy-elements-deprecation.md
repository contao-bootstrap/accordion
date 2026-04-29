# Legacy Elements Deprecation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Legacy-Content-Elemente (`bs_accordion_start/end`, `bs_accordion_group_start/end`) als deprecated markieren und per Bundle-Konfiguration (`enable_legacy_elements`) deaktivierbar machen.

**Architecture:** Die 4 Legacy-Services werden in eine separate `legacy.yaml` ausgelagert, die die Extension nur lädt wenn `enable_legacy_elements: true` (Default). Die Legacy-Controller erhalten `@deprecated`-PHPDoc sowie einen Konstruktor mit `trigger_error(E_USER_DEPRECATED, ...)`.

**Tech Stack:** PHP 8.2, Symfony DI (YamlFileLoader), Contao 5 Bundle

---

## Dateiübersicht

| Datei | Änderung |
|---|---|
| `src/Resources/config/legacy.yaml` | Neu — 4 Legacy-Service-Definitionen |
| `src/Resources/config/services.yaml` | 4 Legacy-Services entfernen |
| `src/DependencyInjection/Configuration.php` | `enable_legacy_elements: true` hinzufügen |
| `src/DependencyInjection/ContaoBootstrapAccordionExtension.php` | Bedingtes Laden von `legacy.yaml` |
| `src/Components/ContentElement/AccordionStartElementController.php` | `@deprecated` + Konstruktor |
| `src/Components/ContentElement/AccordionEndElementController.php` | `@deprecated` + Konstruktor |
| `src/Components/ContentElement/AccordionGroupStartElementController.php` | `@deprecated` + Konstruktor |
| `src/Components/ContentElement/AccordionGroupEndElementController.php` | `@deprecated` + Konstruktor |
| `README.md` | Abschnitt "Deprecated" hinzufügen |
| `CHANGELOG.md` | Einträge in "Unreleased" |

---

### Task 1: Legacy-Services in separate Datei auslagern

**Files:**
- Create: `src/Resources/config/legacy.yaml`
- Modify: `src/Resources/config/services.yaml`

- [ ] **Schritt 1: `legacy.yaml` erstellen**

Neue Datei `src/Resources/config/legacy.yaml` mit folgendem Inhalt:

```yaml
services:
    _defaults:
        autoconfigure: true
        public: false

    ContaoBootstrap\Accordion\Components\ContentElement\AccordionGroupStartElementController:
        arguments:
            - '@netzmacht.contao_toolkit.template_renderer'
            - '@netzmacht.contao_toolkit.routing.scope_matcher'
            - '@netzmacht.contao_toolkit.response_tagger'
            - '@contao.security.token_checker'
            - '@contao_bootstrap.core.helper.color_rotate'

    ContaoBootstrap\Accordion\Components\ContentElement\AccordionGroupEndElementController:
        arguments:
            - '@netzmacht.contao_toolkit.template_renderer'
            - '@netzmacht.contao_toolkit.routing.scope_matcher'
            - '@netzmacht.contao_toolkit.response_tagger'
            - '@contao.security.token_checker'
            - '@contao_bootstrap.core.helper.color_rotate'

    ContaoBootstrap\Accordion\Components\ContentElement\AccordionStartElementController:
        arguments:
            - '@netzmacht.contao_toolkit.template_renderer'
            - '@netzmacht.contao_toolkit.routing.scope_matcher'
            - '@netzmacht.contao_toolkit.response_tagger'
            - '@contao.security.token_checker'
            - '@contao_bootstrap.core.helper.color_rotate'

    ContaoBootstrap\Accordion\Components\ContentElement\AccordionEndElementController:
        arguments:
            - '@netzmacht.contao_toolkit.template_renderer'
            - '@netzmacht.contao_toolkit.routing.scope_matcher'
            - '@netzmacht.contao_toolkit.response_tagger'
            - '@contao.security.token_checker'
            - '@contao_bootstrap.core.helper.color_rotate'
```

- [ ] **Schritt 2: Die 4 Legacy-Definitionen aus `services.yaml` entfernen**

`src/Resources/config/services.yaml` soll danach so aussehen (nur noch moderne Elemente + Migrations):

```yaml
services:
    _defaults:
        autoconfigure: true
        public: false

    ContaoBootstrap\Accordion\Components\ContentElement\AccordionGroupWrapperElementController:
        arguments:
            - '@contao_bootstrap.core.helper.color_rotate'

    ContaoBootstrap\Accordion\Components\ContentElement\AccordionWrapperElementController:
        arguments:
            - '@contao_bootstrap.core.helper.color_rotate'

    ContaoBootstrap\Accordion\Components\ContentElement\AccordionSingleElementController:
        arguments:
            - '@netzmacht.contao_toolkit.template_renderer'
            - '@netzmacht.contao_toolkit.routing.scope_matcher'
            - '@netzmacht.contao_toolkit.response_tagger'
            - '@contao.security.token_checker'
            - '@contao_bootstrap.core.helper.color_rotate'
            - '@contao.image.studio'

    ContaoBootstrap\Accordion\Migration\PanelMigration:
        arguments:
            - '@database_connection'

    ContaoBootstrap\Accordion\Migration\AccordionGroupWrapperMigration:
        arguments:
            - '@database_connection'
            - '%contao_bootstrap.accordion.enable_wrapper_migration%'

    ContaoBootstrap\Accordion\Migration\AccordionWrapperMigration:
        arguments:
            - '@database_connection'
            - '%contao_bootstrap.accordion.enable_wrapper_migration%'
```

- [ ] **Schritt 3: Commit**

```bash
git add src/Resources/config/legacy.yaml src/Resources/config/services.yaml
git commit -m "Move legacy element services to dedicated legacy.yaml"
```

---

### Task 2: Konfiguration um `enable_legacy_elements` erweitern

**Files:**
- Modify: `src/DependencyInjection/Configuration.php`

- [ ] **Schritt 1: Boolean-Node in `Configuration.php` ergänzen**

Die Methode `getConfigTreeBuilder()` in `src/DependencyInjection/Configuration.php` anpassen:

```php
#[Override]
public function getConfigTreeBuilder(): TreeBuilder
{
    $treeBuilder = new TreeBuilder('contao_bootstrap_accordion');

    $treeBuilder->getRootNode()
        ->children()
            ->booleanNode('enable_wrapper_migration')
                ->defaultFalse()
            ->end()
            ->booleanNode('enable_legacy_elements')
                ->defaultTrue()
            ->end()
        ->end();

    return $treeBuilder;
}
```

- [ ] **Schritt 2: Commit**

```bash
git add src/DependencyInjection/Configuration.php
git commit -m "Add enable_legacy_elements config option (default: true)"
```

---

### Task 3: Extension anpassen — bedingtes Laden von `legacy.yaml`

**Files:**
- Modify: `src/DependencyInjection/ContaoBootstrapAccordionExtension.php`

- [ ] **Schritt 1: Extension um bedingtes Laden erweitern**

Die Methode `load()` in `src/DependencyInjection/ContaoBootstrapAccordionExtension.php` anpassen:

```php
#[Override]
public function load(array $configs, ContainerBuilder $container): void
{
    $configuration = new Configuration();
    $config        = $this->processConfiguration($configuration, $configs);

    $container->setParameter(
        'contao_bootstrap.accordion.enable_wrapper_migration',
        $config['enable_wrapper_migration'],
    );

    $loader = new YamlFileLoader(
        $container,
        new FileLocator(__DIR__ . '/../Resources/config'),
    );

    $loader->load('listener.yaml');
    $loader->load('services.yaml');

    if ($config['enable_legacy_elements']) {
        $loader->load('legacy.yaml');
    }
}
```

- [ ] **Schritt 2: Commit**

```bash
git add src/DependencyInjection/ContaoBootstrapAccordionExtension.php
git commit -m "Conditionally load legacy element services based on enable_legacy_elements config"
```

---

### Task 4: Legacy-Controller als deprecated markieren

**Files:**
- Modify: `src/Components/ContentElement/AccordionStartElementController.php`
- Modify: `src/Components/ContentElement/AccordionEndElementController.php`
- Modify: `src/Components/ContentElement/AccordionGroupStartElementController.php`
- Modify: `src/Components/ContentElement/AccordionGroupEndElementController.php`

Der Konstruktor der Basisklasse `AbstractAccordionElementController` hat folgende Signatur:
```php
public function __construct(
    TemplateRenderer $templateRenderer,         // Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer
    RequestScopeMatcher $scopeMatcher,          // Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher
    ResponseTagger $responseTagger,             // Netzmacht\Contao\Toolkit\Response\ResponseTagger
    TokenChecker $tokenChecker,                 // Contao\CoreBundle\Security\Authentication\Token\TokenChecker
    ColorRotate $colorRotate,                   // ContaoBootstrap\Core\Helper\ColorRotate
)
```

- [ ] **Schritt 1: `AccordionStartElementController` aktualisieren**

Vollständiger neuer Inhalt von `src/Components/ContentElement/AccordionStartElementController.php`:

```php
<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use ContaoBootstrap\Core\Helper\ColorRotate;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;

/**
 * @deprecated Use AccordionWrapperElementController (bs_accordion_wrapper) instead. Will be removed in a future major version.
 */
#[AsContentElement('bs_accordion_start', category: 'bs_accordion', template: 'ce_bs_accordion_start')]
final class AccordionStartElementController extends AbstractAccordionStartElementController
{
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        TokenChecker $tokenChecker,
        ColorRotate $colorRotate,
    ) {
        trigger_error(
            'Content element "bs_accordion_start" is deprecated. Use "bs_accordion_wrapper" instead. Will be removed in a future major version.',
            E_USER_DEPRECATED,
        );

        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger, $tokenChecker, $colorRotate);
    }
}
```

- [ ] **Schritt 2: `AccordionEndElementController` aktualisieren**

Vollständiger neuer Inhalt von `src/Components/ContentElement/AccordionEndElementController.php`:

```php
<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use ContaoBootstrap\Core\Helper\ColorRotate;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Override;
use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated Use AccordionWrapperElementController (bs_accordion_wrapper) instead. Will be removed in a future major version.
 */
#[AsContentElement('bs_accordion_end', category: 'bs_accordion', template: 'ce_bs_accordion_end')]
final class AccordionEndElementController extends AbstractAccordionElementController
{
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        TokenChecker $tokenChecker,
        ColorRotate $colorRotate,
    ) {
        trigger_error(
            'Content element "bs_accordion_end" is deprecated. Use "bs_accordion_wrapper" instead. Will be removed in a future major version.',
            E_USER_DEPRECATED,
        );

        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger, $tokenChecker, $colorRotate);
    }

    #[Override]
    protected function renderContentBackendView(ContentModel $model): Response
    {
        return new Response();
    }
}
```

- [ ] **Schritt 3: `AccordionGroupStartElementController` aktualisieren**

Vollständiger neuer Inhalt von `src/Components/ContentElement/AccordionGroupStartElementController.php`:

```php
<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\Model;
use ContaoBootstrap\Core\Helper\ColorRotate;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Override;
use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated Use AccordionGroupWrapperElementController (bs_accordion_group_wrapper) instead. Will be removed in a future major version.
 */
#[AsContentElement('bs_accordion_group_start', category: 'bs_accordion', template: 'ce_bs_accordion_group_start')]
final class AccordionGroupStartElementController extends AbstractAccordionElementController
{
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        TokenChecker $tokenChecker,
        ColorRotate $colorRotate,
    ) {
        trigger_error(
            'Content element "bs_accordion_group_start" is deprecated. Use "bs_accordion_group_wrapper" instead. Will be removed in a future major version.',
            E_USER_DEPRECATED,
        );

        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger, $tokenChecker, $colorRotate);
    }

    /** {@inheritDoc} */
    #[Override]
    protected function prepareTemplateData(array $data, Request $request, Model $model): array
    {
        $data = parent::prepareTemplateData($data, $request, $model);

        if (empty($data['cssID'])) {
            $data['cssID'] = ' id="accordion-group-' . $model->id . '"';
        }

        return $data;
    }
}
```

- [ ] **Schritt 4: `AccordionGroupEndElementController` aktualisieren**

Vollständiger neuer Inhalt von `src/Components/ContentElement/AccordionGroupEndElementController.php`:

```php
<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use ContaoBootstrap\Core\Helper\ColorRotate;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Override;
use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated Use AccordionGroupWrapperElementController (bs_accordion_group_wrapper) instead. Will be removed in a future major version.
 */
#[AsContentElement('bs_accordion_group_end', category: 'bs_accordion', template: 'ce_bs_accordion_group_end')]
final class AccordionGroupEndElementController extends AbstractAccordionElementController
{
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        TokenChecker $tokenChecker,
        ColorRotate $colorRotate,
    ) {
        trigger_error(
            'Content element "bs_accordion_group_end" is deprecated. Use "bs_accordion_group_wrapper" instead. Will be removed in a future major version.',
            E_USER_DEPRECATED,
        );

        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger, $tokenChecker, $colorRotate);
    }

    #[Override]
    protected function renderContentBackendView(ContentModel $model): Response
    {
        return new Response();
    }
}
```

- [ ] **Schritt 5: Psalm-Prüfung ausführen**

```bash
vendor/bin/phpcq run psalm
```

Erwartetes Ergebnis: Keine neuen Fehler (Deprecation-Annotations sind psalm-kompatibel).

- [ ] **Schritt 6: Commit**

```bash
git add src/Components/ContentElement/AccordionStartElementController.php \
        src/Components/ContentElement/AccordionEndElementController.php \
        src/Components/ContentElement/AccordionGroupStartElementController.php \
        src/Components/ContentElement/AccordionGroupEndElementController.php
git commit -m "Deprecate legacy accordion content elements"
```

---

### Task 5: README aktualisieren

**Files:**
- Modify: `README.md`

- [ ] **Schritt 1: Abschnitt "Deprecated" nach dem "Migration"-Abschnitt einfügen**

Nach dem Block:

```markdown
Afterwards you can run the migration in the Contao Manager or via CLI.
```

Folgenden neuen Abschnitt einfügen:

```markdown

Deprecated
-------

The legacy content elements `bs_accordion_start`, `bs_accordion_end`,
`bs_accordion_group_start` and `bs_accordion_group_end` are deprecated
and will be removed in a future major version. Use `bs_accordion_wrapper`
and `bs_accordion_group_wrapper` instead.

To disable the legacy elements now, set the following configuration:

```yaml
contao_bootstrap_accordion:
    enable_legacy_elements: false
```
```

- [ ] **Schritt 2: Commit**

```bash
git add README.md
git commit -m "Document deprecation of legacy content elements in README"
```

---

### Task 6: CHANGELOG aktualisieren

**Files:**
- Modify: `CHANGELOG.md`

- [ ] **Schritt 1: Einträge im `Unreleased`-Abschnitt ergänzen**

Den bestehenden `Unreleased`-Abschnitt ersetzen durch:

```markdown
Unreleased
----------

### Deprecated

 - Legacy content elements `bs_accordion_start`, `bs_accordion_end`,
   `bs_accordion_group_start` and `bs_accordion_group_end` are deprecated.
   Use `bs_accordion_wrapper` and `bs_accordion_group_wrapper` instead.

### Added

 - Bundle configuration option `enable_legacy_elements` (default: `true`) to
   disable legacy content elements.
```

- [ ] **Schritt 2: Commit**

```bash
git add CHANGELOG.md
git commit -m "Update CHANGELOG with legacy elements deprecation"
```