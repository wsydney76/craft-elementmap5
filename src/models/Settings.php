<?php

namespace wsydney76\elementmap\models;

use Craft;
use craft\base\Model;

/**
 * Element Map 5 settings
 */
class Settings extends Model
{
    public string $showSites = 'all';
    public string $showThumbnails = 'false';
    public string $showRevisions = 'false';
    public string $linkToElement = 'false';
    public string $showRootOwner = 'true';
    public string $orderResults = 'elementType';
    public string $limitPerType = '100';

}
