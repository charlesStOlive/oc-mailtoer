<?php namespace Waka\Mailtoer\Behaviors;

use Backend\Classes\ControllerBehavior;
use Waka\Mailtoer\Classes\MailtoCreator;
use Waka\Mailtoer\Models\WakaMailto;
use Waka\Utils\Classes\DataSource;

class MailtoBehavior extends ControllerBehavior
{
    //use \Waka\Utils\Classes\Traits\StringRelation;

    protected $mailtoBehaviorWidget;

    public function __construct($controller)
    {
        parent::__construct($controller);
        $controller->addJs('/plugins/waka/utils/widgets/sidebarattributes/assets/js/clipboard.min.js');
        //$this->mailtoBehaviorWidget = $this->createMailtoBehaviorWidget();
    }

    public function getPostContent()
    {
        $model = post('model');
        $modelId = post('modelId');

        $ds = new DataSource($model, 'class');
        $options = $ds->getPartialOptions($modelId, 'Waka\Mailtoer\Models\WakaMailto');

        $this->vars['options'] = $options;
        $this->vars['modelId'] = $modelId;
    }
    /**
     * LOAD DES POPUPS
     */
    public function onLoadMailtoBehaviorPopupForm()
    {
        $this->getPostContent();
        return $this->makePartial('$/waka/mailtoer/behaviors/mailtobehavior/_popup.htm');
    }
    public function onLoadMailtoBehaviorContentForm()
    {
        $this->getPostContent();
        return [
            '#popupActionContent' => $this->makePartial('$/waka/mailtoer/behaviors/mailtobehavior/_content.htm'),
        ];
    }

    public function onMailtoBehaviorPopupValidation()
    {
        $errors = $this->CheckValidation(\Input::all());
        if ($errors) {
            throw new \ValidationException(['error' => $errors]);
        }
        $wakaMailtoId = post('wakaMailtoId');
        $modelId = post('modelId');

        //return Redirect::to('/backend/waka/mailtoer/wakamailtos/makemailto/?wakaMailtoId=' . $wakaMailtoId . '&modelId=' . $modelId);
        return $this->makemailto($wakaMailtoId, $modelId);

    }

    /**
     * Validations
     */
    public function CheckValidation($inputs)
    {
        $rules = [
            'wakaMailtoId' => 'required',
        ];

        $validator = \Validator::make($inputs, $rules);

        if ($validator->fails()) {
            return $validator->messages()->first();
        } else {
            return false;
        }
    }
    /**
     * Cette fonction est utilisé lors du test depuis le controller wakamailto.
     */
    // public function onLoadMailtoTest()
    // {
    //     $type = post('type');
    //     $wakaMailtoId = post('wakaMailtoId');
    //     $this->vars['wakaMailtoId'] = $wakaMailtoId;

    //     return $this->onLoadMailtoBehaviorPopupForm()
    //     //return $this->makemailto($wakaMailtoId);
    //     return $this->makePartial('$/waka/mailtoer/behaviors/mailtobehavior/_popup.htm');
    //     //return Redirect::to('/backend/waka/mailtoer/wakamailtos/makemailto/?wakaMailtoId=' . $wakaMailtoId . '&type=' . $type);
    // }

    public function makemailto($wakaMailtoId, $modelId = null)
    {
        // $wakaMailtoId = post('wakaMailtoId');
        // $modelId = post('modelId');

        $mc = new MailtoCreator($wakaMailtoId);
        $textobj = $mc->createMailto($modelId);
        $this->vars['textobj'] = $textobj;
        //trace_log($textobj);
        return [
            '#mailtoContent' => $this->makePartial('$/waka/mailtoer/behaviors/mailtobehavior/_text.htm'),
        ];

        //$url = "mailto:$to?Subject=$subject&Body=$body";
        //return redirect::to($url);
    }

    // public function createMailtoBehaviorWidget()
    // {

    //     $config = $this->makeConfig('$/waka/mailtoer/models/wakamailto/fields_for_mailto.yaml');
    //     $config->alias = 'mailtoBehaviorformWidget';
    //     $config->arrayName = 'mailtoBehavior_array';
    //     $config->model = new WakaMailto();
    //     $widget = $this->makeWidget('Backend\Widgets\Form', $config);
    //     $widget->bindToController();
    //     return $widget;
    // }
}
