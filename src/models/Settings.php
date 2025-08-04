<?php

namespace wsydney76\elementmap\models;

use Craft;
use craft\base\Model;

/**
 * Element Map 5 settings
 */
class Settings extends Model
{
    public bool $showAllSites = true;
    public bool $showRevisions = false;
    public bool $showThumbnails = false;
    public bool $linkToNestedElement = false;
    public bool $showRootOwner = true;
    public bool $sortByElementType = true;

}
