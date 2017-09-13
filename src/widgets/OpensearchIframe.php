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
    public $index;
    public $renderer;

    protected $apiUrl;
    protected $apiKey;
    protected $apiLogin;

    protected $ossApi;

    public function init()
    {
        parent::init();
        $this->apiUrl = 'http://localhost:9090';
        $this->apiKey = 'oss_key';
        $this->apiLogin = 'oss_user';
        $this->index = 'hrzg';
        $this->renderer = 'default';
    }

    public function run()
    {
        $srcUrl = 'https://'.$this->apiUrl.'/renderer?';
        $indexParam = 'use='.$this->index;
        $nameParam = '&name='.$this->renderer;
        $loginParam = '&login='.$this->apiLogin;
        $keyParam = '&key='.$this->apiKey;

        $iframeSrc = $srcUrl.$indexParam.$nameParam.$loginParam.$keyParam;

        return \Yii::$app->view->render('@app/views/widgets/opensearchIframe.twig', [
            'iframeSrc' => $iframeSrc,
        ]);
    }
}
