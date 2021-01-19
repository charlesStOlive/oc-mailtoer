<?php namespace Waka\Mailtoer\Classes;

use Waka\Mailtoer\Models\WakaMailto;
use Waka\Utils\Classes\DataSource;

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

    // public function prepareCreatorVars($dataSourceId = null)
    // {
    //     $this->dataSourceModel = $this->linkModelSource($dataSourceId);
    //     $this->dataSourceAdditionalParams = $this->dataSourceModel->hasRelationArray;
    // }
    // public function setAdditionalParams($additionalParams)
    // {
    //     if ($additionalParams) {
    //         $this->additionalParams = $additionalParams;
    //     }
    // }
    // private function linkModelSource($dataSourceId)
    // {
    //     $this->dataSourceId = $dataSourceId;
    //     // si vide on puise dans le test
    //     if (!$this->dataSourceId) {
    //         $this->dataSourceId = $this->wakamailto->data_source->test_id;
    //     }
    //     //on enregistre le modèle
    //     //trace_log($this->wakamailto->data_source->modelClass);
    //     return $this->wakamailto->data_source->modelClass::find($this->dataSourceId);
    // }

    public function createMailto($modelId = null)
    {
        $ds = new DataSource($this->wakamailto->data_source);

        $varName = strtolower($ds->name);

        $logKey = null;
        if (class_exists('\Waka\Lp\Classes\LogKey')) {
            if ($this->wakamailto->use_key) {
                $logKey = new \Waka\Lp\Classes\LogKey($modelId, $this->wakamailto);
                $logKey->add();
            }
        }

        $doted = $ds->getValues($modelId);
        $img = $ds->wimages->getPicturesUrl($this->wakamailto->images);
        $fnc = $ds->getFunctionsCollections($modelId, $this->wakamailto->model_functions);

        $model = [
            $varName => $doted,
            'IMG' => $img,
            'FNC' => $fnc,
            'log' => $logKey ? $logKey->log : null,
        ];

        trace_log($model);

        $html = \Twig::parse($this->wakamailto->template, $model);
        $body = rawurlencode($html);
        $subject = rawurlencode($this->wakamailto->subject);
        $to = $ds->getContact('to', $modelId)[0] ?? '';
        //trace_log($to);
        $obj = [
            'to' => $to,
            'subject' => $subject,
            'body' => $body,
            'text' => $html,
        ];

        return $obj;
    }

    // public function getDotedValues()
    // {
    //     $array = [];
    //     if ($this->additionalParams) {
    //         if (count($this->additionalParams)) {
    //             $rel = $this->wakamailto->data_source->getDotedRelationValues($this->dataSourceId, $this->additionalParams);
    //             //trace_log($rel);
    //             $array = array_merge($array, $rel);
    //             //trace_log($array);
    //         }
    //     }

    //     $rel = $this->wakamailto->data_source->getDotedValues($this->dataSourceId);
    //     //trace_log($rel);
    //     $array = array_merge($array, $rel);
    //     return $array;

    // }

}
