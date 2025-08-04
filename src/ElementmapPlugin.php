<?php

namespace wsydney76\elementmap;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use wsydney76\elementmap\models\Settings;
use wsydney76\elementmap\services\ElementmapService;
use wsydney76\elementmap\services\ElementmapRenderer;

/**
 * Element Map 5 plugin
 *
 * @property-read ElementmapService $elementmap
 * @property-read ElementmapRenderer $elementmapRenderer
 * @method static ElementmapPlugin getInstance()
 * @method Settings getSettings()
 */
class ElementmapPlugin extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    public static function config(): array
    {
        return [
            'components' => [
                'elementmap' => ElementmapService::class,
                'renderer' => ElementmapRenderer::class,
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        $this->attachEventHandlers();

        if (Craft::$app->request->isCpRequest) {
            $this->elementmap->initElementmap();
        }

        // Any code that creates an element query or loads Twig should be deferred until
        // after Craft is fully initialized, to avoid conflicts with other plugins/modules
        Craft::$app->onInit(function() {
            // ...
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('_elementmap/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/5.x/extend/events.html to get started)
    }

}
