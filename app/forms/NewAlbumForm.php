<?php

namespace app\forms;

require("../vendor/autoload.php");

use app\exceptions\SignException;
use app\models\SignManager;
use Exception;
use Nette\Forms\Form;

/**
 * Form FullSignInForm
 *
 * @package app\forms
 */
final class  NewAlbumForm extends FormFactory
{

    /**
     * @var Form $form
     */
    private Form $form;

    /**
     * FullSignUp constructor.
     */
    public function __construct()
    {
        $this->form = parent::getBootstrapForm("NewAlbumForm");
    }

    /**
     * @param callable $onSuccess
     *
     * @return Form
     */
    public function create(callable $onSuccess): Form
    {
        $this->form->addText('albumTitle', 'Album title:')
            ->setHtmlAttribute("placeholder", "album title")
            ->setRequired(true);
        $this->form->addText('albumDescription', 'Album description:')
            ->setHtmlAttribute("placeholder", "album description *")
            ->setRequired(true);
        $this->form->addText('albumKeywords', 'Album keywords:')
            ->setHtmlAttribute("placeholder", "album keywords *")
            ->setRequired(true);
        $this->form->addSubmit("submit", "Create");

        if ($this->form->isSuccess()) {
            $values = $this->form->getValues("array");
            try {
                $onSuccess($values);
            } catch (Exception $exception) {
                $this->form->addError($exception->getMessage());
            }
        }

        return $this->form;
    }
}
