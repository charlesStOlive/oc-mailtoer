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
    private $isTwigStarted;
    public $manualData = [];
    public $implement = [];
    public $askResponse = [];

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

    public function setModelId($modelId)
    {
        //trace_log('setModelId');
        $this->modelId = $modelId;
        $dataSourceCode = $this->getProductor()->data_source;
        $this->ds = \DataSources::find($dataSourceCode);
        $this->ds->instanciateModel($modelId);
        //trace_log('ok');
        return $this;
    }

    public function setModelTest()
    {
        $this->modelId = $this->getProductor()->test_id;
        $dataSourceCode = $this->getProductor()->data_source;
        $this->ds = \DataSources::find($dataSourceCode);
        $this->ds->instanciateModel($this->modelId);
        return $this;
    }

    public function setAsksResponse($datas = [])
    {
        if($this->ds) {
             $this->askResponse = $this->ds->getAsksFromData($datas, $this->getProductor()->asks);
        } else {
            $this->askResponse = [];
        }
        return $this;
    }

    public function setRuleAsksResponse($datas = [])
    {
        $askArray = [];
        $srcmodel = $this->ds->getModel($this->modelId);
        $asks = $this->getProductor()->rule_asks()->get();
        foreach($asks as $ask) {
            $key = $ask->getCode();
            //trace_log($key);
            $askResolved = $ask->resolve($srcmodel, 'txt', $datas);
            $askArray[$key] = $askResolved;
        }
        //trace_log($askArray);
        return array_replace($askArray,$this->askResponse);
        
    }

    public function setRuleFncsResponse()
    {
        $fncArray = [];
        $srcmodel = $this->ds->getModel($this->modelId);
        $fncs = $this->getProductor()->rule_fncs()->get();
        foreach($fncs as $fnc) {
            $key = $fnc->getCode();
            //trace_log('key of the function');
            $fncResolved = $fnc->resolve($srcmodel,$this->ds->code);
            $fncArray[$key] = $fncResolved;
        }
        //trace_log($fncArray);
        return $fncArray;
        
    }

    public function setdefaultAsks($datas = [])
    {
        if($this->ds) {
             $this->askResponse = $this->ds->getAsksFromData($datas, $this->getProductor()->asks);
        } else {
            $this->askResponse = [];
        }
        return $this;
    }

    public function checkConditions()//Ancienement checkScopes
    {
        $conditions = new \Waka\Utils\Classes\Conditions($this->getProductor(), $this->ds->model);
        return $conditions->checkConditions();
    }

    public function setManualData($data) {
        $this->manualData = array_merge($this->manualData, $data);
        return $this;
    }

    public function prepare()
    {
        if ((!$this->ds || !$this->modelId)) {
            throw new \ApplicationException("Le modelId n a pas ete instancié et il n' y a pas de données manuel");
        }
        $model = [];
        //Fusion des données avec prepare model reoturne un objet avec ds, imag et fnc

        if($this->ds && $this->modelId) {
            $values = $this->ds->getValues($this->modelId);
            $model = [
                'ds' => $values,
            ];
        }
        //Ajout des donnnées manuels
        if(count($this->manualData)) {
            $model = array_merge($model, $this->manualData);
        }

        //Nouveau bloc pour nouveaux asks
        if($this->getProductor()->rule_asks()->count()) {
            $this->askResponse = $this->setRuleAsksResponse($model);

            if(!$this->askResponse) {
                $this->setAsksResponse($model);
            }
        } 
        

        //Nouveau bloc pour les new Fncs
        if($this->getProductor()->rule_fncs()->count()) {
            $fncs = $this->setRuleFncsResponse($model);
            $model = array_merge($model, [ 'fncs' => $fncs]);
        }
        

        $model = array_merge($model, [ 'asks' => $this->askResponse]);

        //Recupère des variables par des evenements exemple LP log dans la finction boot
        $dataModelFromEvent = Event::fire('waka.productor.subscribeData', [$this]);
        if ($dataModelFromEvent[0] ?? false) {
            foreach ($dataModelFromEvent as $dataEvent) {
                //trace_log($dataEvent);
               $model[key($dataEvent)] = $dataEvent[key($dataEvent)];
            }
        }

        $content = $this->getProductor()->content;
        trace_log($model);
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
        $to = $this->ds->getContact('to')[0];
        //trace_log($to);
        $obj = [
            'to' => $to,
            'subject' => $subject,
            'body' => $body,
            'text' => $content,
        ];

        return $obj;
    }

    protected function startTwig()
    {
        if ($this->isTwigStarted) {
            return;
        }

        $this->isTwigStarted = true;

        $markupManager = \System\Classes\MarkupManager::instance();
        $markupManager->beginTransaction();
        $markupManager->registerTokenParsers([
            new \System\Twig\MailPartialTokenParser,
        ]);
    }

    /**
     * Indicates that we are finished with Twig.
     * @return void
     */
    protected function stopTwig()
    {
        if (!$this->isTwigStarted) {
            return;
        }

        $markupManager = \System\Classes\MarkupManager::instance();
        $markupManager->endTransaction();

        $this->isTwigStarted = false;
    }

    public function getModelEmails()
    {
        return $this->ds->getContact('to', null);
    }
}
