<?php

namespace hrzg\opensearch\widgets;

use yii\base\Widget;

/**
 * Class Module
 * @package hrzg\opensearch
 * @author René Lantzsch <r.lantzsch@herzogkommunikation.de>
 */
class OpensearchIframe extends Widget
{
    public $moduleName = 'opensearch';

    public $index;
    public $renderer;

    protected $apiUrl;
    protected $apiKey;
    protected $apiLogin;

    public function init()
    {
        parent::init();

        $module = \Yii::$app->getModule($this->moduleName);

        $this->apiUrl = $module->apiUrl;
        $this->apiKey = $module->apiKey;
        $this->apiLogin = $module->apiLogin;

        $this->index = $module->defaultIndex;

        $this->renderer = 'default';
    }

    public function run()
    {
        $srcUrl = $this->apiUrl.'/renderer?';
        $indexParam = 'use='.$this->index;
        $nameParam = '&name='.$this->renderer;
        $loginParam = '&login='.$this->apiLogin;
        $keyParam = '&key='.$this->apiKey;

        $iframeSrc = $srcUrl.$indexParam.$nameParam.$loginParam.$keyParam;

        return \Yii::$app->view->render('@dmstr/opensearch/views/opensearchIframe.twig', [
            'iframeSrc' => $iframeSrc,
        ]);
    }
}
