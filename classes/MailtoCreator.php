<?php namespace Waka\Mailtoer\Classes;

use Waka\Mailtoer\Models\WakaMailto;

class MailtoCreator
{

    private $dataSourceModel;
    private $dataSourceId;
    private $additionalParams;
    private $dataSourceAdditionalParams;

    public function __construct($mailto_id)
    {
        //trace_log($mailto_id);
        $wakamailto = WakaMailto::find($mailto_id);
        $this->wakamailto = $wakamailto;

    }

    public function prepareCreatorVars($dataSourceId)
    {
        $this->dataSourceModel = $this->linkModelSource($dataSourceId);
        $this->dataSourceAdditionalParams = $this->dataSourceModel->hasRelationArray;
    }
    public function setAdditionalParams($additionalParams)
    {
        if ($additionalParams) {
            $this->additionalParams = $additionalParams;
        }
    }
    private function linkModelSource($dataSourceId)
    {
        $this->dataSourceId = $dataSourceId;
        // si vide on puise dans le test
        if (!$this->dataSourceId) {
            $this->dataSourceId = $this->wakamailto->data_source->test_id;
        }
        //on enregistre le modèle
        //trace_log($this->wakamailto->data_source->modelClass);
        return $this->wakamailto->data_source->modelClass::find($this->dataSourceId);
    }

    public function renderMailto($dataSourceId, $type = 'inline')
    {
        $this->prepareCreatorVars($dataSourceId);

        $data = [];
        //$data['collections'] = $this->wakamailto->data_source->getFunctionsCollections($dataSourceId, $this->wakamailto);
        $data = [];
        $data['model'] = $this->wakamailto->data_source->getValues($dataSourceId);
        $data['images'] = $this->wakamailto->data_source->getPicturesUrl($dataSourceId, $this->wakamailto->images);
        $data['collections'] = $this->wakamailto->data_source->getFunctionsCollections($dataSourceId, $this->wakamailto->model_functions);
        $data['settings'] = null;

        trace_log(compact('data'));

        $html = \Twig::parse($this->wakamailto->template, compact('data'));

        // if ($type == "html") {
        //     return $html;
        // }
        $mailto = \PDF::loadHtml($html);
        $mailto->setOption('margin-top', 10);
        $mailto->setOption('margin-right', 10);
        $mailto->setOption('margin-bottom', 10);
        $mailto->setOption('margin-left', 10);
        $mailto->setOption('viewport-size', '1280x1024');
        // $mailto->setOption('enable-javascript', true);
        // $mailto->setOption('javascript-delay', 5000);
        $mailto->setOption('enable-smart-shrinking', true);
        // $mailto->setOption('no-stop-slow-scripts', true);

        if (!$type || $type == "download") {
            return $mailto->download('test2.mailto');
        } else {
            return $mailto->inline('test2.mailto');
        }
    }

    public function getDotedValues()
    {
        $array = [];
        if ($this->additionalParams) {
            if (count($this->additionalParams)) {
                $rel = $this->wakamailto->data_source->getDotedRelationValues($this->dataSourceId, $this->additionalParams);
                //trace_log($rel);
                $array = array_merge($array, $rel);
                //trace_log($array);
            }
        }

        $rel = $this->wakamailto->data_source->getDotedValues($this->dataSourceId);
        //trace_log($rel);
        $array = array_merge($array, $rel);
        return $array;

    }

}
