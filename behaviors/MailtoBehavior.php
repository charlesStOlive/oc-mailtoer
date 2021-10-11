<?php namespace Waka\Mailtoer\Behaviors;

use Backend\Classes\ControllerBehavior;
use Waka\Mailtoer\Classes\MailtoCreator;
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
        $modelClass = post('modelClass');
        $modelId = post('modelId');

        $ds = \DataSources::findByClass($modelClass);
        $options = $ds->getProductorOptions('Waka\Mailtoer\Models\WakaMailto', $modelId);

        $this->vars['options'] = $options;
        $this->vars['modelId'] = $modelId;
    }
    /**
     * LOAD DES POPUPS
     */
    public function onLoadMailtoBehaviorPopupForm()
    {
        $this->getPostContent();
        if($this->vars['options']) {
            return $this->makePartial('$/waka/mailtoer/behaviors/mailtobehavior/_popup.htm');
        } else {
            return $this->makePartial('$/waka/utils/views/_popup_no_model.htm');
        }
        
    }
    public function onLoadMailtoBehaviorContentForm()
    {
        $this->getPostContent();
        if($this->vars['options']) {
             return ['#popupActionContent' => $this->makePartial('$/waka/mailtoer/behaviors/mailtobehavior/_content.htm')];
        } else {
             return ['#popupActionContent' => $this->makePartial('$/waka/utils/views/_content_no_model.htm')];
        }
    }

    public function onMailtoBehaviorPopupValidation()
    {
        $errors = $this->CheckValidation(\Input::all());
        if ($errors) {
            throw new \ValidationException(['error' => $errors]);
        }
        $productorId = post('productorId');
        $modelId = post('modelId');

        //return Redirect::to('/backend/waka/mailtoer/wakamailtos/makemailto/?productorId=' . $productorId . '&modelId=' . $modelId);
        return $this->makemailto($productorId, $modelId);
    }

    /**
     * Validations
     */
    public function CheckValidation($inputs)
    {
        $rules = [
            'productorId' => 'required',
        ];

        $validator = \Validator::make($inputs, $rules);

        if ($validator->fails()) {
            return $validator->messages()->first();
        } else {
            return false;
        }
    }

    public function makemailto($productorId, $modelId = null)
    {
        $this->vars['textobj'] = MailtoCreator::find($productorId)->render($modelId);
        return [
            '#mailtoContent' => $this->makePartial('$/waka/mailtoer/behaviors/mailtobehavior/_text.htm'),
        ];
    }
}
