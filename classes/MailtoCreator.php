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

    public function prepareCreatorVars($dataSourceId = null)
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

    public function createMailto($dataSourceId = null)
    {
        $this->prepareCreatorVars($dataSourceId);

        $varName = strtolower($this->wakamailto->data_source->model);

        // Log du mailto
        $uniqueKey = uniqid() . str_Random(8);
        $log = new \Waka\Utils\Models\SourceLog();
        $log->key = $uniqueKey;
        $log->send_targeteable_id = $dataSourceId;
        $log->send_targeteable_type = $this->wakamailto->data_source->modelClass;
        $test = $this->wakamailto->sends()->add($log);

        $doted = $this->wakamailto->data_source->getValues($dataSourceId);
        $img = $this->wakamailto->data_source->getPicturesUrl($dataSourceId, $this->wakamailto->images);
        $fnc = $this->wakamailto->data_source->getFunctionsCollections($dataSourceId, $this->wakamailto->model_functions);

        $model = [
            $varName => $doted,
            'IMG' => $img,
            'FNC' => $fnc,
            'key' => $uniqueKey,
        ];

        $html = \Twig::parse($this->wakamailto->template, $model);
        $body = rawurlencode($html);
        $subject = rawurlencode($this->wakamailto->subject);
        $to = $this->wakamailto->data_source->getContact('ask_to', $dataSourceId)[0];
        //trace_log($to);
        $obj = [
            'to' => $to,
            'subject' => $subject,
            'body' => $body,
            'text' => $html,
        ];

        return $obj;
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
