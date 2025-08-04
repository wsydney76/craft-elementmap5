<?php

namespace wsydney76\elementmap\events;

use craft\base\Event;

class ElementMapDataEvent extends Event
{
    public $type = null;
    public $elements = [];
    public $siteId = null;
    public $data = [];
}