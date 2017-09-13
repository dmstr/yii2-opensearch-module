<?php

namespace hrzg\opensearch\widgets;

use yii\base\Widget;

use OpenSearchServer\Handler;
use OpenSearchServer\Search\Field\Search;

/**
 * Class Module
 * @package hrzg\opensearch
 * @author René Lantzsch <r.lantzsch@herzogkommunikation.de>
 */
class Opensearch extends Widget
{
    public $moduleName = 'opensearch';

    public $index;
    public $postvar;

    protected $apiUrl;
    protected $apiKey;
    protected $apiLogin;

    protected $ossApi;

    public function init()
    {
        parent::init();

        $module = \Yii::$app->getModule($this->moduleName);

        //$this->apiUrl = 'oss.dmstr.net'; // <-- causes unnoticeable 301 redirect from http to https with loosing request body due to missing CURL_REDIR_POST_ALL setting
        $this->apiUrl = $module->apiUrl;
        $this->apiKey = $module->apiKey;
        $this->apiLogin = $module->apiLogin;

        $this->index = $module->defaultIndex;

        $this->ossApi = new Handler(array('url' => $this->apiUrl, 'key' => $this->apiKey, 'login' => $this->apiLogin));
    }

    /*
     * In case the oss client lib works with PHP 7.1, it would be used like this
     */
    public function run() {
        // LIST FIELDS
        /*$request = new \OpenSearchServer\Field\GetList();
        $request->index('hrzg');
        $response = $this->ossApi->submit($request);
        foreach($response as $key => $item) {
            echo '<br/>Item #'.$key .': ';
            print_r($item);
        }*/

        $searchword = '';
        $renderSearchbox = true;
        $postData = \Yii::$app->request->post();
        if($this->postvar) {
            $renderSearchbox = false;
            if(isset($postData[$this->postvar])) {
                $searchword = $postData[$this->postvar];
            }
        } else if(isset($postData['searchword'])) {
            $searchword = $postData['searchword'];
        }
        $autocomplete = [];
        $results = [];

        if($searchword) {
            // autocomplete
            $autocompleteRequest = new \OpenSearchServer\Autocompletion\Query();
            $autocompleteRequest->index($this->index)
                ->name('autocomplete')
                ->query($searchword)
                ->rows(10);
            $autocomplete = $this->ossApi->submit($autocompleteRequest);
            // default search for testing
            $request = new Search();
            $request->index($this->index)
                ->query($searchword)
                ->searchFields(array('title', 'content'))
                ->returnedFields(array('title', 'content', 'url'));
            $results = $this->ossApi->submit($request);
        }

        // DYNAMIC MODEL
        /*$model = new \yii\base\DynamicModel(['searchword']);
        if($model->load(\Yii::$app->request->post())){
            var_dump($model->getSearchword());
        }*/

        return \Yii::$app->view->render($this->viewPath.'/opensearch.twig', [
            'autocomplete' => $autocomplete,
            'searchword' => $searchword,
            'renderSearchbox' => $renderSearchbox,
            'results' => $results,
            //'model' => $model
        ]);
    }
}
