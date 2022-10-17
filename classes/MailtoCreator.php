<?php namespace Waka\Mailtoer\Classes;

use ApplicationException;
use Event;
use Waka\Mailtoer\Models\WakaMailto;
use Waka\Utils\Classes\DataSource;
use Waka\Utils\Classes\ProductorCreator;

class MailtoCreator extends ProductorCreator
{
    public $manualData = [];

    

     public static function find($mail_id, $slug = false)
    {
        $productorModel = null;
        if ($slug) {
            $productorModel = WakaMailto::where('slug', $mail_id)->first();
        } else {
            $productorModel = WakaMailto::find($mail_id);
        }
        if (!$productorModel) {
            /**/trace_log("Le code ou id  email ne fonctionne pas : " . $mail_id. "vous dever entrer l'id ou le code suivi de true");
            throw new ApplicationException("Le code ou id  email ne fonctionne pas : " . $mail_id. "vous dever entrer l'id ou le code suivi de true");
        }
        
        self::$productor = $productorModel;
        return new self;
    }




    public function setManualData($data) {
        $this->manualData = array_merge($this->manualData, $data);
        return $this;
    }

    public function prepare()
    {
        if ((!$this->productorDs || !$this->modelId)) {
            throw new \ApplicationException("Le modelId n a pas ete instancié et il n' y a pas de données manuel");
        }
        $model = $this->getProductorVars();
        //Ajout des donnnées manuels
        if(count($this->manualData)) {
            $model = array_merge($model, $this->manualData);
        }

        $content = $this->getProductor()->content;
        //trace_log($model);
        $content = \Twig::parse($content, $model);
        return $content;
    }

    public function render()
    {
        
        $content = html_entity_decode($this->prepare());
        $body = rawurlencode($content);
        $subject = rawurlencode($this->getProductor()->subject);
        $to = $this->productorDs->getContact('to')[0];
        //trace_log($to);
        $obj = [
            'to' => $to,
            'subject' => $subject,
            'body' => $body,
            'text' => $content,
        ];

        return $obj;
    }

    public function getModelEmails()
    {
        return self::$ds->getContact('to', null);
    }
}
