<?php

namespace wsydney76\elementmap\controllers;

use Craft;
use craft\base\ElementInterface;
use craft\records\Element;
use craft\web\Controller;
use putyourlightson\campaign\elements\CampaignElement;
use wsydney76\elementmap\ElementmapPlugin;
use yii\web\NotFoundHttpException;

class ElementmapController extends Controller
{

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected array|bool|int $allowAnonymous = [];

    // Protected Properties
    // =========================================================================

    public function actionGetRelations($siteId, $elementId)
    {
        $this->requireLogin();

        $element = Craft::$app->elements->getElementById($elementId, siteId: $siteId);

        if (!$element) {
            throw new NotFoundHttpException("Element not found: {$elementId}");
        }

        $plugin = ElementmapPlugin::getInstance();
        $map = $plugin->renderer->getElementMap($element, $element->siteId);

        if (Craft::$app->request->getParam('json')) {
            return $this->asJson([
                'map' => $map,
            ]);
        }

        return Craft::$app->view->renderTemplate('_elementmap/_elementmap_content', [
            'element' => $element,
            'map' => $map,
            'settings' => $plugin->getSettings(),
        ]);
    }
}
