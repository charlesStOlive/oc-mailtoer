<?php namespace Waka\Mailtoer\Classes;

use ApplicationException;
use Event;
use Waka\Mailtoer\Models\WakaMailto;
use Waka\Utils\Classes\DataSource;

class MailtoCreator extends \Winter\Storm\Extension\Extendable
{

    public static $wakamailto;
    public $ds;
    public $modelId;

    public static function find($mail_id, $slug = false)
    {
        $wakamailto;
        if ($slug) {
            $wakamailto = WakaMailto::where('slug', $mail_id)->first();
            if (!$wakamailto) {
                throw new ApplicationException("Le code email ne fonctionne pas : " . $mail_id);
            }
        } else {
            $wakamailto = WakaMailto::find($mail_id);
        }
        self::$wakamailto = $wakamailto;
        return new self;
    }

    public static function getProductor()
    {
        return self::$wakamailto;
    }

    public function render($modelId = null)
    {
        $this->modelId = $modelId;
        $this->ds = new DataSource($this->getProductor()->data_source);

        $varName = strtolower($this->ds->name);
        $doted = $this->ds->getValues($modelId);
        $img = $this->ds->wimages->getPicturesUrl($this->getProductor()->images);
        $fnc = $this->ds->getFunctionsCollections($modelId, $this->getProductor()->model_functions);

        $model = [
            $varName => $doted,
            'IMG' => $img,
            'FNC' => $fnc,
        ];

        //Recupère des variables par des evenements exemple LP log dans la finction boot
        $dataModelFromEvent = Event::fire('waka.productor.subscribeData', [$this]);
        //trace_log($dataModelFromEvent);
        if ($dataModelFromEvent[0] ?? false) {
            foreach ($dataModelFromEvent as $dataEvent) {
                //la fonction renvoi un array du type [0] => [key => $data] elle est traduite en key =>data
                $model[key($dataEvent)] = $dataEvent[key($dataEvent)];
            }
        }

        //trace_log($model);

        $html = \Twig::parse($this->getProductor()->template, $model);
        $body = rawurlencode($html);
        $subject = rawurlencode($this->getProductor()->subject);
        $to = $this->ds->getContact('to', $modelId)[0] ?? '';
        //trace_log($to);
        $obj = [
            'to' => $to,
            'subject' => $subject,
            'body' => $body,
            'text' => $html,
        ];

        return $obj;
    }
}
