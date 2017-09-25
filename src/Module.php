<?php

namespace hrzg\opensearch;

/**
 * Class Module
 * @package hrzg\opensearch
 * @author René Lantzsch <r.lantzsch@herzogkommunikation.de>
 */
class Module extends \yii\base\Module
{
    public $apiUrl = 'http://localhost:9090';
    public $apiLogin = 'oss_user';
    public $apiKey = 'oss_key';
    public $defaultIndex = 'default';
    public $resultsPerPage = 10;
}
