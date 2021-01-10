<?php

namespace app\forms;

require("../vendor/autoload.php");

use Exception;
use Nette\Forms\Form;

/**
 * Form FullSignInForm
 *
 * @package app\forms
 */
final class  EditAlbumImageForm extends FormFactory
{

    private string $title;
    private string $description;
    private string $keywords;
    private bool $visible;
    /**
     * @var Form $form
     */
    private Form $form;

    /**
     * EditAlbumForm constructor.
     *
     * @param string $title
     * @param string $description
     * @param string $keywords
     * @param bool   $visible
     */
    public function __construct(string $title, string $description, string $keywords, bool $visible)
    {
        $this->title = $title;
        $this->description = $description;
        $this->keywords = $keywords;
        $this->visible = $visible;
        $this->form = parent::getBootstrapForm("EditAlbumForm");
    }

    /**
     * @param callable $onSuccess
     *
     * @return Form
     */
    public function create(callable $onSuccess): Form
    {
        $this->form->addText('albumTitle', 'Album title:')
            ->setHtmlAttribute("placeholder", $this->title)
            ->setRequired(true);
        $this->form->addTextArea('albumDescription', 'Album description:')
            ->setHtmlAttribute("placeholder", $this->description)
            ->setHtmlAttribute("class", "form-control")
            ->setRequired(true);
        $this->form->addText('albumKeywords', 'Album keywords:')
            ->setHtmlAttribute("placeholder", $this->keywords)
            ->setRequired(true);
        $this->form->addCheckbox('albumVisible', "Album is visible:")
            ->setDefaultValue($this->visible);
        $this->form->addSubmit("submit", "Edit");

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
