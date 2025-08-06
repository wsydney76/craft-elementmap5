<?php

namespace wsydney76\elementmap\events;

use craft\base\Event;

class ElementmapAddElementsEvent extends Event
{
    public $element = null;
    public $siteId = null;
    public $direction = null;
    public $data = [];
}