<?php namespace Waka\Mailtoer\Classes;

use ApplicationException;
use Event;
use Waka\Mailtoer\Models\WakaMailto;
use Waka\Utils\Classes\DataSource;

class MailtoCreator extends \Winter\Storm\Extension\Extendable
{

    public static $productor;
    public $ds;
    public $modelId;
    private $isTwigStarted;
    public $manualData = [];
    public $implement = [];
    public $askResponse = [];

    public static function find($mail_id, $slug = false)
    {
        $productor;
        if ($slug) {
            $productor = WakaMailto::where('slug', $mail_id)->first();
            if (!$productor) {
                throw new ApplicationException("Le code email ne fonctionne pas : " . $mail_id);
            }
        } else {
            $productor = WakaMailto::find($mail_id);
        }
        self::$productor = $productor;
        return new self;
    }

    public function setManualData($data) {
        $this->manualData = array_merge($this->manualData, $data);
        return $this;
    }

    public function prepare()
    {
        if ((!self::$ds || !$this->modelId)) {
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
        $this->startTwig();
        $content = html_entity_decode($this->prepare());
        $this->stopTwig();
        

        $body = rawurlencode($content);
        $subject = rawurlencode($this->getProductor()->subject);
        $to = self::$ds->getContact('to')[0];
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
