<?php

namespace app\forms;

require("../vendor/autoload.php");

use app\exceptions\SignException;
use app\models\SignManager;
use Nette\Forms\Form;

/**
 * Form FullSignInForm
 *
 * @package app\forms
 */
final class  FullSignInForm extends FormFactory
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
        $this->form = parent::getForm("SignIn");
    }

    /**
     * @param callable $onSuccess
     *
     * @return Form
     */
    public function create(callable $onSuccess): Form
    {
        $this->form->addText('login', 'Login:')
            ->setHtmlAttribute("placeholder", "Login *")
            ->setRequired(true);
        $this->form->addPassword('password', 'Password:')
            ->setHtmlAttribute("placeholder", "Password *")
            ->setRequired(true);

        $this->form->addSubmit("submit", "Login");

        if ($this->form->isSuccess()) {
            $values = $this->form->getValues("array");
            try {
                SignManager::SignIn($values["login"], $values["password"]);
                $onSuccess();
            } catch (SignException $exception) {
                $this->form->addError($exception->getMessage());
            }
        }

        return $this->form;
    }
}
