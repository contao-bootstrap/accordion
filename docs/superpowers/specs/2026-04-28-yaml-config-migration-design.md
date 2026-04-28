# Design: Migration der Bundle-Konfiguration von XML zu YAML

**Datum:** 2026-04-28  
**Branch:** develop

## Ziel

Die bestehenden XML-basierten Service-Konfigurationsdateien durch YAML ersetzen und gleichzeitig veraltete Docblock-Annotations auf PHP 8 Attribute umstellen.

## Rahmenbedingungen

- Kein Autowiring (alle Konstruktor-Argumente bleiben explizit definiert)
- Dateien verbleiben in `src/Resources/config/`
- Separate Dateien für Services und Listener bleiben erhalten
- Überflüssige Argumente in der bestehenden XML werden bereinigt

## Dateien

### Gelöscht

| Datei | Grund |
|---|---|
| `src/Resources/config/services.xml` | Ersetzt durch `services.yaml` |
| `src/Resources/config/listener.xml` | Ersetzt durch `listener.yaml` |
| `src/Resources/config/config.xml` | Leer, wird von der Extension nicht geladen |

### Erstellt

**`src/Resources/config/listener.yaml`**

```yaml
services:
    _defaults:
        autoconfigure: true
        public: false

    ContaoBootstrap\Accordion\EventListener\Dca\ContentDcaListener: ~
```

Kein expliziter Tag – `autoconfigure: true` verarbeitet `#[AsCallback]` automatisch.

**`src/Resources/config/services.yaml`**

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

Keine expliziten Tags:
- Controller: `#[AsContentElement]` + autoconfigure
- Migrations: `MigrationInterface`-Implementierung + autoconfigure

### Geändert

**`src/DependencyInjection/ContaoBootstrapAccordionExtension.php`**

`XmlFileLoader` wird durch `YamlFileLoader` ersetzt:

```php
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

$loader = new YamlFileLoader(
    $container,
    new FileLocator(__DIR__ . '/../Resources/config'),
);

$loader->load('listener.yaml');
$loader->load('services.yaml');
```

## PHP 8 Attribute

### Controller — `@ContentElement` → `#[AsContentElement]`

| Klasse | Vorher | Nachher |
|---|---|---|
| `AccordionGroupStartElementController` | `@ContentElement("bs_accordion_group_start", category="bs_accordion", template="ce_bs_accordion_group_start")` | `#[AsContentElement('bs_accordion_group_start', category: 'bs_accordion', template: 'ce_bs_accordion_group_start')]` |
| `AccordionGroupEndElementController` | `@ContentElement("bs_accordion_group_end", ...)` | `#[AsContentElement('bs_accordion_group_end', category: 'bs_accordion', template: 'ce_bs_accordion_group_end')]` |
| `AccordionStartElementController` | `@ContentElement("bs_accordion_start", ...)` | `#[AsContentElement('bs_accordion_start', category: 'bs_accordion', template: 'ce_bs_accordion_start')]` |
| `AccordionEndElementController` | `@ContentElement("bs_accordion_end", ...)` | `#[AsContentElement('bs_accordion_end', category: 'bs_accordion', template: 'ce_bs_accordion_end')]` |
| `AccordionSingleElementController` | `@ContentElement("bs_accordion_single", ...)` | `#[AsContentElement('bs_accordion_single', category: 'bs_accordion', template: 'ce_bs_accordion_single')]` |

Import alt: `use Contao\CoreBundle\ServiceAnnotation\ContentElement;`  
Import neu: `use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;`

### Listener — `@Callback` → `#[AsCallback]`

`ContentDcaListener::generateAccordionName()`:

```php
// Vorher (Docblock):
// @Callback(table="tl_content", target="fields.bs_accordion_name.save")

// Nachher (Attribut an der Methode):
#[AsCallback(table: 'tl_content', target: 'fields.bs_accordion_name.save')]
public function generateAccordionName(...)
```

Import alt: `use Contao\CoreBundle\ServiceAnnotation\Callback;`  
Import neu: `use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;`

## Bereinigungen in der XML → YAML

Folgende Services hatten in der XML mehr Argumente als der Konstruktor nimmt:

| Service | Entferntes Argument |
|---|---|
| `AccordionGroupStartElementController` | 6. Arg: `netzmacht.contao_toolkit.repository_manager` |
| `AccordionGroupEndElementController` | 6. Arg: `netzmacht.contao_toolkit.repository_manager` |
| `AccordionStartElementController` | 6. Arg: `netzmacht.contao_toolkit.repository_manager` |
| `AccordionEndElementController` | 6. Arg: `netzmacht.contao_toolkit.repository_manager`, 7. Arg: `%kernel.project_dir%` |