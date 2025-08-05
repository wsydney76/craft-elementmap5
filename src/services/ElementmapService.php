<?php

namespace wsydney76\elementmap\services;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\base\Event;
use craft\commerce\elements\Product;
use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\Entry;
use craft\elements\User;
use craft\events\DefineAttributeHtmlEvent;
use craft\events\DefineHtmlEvent;
use craft\events\RegisterElementTableAttributesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use putyourlightson\campaign\elements\CampaignElement;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use wsydney76\elementmap\ElementmapPlugin;
use yii\base\Exception;
use function count;

class ElementmapService
{
    public function initElementmap(): void
    {

        // Set routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['elementmap-getrelations/<siteId:.*>/<elementId:[\d]+>'] = '_elementmap/elementmap/get-relations';
        });

        // Render element maps within the appropriate template hooks.
        Craft::$app->getView()->hook('cp.commerce.product.edit.details', [$this, 'renderProductElementMap']);

        // Dont' show button in slideout editors
        // if (!Craft::$app->request->isConsoleRequest && !Craft::$app->request->isAjax) {
        Event::on(
            Entry::class,
            Entry::EVENT_DEFINE_SIDEBAR_HTML,
            function(DefineHtmlEvent $event) {
                $event->html .= $this->renderSidebarButton($event->sender, 'entry');;
            }
        );
        Event::on(
            Category::class,
            Category::EVENT_DEFINE_SIDEBAR_HTML,
            function(DefineHtmlEvent $event) {
                $event->html .= $this->renderSidebarButton($event->sender, 'category');;
            }
        );
        Event::on(
            Asset::class,
            Asset::EVENT_DEFINE_SIDEBAR_HTML,
            function(DefineHtmlEvent $event) {
                $event->html .= $this->renderSidebarButton($event->sender, 'asset');;
            }
        );
        Event::on(
            User::class,
            User::EVENT_DEFINE_SIDEBAR_HTML,
            function(DefineHtmlEvent $event) {
                $event->html .= $this->renderSidebarButton($event->sender, 'asset');;
            }
        );

        if (Craft::$app->plugins->isPluginEnabled('campaign')) {
            Event::on(
                CampaignElement::class,
                CampaignElement::EVENT_DEFINE_SIDEBAR_HTML,
                function(DefineHtmlEvent $event) {
                    $event->html .= $this->renderSidebarButton($event->sender, 'campaign');;
                }
            );
        }

        if (Craft::$app->plugins->isPluginEnabled('commerce')) {
            Event::on(
                Product::class,
                Product::EVENT_DEFINE_SIDEBAR_HTML,
                function(DefineHtmlEvent $event) {
                    $event->html .= $this->renderSidebarButton($event->sender, 'product');;
                }
            );
        }
        // }

        // Allow some elements to have map data shown in their overview tables.
        Event::on(Asset::class, Element::EVENT_REGISTER_TABLE_ATTRIBUTES, [$this, 'registerElementmapTableAttributes']);
        Event::on(Asset::class, Element::EVENT_DEFINE_ATTRIBUTE_HTML, [$this, 'getElementmapTableAttributeHtml']);
//        Event::on(Category::class, Element::EVENT_REGISTER_TABLE_ATTRIBUTES, [$this, 'registerTableAttributes']);
//        Event::on(Category::class, Element::EVENT_DEFINE_ATTRIBUTE_HTML, [$this, 'getTableAttributeHtml']);
        Event::on(Entry::class, Element::EVENT_REGISTER_TABLE_ATTRIBUTES, [$this, 'registerElementmapTableAttributes']);
        Event::on(Entry::class, Element::EVENT_DEFINE_ATTRIBUTE_HTML, [$this, 'getElementmapTableAttributeHtml']);
//        Event::on(User::class, Element::EVENT_REGISTER_TABLE_ATTRIBUTES, [$this, 'registerTableAttributes']);
//        Event::on(User::class, Element::EVENT_DEFINE_ATTRIBUTE_HTML, [$this, 'getTableAttributeHtml']);
//        Event::on(Product::class, Element::EVENT_REGISTER_TABLE_ATTRIBUTES, [$this, 'registerTableAttributes']);
//        Event::on(Product::class, Element::EVENT_DEFINE_ATTRIBUTE_HTML, [$this, 'getTableAttributeHtml']);
    }


    /**
     * Handler for the Element::EVENT_REGISTER_TABLE_ATTRIBUTES event.
     */
    public function registerElementmapTableAttributes(RegisterElementTableAttributesEvent $event)
    {
        $event->tableAttributes['elementmap_incomingReferenceCount'] = ['label' => Craft::t('_elementmap', 'References From (Count)')];
        $event->tableAttributes['elementmap_outgoingReferenceCount'] = ['label' => Craft::t('_elementmap', 'References To (Count)')];
        $event->tableAttributes['elementmap_incomingReferences'] = ['label' => Craft::t('_elementmap', 'References From')];
        $event->tableAttributes['elementmap_outgoingReferences'] = ['label' => Craft::t('_elementmap', 'References To')];
    }

    /**
     * Handler for the Element::EVENT_DEFINE_ATTRIBUTE_HTML event.
     */
    public function getElementmapTableAttributeHtml(DefineAttributeHtmlEvent $event)
    {
        try {
            $renderer = new ElementmapRenderer();
            /** @var Element $element */
            $element = $event->sender;
            if ($event->attribute === 'elementmap_incomingReferenceCount') {
                $event->handled = true;
                $elements = $renderer->getIncomingElements($element, $element->site->id);
                $event->html = Craft::$app->view->renderTemplate(
                    '_elementmap/_elementmap_indexcolumn', [
                    'elements' => count($elements) + $renderer->elementsNotShown
                ]);
            } else if ($event->attribute === 'elementmap_outgoingReferenceCount') {
                $event->handled = true;
                $elements = $renderer->getOutgoingElements($element, $element->site->id);
                $event->html = Craft::$app->view->renderTemplate(
                    '_elementmap/_elementmap_indexcolumn', [
                    'elements' => count($elements) + $renderer->elementsNotShown
                ]);
            } else if ($event->attribute === 'elementmap_incomingReferences') {
                $event->handled = true;
                $elements = $renderer->getIncomingElements($element, $element->site->id);
                $event->html = Craft::$app->view->renderTemplate(
                    '_elementmap/_elementmap_indexcolumn', [
                    'elements' => $elements,
                    'elementsNotShown' => $renderer->elementsNotShown,
                    'settings' => ElementmapPlugin::getInstance()->getSettings()
                ]);
            } else if ($event->attribute === 'elementmap_outgoingReferences') {
                $event->handled = true;
                $elements = $renderer->getOutgoingElements($element, $element->site->id);
                $event->html = Craft::$app->view->renderTemplate(
                    '_elementmap/_elementmap_indexcolumn', [
                    'elements' => $elements,
                    'elementsNotShown' => $renderer->elementsNotShown,
                    'settings' => ElementmapPlugin::getInstance()->getSettings()
                ]);
            }

        } catch (Exception $e) {
            $event->handled = true;
            $event->html = 'Error: ' . $e->getMessage();
        }
    }

    /**
     * Renders the sidebar button.
     *
     * @param $element
     * @param $class
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function renderSidebarButton($element, $class): string
    {
        return Craft::$app->view->renderTemplate(
            '_elementmap/_elementmap_sidebarbutton', [
            'element' => $element,
            'class' => $class
        ]);
    }

    /**
     * Renders the element map for an entry within the entry editor, given the current Twig context.
     *
     * @param array $context The incoming Twig context.
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function renderAssetElementMap(array &$context)
    {
        return $this->renderSidebarButton($context['element'], 'asset');
    }

    /**
     * Renders the element map for a category within the category editor, given the current Twig context.
     *
     * @param array $context The incoming Twig context.
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
//    public function renderCategoryElementMap(array &$context)
//    {
//        return $this->renderMap($context['category'], 'category');
//    }

    /**
     * Renders the element map for a user within the user editor, given the current Twig context.
     *
     * @param array $context The incoming Twig context.
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
//    public function renderUserElementMap(array &$context)
//    {
//        return $this->renderMap($context['user'], 'user');
//    }

    /**
     * @param array $context
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
//    public function renderGlobalsElementMap(array &$context)
//    {
//        return $this->renderMap($context['globalSet'], 'globalset');
//    }

    /**
     * Renders the element map for a product within the product editor, given the current Twig context.
     *
     * @param array $context The incoming Twig context.
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function renderProductElementMap(array &$context)
    {
        return $this->renderSidebarButton($context['product'], 'product');
    }
}