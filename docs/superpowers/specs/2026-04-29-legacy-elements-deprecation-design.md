# Design Spec: Deprecation der Legacy Content-Elemente

**Datum:** 2026-04-29  
**Status:** Genehmigt

---

## Überblick

Die Legacy-Content-Elemente `bs_accordion_start`, `bs_accordion_end`, `bs_accordion_group_start` und `bs_accordion_group_end` (Start/Stop-Wrapper-Ansatz) werden als deprecated markiert und können per Bundle-Konfiguration deaktiviert werden. Sie werden in einer zukünftigen Major-Version entfernt. Ersatz sind die modernen `bs_accordion_wrapper` / `bs_accordion_group_wrapper`-Elemente (nestedFragments-Ansatz).

---

## Konfiguration

### `Configuration.php`

Neuer Boolean-Node `enable_legacy_elements` mit Default `true` (BC-sicher, Opt-out):

```yaml
contao_bootstrap_accordion:
    enable_legacy_elements: true  # auf false setzen zum Deaktivieren
```

### `ContaoBootstrapAccordionExtension.php`

Der neue Konfigurationswert wird ausgelesen. Die neue `legacy.yaml` wird nur geladen wenn `enable_legacy_elements: true`:

```php
if ($config['enable_legacy_elements']) {
    $loader->load('legacy.yaml');
}
```

---

## Service-Konfiguration

### Neue Datei `src/Resources/config/legacy.yaml`

Die 4 Legacy-Service-Definitionen werden aus `services.yaml` in diese neue Datei verschoben:

- `AccordionStartElementController`
- `AccordionEndElementController`
- `AccordionGroupStartElementController`
- `AccordionGroupEndElementController`

Die verbleibende `services.yaml` enthält danach nur noch die modernen Elemente (`AccordionWrapperElementController`, `AccordionGroupWrapperElementController`, `AccordionSingleElementController`) sowie die Migrations-Services.

---

## Deprecation-Hinweise im PHP-Code

### Betroffene Klassen

Alle 4 Legacy-Controller erhalten:

1. **`@deprecated`-PHPDoc** auf Klassenebene mit Hinweis auf den Ersatz
2. **`trigger_error(E_USER_DEPRECATED, ...)`** im Konstruktor

Da diese Klassen aktuell keinen eigenen Konstruktor haben, wird je ein Konstruktor ergänzt, der die Parent-Argumente durchreicht und die Deprecation auslöst.

Die Parent-Konstruktor-Signatur (`AbstractAccordionElementController`) lautet:
```php
public function __construct(
    TemplateRenderer $templateRenderer,
    RequestScopeMatcher $scopeMatcher,
    ResponseTagger $responseTagger,
    TokenChecker $tokenChecker,
    ColorRotate $colorRotate,
)
```

### Beispiel `AccordionStartElementController`

```php
/**
 * @deprecated Use AccordionWrapperElementController with bs_accordion_wrapper instead.
 *             Will be removed in a future major version.
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
            sprintf(
                'Content element "%s" is deprecated. Use "%s" instead. Will be removed in a future major version.',
                'bs_accordion_start',
                'bs_accordion_wrapper',
            ),
            E_USER_DEPRECATED,
        );

        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger, $tokenChecker, $colorRotate);
    }
}
```

### Deprecation-Meldungen je Klasse

| Klasse | Element | Ersatz |
|---|---|---|
| `AccordionStartElementController` | `bs_accordion_start` | `bs_accordion_wrapper` |
| `AccordionEndElementController` | `bs_accordion_end` | `bs_accordion_wrapper` |
| `AccordionGroupStartElementController` | `bs_accordion_group_start` | `bs_accordion_group_wrapper` |
| `AccordionGroupEndElementController` | `bs_accordion_group_end` | `bs_accordion_group_wrapper` |

---

## README

Neuer Abschnitt "Deprecated" nach dem "Migration"-Abschnitt:

```markdown
## Deprecated

The legacy content elements `bs_accordion_start`, `bs_accordion_end`,
`bs_accordion_group_start` and `bs_accordion_group_end` are deprecated
and will be removed in a future major version. Use `bs_accordion_wrapper`
and `bs_accordion_group_wrapper` instead.

To disable the legacy elements now, set the following configuration:

\```yaml
contao_bootstrap_accordion:
    enable_legacy_elements: false
\```
```

---

## CHANGELOG

Eintrag im `Unreleased`-Abschnitt:

```markdown
### Deprecated

 - Legacy content elements `bs_accordion_start`, `bs_accordion_end`,
   `bs_accordion_group_start` and `bs_accordion_group_end` are deprecated.
   Use `bs_accordion_wrapper` and `bs_accordion_group_wrapper` instead.

### Added

 - Bundle configuration option `enable_legacy_elements` (default: `true`) to
   disable legacy content elements.
```

---

## Nicht verändert

- `$GLOBALS['TL_WRAPPERS']`-Registrierungen in `config.php` bleiben unverändert
- DCA-Konfigurationen bleiben unverändert
- `AccordionSingleElementController` ist kein Legacy-Element und wird nicht deprecated

---

## Dateien

| Datei | Änderung |
|---|---|
| `src/DependencyInjection/Configuration.php` | `enable_legacy_elements: true` hinzufügen |
| `src/DependencyInjection/ContaoBootstrapAccordionExtension.php` | Bedingtes Laden von `legacy.yaml` |
| `src/Resources/config/services.yaml` | 4 Legacy-Services entfernen |
| `src/Resources/config/legacy.yaml` | Neue Datei mit 4 Legacy-Services |
| `src/Components/ContentElement/AccordionStartElementController.php` | `@deprecated` + Konstruktor mit `trigger_error` |
| `src/Components/ContentElement/AccordionEndElementController.php` | `@deprecated` + Konstruktor mit `trigger_error` |
| `src/Components/ContentElement/AccordionGroupStartElementController.php` | `@deprecated` + Konstruktor mit `trigger_error` |
| `src/Components/ContentElement/AccordionGroupEndElementController.php` | `@deprecated` + Konstruktor mit `trigger_error` |
| `README.md` | Neuer Abschnitt "Deprecated" |
| `CHANGELOG.md` | Einträge in "Unreleased" |