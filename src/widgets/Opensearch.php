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
    public $resultsPerPage;
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
        $this->resultsPerPage = $module->resultsPerPage;

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

        $query = '';
        $page = 1;
        $renderSearchbox = true;
        $postData = \Yii::$app->request->post();
        $getData = \Yii::$app->request->get();
        if($this->postvar) {
            $renderSearchbox = false;
            if(isset($postData[$this->postvar])) {
                $query = $postData[$this->postvar];
            }
        } else if(isset($postData['query'])) {
            $query = $postData['query'];

        } else if(isset($getData['query'])) {
            $query = $getData['query'];
        }
        $autocomplete = [];
        $results = [];
        $page = (isset($getData['page'])) ? intval($getData['page']) : 1;

        if($query) {
            // autocomplete
            $autocompleteRequest = new \OpenSearchServer\Autocompletion\Query();
            $autocompleteRequest->index($this->index)
                ->name('autocomplete')
                ->query($query)
                ->rows($this->resultsPerPage);
            $autocomplete = $this->ossApi->submit($autocompleteRequest);
            // default search for testing
            $offset = ($page-1)*$this->resultsPerPage;
            $request = new Search();
            $request->index($this->index)
                ->query($query)
                ->start($offset)
                ->searchFields(array('title', 'content'))
                ->returnedFields(array('title', 'content', 'url'));
            $results = $this->ossApi->submit($request);
        }

        // DYNAMIC MODEL
        /*$model = new \yii\base\DynamicModel(['searchword']);
        if($model->load(\Yii::$app->request->post())){
            var_dump($model->getSearchword());
        }*/

        $pages = ($results) ? ceil($results->getTotalNumberFound() / $this->resultsPerPage) : 1;

        return \Yii::$app->view->render('@hrzg/opensearch/views/opensearch.twig', [
            'autocomplete' => $autocomplete,
            'query' => $query,
            'renderSearchbox' => $renderSearchbox,
            'results' => $results,
            'pages' => $pages,
            'page' => $page
            //'model' => $model
        ]);
    }
}
