<?php

namespace wsydney76\elementmap\events;

use craft\base\Event;

class ElementmapAddMapDataEvent extends Event
{
    public $type = null;
    public $elementIds = [];
    public $siteId = null;
    public $data = [];
}