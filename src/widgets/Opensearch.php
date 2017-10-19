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

        if (!$this->index) {
            $this->index = $module->defaultIndex;
        }
        $this->resultsPerPage = $module->resultsPerPage;

        $this->ossApi = new Handler(array('url' => $this->apiUrl, 'key' => $this->apiKey, 'login' => $this->apiLogin));
    }

    public function run() {
        $query = '';
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
        $page = (isset($getData['page'])) ? intval($getData['page']) : 1;

        $results = [];
        if($query) {
            $offset = ($page-1)*$this->resultsPerPage;
            $request = new Search();
            $request->index($this->index)
                ->query($query)
                ->start($offset)
                ->searchFields(array('title', 'content'))
                ->returnedFields(array('title', 'content', 'url'));
            $results = $this->ossApi->submit($request);
        }

        $pages = ($results) ? ceil($results->getTotalNumberFound() / $this->resultsPerPage) : 1;

        return \Yii::$app->view->render('@hrzg/opensearch/views/opensearch.twig', [
            'query' => $query,
            'renderSearchbox' => $renderSearchbox,
            'results' => $results,
            'pages' => $pages,
            'page' => $page
        ]);
    }
}
