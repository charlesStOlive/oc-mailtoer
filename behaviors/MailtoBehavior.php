<?php namespace Waka\Mailtoer\Behaviors;

use Backend\Classes\ControllerBehavior;
use Waka\Mailtoer\Classes\MailtoCreator;
use Waka\Mailtoer\Models\WakaMailto;

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

    /**
     * METHODES
     */

    public function getDataSourceClassName(String $model)
    {
        $modelClassDecouped = explode('\\', $model);
        return array_pop($modelClassDecouped);

    }

    public function getDataSourceFromModel(String $model)
    {
        $modelClassName = $this->getDataSourceClassName($model);
        //On recherche le data Source depuis le nom du model
        return \Waka\Utils\Models\DataSource::where('model', '=', $modelClassName)->first();
    }

    public function getModel($model, $modelId)
    {
        $myModel = $model::find($modelId);
        return $myModel;
    }

    public function getPartialOptions($model, $modelId)
    {
        $modelClassName = $this->getDataSourceClassName($model);

        $options = wakaMailto::whereHas('data_source', function ($query) use ($modelClassName) {
            $query->where('model', '=', $modelClassName);
        });

        $optionsList = [];

        foreach ($options->get() as $option) {
            $optionsList[$option->id] = $option->name;
        }
        return $optionsList;

    }
    public function getPostContent()
    {
        $model = post('model');
        $modelId = post('modelId');

        $options = $this->getPartialOptions($model, $modelId);

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
