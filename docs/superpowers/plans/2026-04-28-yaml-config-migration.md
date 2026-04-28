# YAML Config Migration Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** XML-Service-Konfiguration durch YAML ersetzen und veraltete Docblock-Annotations durch PHP 8 Attribute ersetzen.

**Architecture:** Separate YAML-Dateien (`services.yaml`, `listener.yaml`) ersetzen die XML-Pendants. Die Extension-Klasse wechselt auf `YamlFileLoader`. Fünf Controller und ein Listener werden von `@ContentElement`/`@Callback`-Docblock-Annotations auf `#[AsContentElement]`/`#[AsCallback]`-Attribute umgestellt. Keine Autowiring-Änderungen – alle Konstruktorargumente bleiben explizit, überflüssige Argumente aus der XML werden dabei bereinigt.

**Tech Stack:** PHP 8.2, Symfony DI (`YamlFileLoader`), Contao 5.3+ (`AsContentElement`, `AsCallback`)

**Spec:** `docs/superpowers/specs/2026-04-28-yaml-config-migration-design.md`

---

## Dateiübersicht

| Aktion | Datei |
|---|---|
| Erstellen | `src/Resources/config/services.yaml` |
| Erstellen | `src/Resources/config/listener.yaml` |
| Ändern | `src/DependencyInjection/ContaoBootstrapAccordionExtension.php` |
| Löschen | `src/Resources/config/services.xml` |
| Löschen | `src/Resources/config/listener.xml` |
| Löschen | `src/Resources/config/config.xml` |
| Ändern | `src/Components/ContentElement/AccordionGroupStartElementController.php` |
| Ändern | `src/Components/ContentElement/AccordionGroupEndElementController.php` |
| Ändern | `src/Components/ContentElement/AccordionStartElementController.php` |
| Ändern | `src/Components/ContentElement/AccordionEndElementController.php` |
| Ändern | `src/Components/ContentElement/AccordionSingleElementController.php` |
| Ändern | `src/EventListener/Dca/ContentDcaListener.php` |

---

## Task 1: `services.yaml` erstellen

**Files:**
- Create: `src/Resources/config/services.yaml`

- [ ] **Schritt 1: Datei erstellen**

Dateiinhalt `src/Resources/config/services.yaml`:

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

    ContaoBootstrap\Accordion\Components\ContentElement\AccordionSingleElementController:
        arguments:
            - '@netzmacht.contao_toolkit.template_renderer'
            - '@netzmacht.contao_toolkit.routing.scope_matcher'
            - '@netzmacht.contao_toolkit.response_tagger'
            - '@contao.security.token_checker'
            - '@contao_bootstrap.core.helper.color_rotate'
            - '@contao.image.studio'

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

> **Hinweis Bereinigungen gegenüber der alten XML:**
> - `AccordionGroupStartElementController`, `AccordionStartElementController`, `AccordionGroupEndElementController`: 6. Argument (`netzmacht.contao_toolkit.repository_manager`) entfernt – nicht im Konstruktor
> - `AccordionEndElementController`: 6. und 7. Argument entfernt (ebenfalls nicht im Konstruktor)
> - Keine expliziten Tags für Migrations – Contao registriert `MigrationInterface` automatisch für `contao.migration` via Autoconfigure. Falls das im Zielprojekt nicht greift, folgende Ergänzung nötig:
>   ```yaml
>   tags:
>       - { name: contao.migration }
>   ```

- [ ] **Schritt 2: Syntax prüfen**

```bash
php -r "yaml_parse(file_get_contents('src/Resources/config/services.yaml'));"
```

Erwartetes Ergebnis: kein Output (kein Fehler).

---

## Task 2: `listener.yaml` erstellen

**Files:**
- Create: `src/Resources/config/listener.yaml`

- [ ] **Schritt 1: Datei erstellen**

Dateiinhalt `src/Resources/config/listener.yaml`:

```yaml
services:
    _defaults:
        autoconfigure: true
        public: false

    ContaoBootstrap\Accordion\EventListener\Dca\ContentDcaListener: ~
```

> `autoconfigure: true` ist hier entscheidend: Sobald der `ContentDcaListener` in Task 6 das `#[AsCallback]`-Attribut bekommt, wird der Callback-Tag automatisch gesetzt.

- [ ] **Schritt 2: Syntax prüfen**

```bash
php -r "yaml_parse(file_get_contents('src/Resources/config/listener.yaml'));"
```

Erwartetes Ergebnis: kein Output.

---

## Task 3: Extension auf `YamlFileLoader` umstellen

**Files:**
- Modify: `src/DependencyInjection/ContaoBootstrapAccordionExtension.php`

- [ ] **Schritt 1: Import ersetzen**

In `src/DependencyInjection/ContaoBootstrapAccordionExtension.php`:

Alt:
```php
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
```

Neu:
```php
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
```

- [ ] **Schritt 2: Loader und Dateinamen ersetzen**

Alt:
```php
$loader = new XmlFileLoader(
    $container,
    new FileLocator(__DIR__ . '/../Resources/config'),
);

$loader->load('listener.xml');
$loader->load('services.xml');
```

Neu:
```php
$loader = new YamlFileLoader(
    $container,
    new FileLocator(__DIR__ . '/../Resources/config'),
);

$loader->load('listener.yaml');
$loader->load('services.yaml');
```

- [ ] **Schritt 3: Syntax prüfen**

```bash
php -l src/DependencyInjection/ContaoBootstrapAccordionExtension.php
```

Erwartetes Ergebnis: `No syntax errors detected`

---

## Task 4: XML-Dateien löschen und committen

**Files:**
- Delete: `src/Resources/config/services.xml`
- Delete: `src/Resources/config/listener.xml`
- Delete: `src/Resources/config/config.xml`

- [ ] **Schritt 1: XML-Dateien löschen**

```bash
git rm src/Resources/config/services.xml src/Resources/config/listener.xml src/Resources/config/config.xml
```

- [ ] **Schritt 2: YAML-Dateien und Extension stagen und committen**

```bash
git add src/Resources/config/services.yaml src/Resources/config/listener.yaml src/DependencyInjection/ContaoBootstrapAccordionExtension.php
git commit -m "Replace XML service config with YAML"
```

---

## Task 5: PHP 8 Attribute auf Controller setzen

**Files:**
- Modify: `src/Components/ContentElement/AccordionGroupStartElementController.php`
- Modify: `src/Components/ContentElement/AccordionGroupEndElementController.php`
- Modify: `src/Components/ContentElement/AccordionStartElementController.php`
- Modify: `src/Components/ContentElement/AccordionEndElementController.php`
- Modify: `src/Components/ContentElement/AccordionSingleElementController.php`

### AccordionGroupStartElementController

- [ ] **Schritt 1: Use-Statement ersetzen**

Alt:
```php
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
```

Neu:
```php
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
```

- [ ] **Schritt 2: Docblock-Annotation durch PHP-Attribut ersetzen**

Alt:
```php
/** @ContentElement("bs_accordion_group_start", category="bs_accordion", template="ce_bs_accordion_group_start") */
final class AccordionGroupStartElementController extends AbstractAccordionElementController
```

Neu:
```php
#[AsContentElement('bs_accordion_group_start', category: 'bs_accordion', template: 'ce_bs_accordion_group_start')]
final class AccordionGroupStartElementController extends AbstractAccordionElementController
```

### AccordionGroupEndElementController

- [ ] **Schritt 3: Use-Statement ersetzen**

Alt:
```php
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
```

Neu:
```php
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
```

- [ ] **Schritt 4: Docblock-Annotation durch PHP-Attribut ersetzen**

Alt:
```php
/** @ContentElement("bs_accordion_group_end", category="bs_accordion", template="ce_bs_accordion_group_end") */
final class AccordionGroupEndElementController extends AbstractAccordionElementController
```

Neu:
```php
#[AsContentElement('bs_accordion_group_end', category: 'bs_accordion', template: 'ce_bs_accordion_group_end')]
final class AccordionGroupEndElementController extends AbstractAccordionElementController
```

### AccordionStartElementController

- [ ] **Schritt 5: Use-Statement ersetzen**

Alt:
```php
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
```

Neu:
```php
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
```

- [ ] **Schritt 6: Docblock-Annotation durch PHP-Attribut ersetzen**

Alt:
```php
/** @ContentElement("bs_accordion_start", category="bs_accordion", template="ce_bs_accordion_start") */
final class AccordionStartElementController extends AbstractAccordionStartElementController
```

Neu:
```php
#[AsContentElement('bs_accordion_start', category: 'bs_accordion', template: 'ce_bs_accordion_start')]
final class AccordionStartElementController extends AbstractAccordionStartElementController
```

### AccordionEndElementController

- [ ] **Schritt 7: Use-Statement ersetzen**

Alt:
```php
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
```

Neu:
```php
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
```

- [ ] **Schritt 8: Docblock-Annotation durch PHP-Attribut ersetzen**

Alt:
```php
/** @ContentElement("bs_accordion_end", category="bs_accordion", template="ce_bs_accordion_end") */
final class AccordionEndElementController extends AbstractAccordionElementController
```

Neu:
```php
#[AsContentElement('bs_accordion_end', category: 'bs_accordion', template: 'ce_bs_accordion_end')]
final class AccordionEndElementController extends AbstractAccordionElementController
```

### AccordionSingleElementController

- [ ] **Schritt 9: Use-Statement ersetzen**

Alt:
```php
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
```

Neu:
```php
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
```

- [ ] **Schritt 10: Docblock-Annotation durch PHP-Attribut ersetzen**

Alt:
```php
/** @ContentElement("bs_accordion_single", category="bs_accordion", template="ce_bs_accordion_single") */
final class AccordionSingleElementController extends AbstractAccordionStartElementController
```

Neu:
```php
#[AsContentElement('bs_accordion_single', category: 'bs_accordion', template: 'ce_bs_accordion_single')]
final class AccordionSingleElementController extends AbstractAccordionStartElementController
```

- [ ] **Schritt 11: Syntax aller geänderten Dateien prüfen**

```bash
php -l src/Components/ContentElement/AccordionGroupStartElementController.php && \
php -l src/Components/ContentElement/AccordionGroupEndElementController.php && \
php -l src/Components/ContentElement/AccordionStartElementController.php && \
php -l src/Components/ContentElement/AccordionEndElementController.php && \
php -l src/Components/ContentElement/AccordionSingleElementController.php
```

Erwartetes Ergebnis: je `No syntax errors detected`

- [ ] **Schritt 12: Committen**

```bash
git add src/Components/ContentElement/AccordionGroupStartElementController.php \
        src/Components/ContentElement/AccordionGroupEndElementController.php \
        src/Components/ContentElement/AccordionStartElementController.php \
        src/Components/ContentElement/AccordionEndElementController.php \
        src/Components/ContentElement/AccordionSingleElementController.php
git commit -m "Replace @ContentElement annotations with #[AsContentElement] attributes"
```

---

## Task 6: PHP 8 Attribut auf Listener setzen

**Files:**
- Modify: `src/EventListener/Dca/ContentDcaListener.php`

- [ ] **Schritt 1: Use-Statement ersetzen**

Alt:
```php
use Contao\CoreBundle\ServiceAnnotation\Callback;
```

Neu:
```php
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
```

- [ ] **Schritt 2: Docblock-Annotation durch PHP-Attribut ersetzen**

Den gesamten Docblock der Methode durch ein Attribut ersetzen. Die Methode sieht danach so aus:

```php
#[AsCallback(table: 'tl_content', target: 'fields.bs_accordion_name.save')]
public function generateAccordionName(string|null $value, DataContainer $dataContainer): string
{
    /** @psalm-suppress RiskyTruthyFalsyComparison */
    if (! $value && $dataContainer->activeRecord) {
        $value = 'accordion_' . $dataContainer->activeRecord->id;
    }

    return (string) $value;
}
```

- [ ] **Schritt 3: Syntax prüfen**

```bash
php -l src/EventListener/Dca/ContentDcaListener.php
```

Erwartetes Ergebnis: `No syntax errors detected`

- [ ] **Schritt 4: Committen**

```bash
git add src/EventListener/Dca/ContentDcaListener.php
git commit -m "Replace @Callback annotation with #[AsCallback] attribute"
```