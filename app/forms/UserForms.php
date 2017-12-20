<?php

namespace App\Forms;

use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\User;
use Nette\Utils\ArrayHash;
use Nette\Object;

/**
 * Class UserFormsFactory
 * @package App\Forms
 */
class UserForms extends Object {

    /** @var User Uživatel. */
    private $user;

    /**
     * Constructor with injected class Nette\Security\User
     * @param User $user user
     */
    public function __construct(User $user) {
        $this->user = $user;
    }

    /**
     * do log in/register
     * @param Form $form                   form calling this method
     * @param null|ArrayHash $instructions user instructions
     * @param bool $register               register new user
     */
    private function login($form, $instructions, $register = false) {
        $presenter = $form->getPresenter(); // get presenter in which form is placed
        try {
            // get form values
            $username = $form->getValues()->username;
            $password = $form->getValues()->password;
            
            if ($register)
            // getAuthenticator() -> get class which implements Nette\Security\IAuthenticator - class UserManager and call its method register
                $this->user->getAuthenticator()->register($username, $password);
            
            $this->user->login($username, $password); // universal method for calling our method authenticate in UserManager https://doc.nette.org/cs/2.3/access-control
            // if form is placed in presenter and $instructions exist
            if ($instructions && $presenter) {
                // If instructions contain message, send it to relevant presenter
                if (isset($instructions->message))
                    $presenter->flashMessage($instructions->message);

                // redirect if it is needed in instructions
                if (isset($instructions->redirection))
                    $presenter->redirect($instructions->redirection);
            }
        } catch (AuthenticationException $ex) {
            if ($presenter) { // If form is placed in presenter
                $presenter->flashMessage($ex->getMessage()); // send message to presenter
                $presenter->redirect('this'); // redirect
            } else { // else add error message to form
                $form->addError($ex->getMessage());
            }
        }
    }

    /**
     * returns basic form
     * @param null|Form $form extendable form
     * @return Form form
     */
    private function createBasicForm(Form $form = null) {
        $form = $form ? $form : new Form;
        $form->addText('username', 'Jméno')->setRequired();
        $form->addPassword('password', 'Heslo');
        return $form;
    }

    /**
     * Returns form component for log in
     * @param null|Form $form concrete form
     * @param null|ArrayHash $instructions user instructions from concrete form
     * @return Form login form
     */
    public function createLoginForm($instructions = null, Form $form = null) {
        $form = $this->createBasicForm($form);
        $form->addSubmit('submit', 'Přihlásit');
        $form->onSuccess[] = function (Form $form) use($instructions) {
            $this->login($form, $instructions);
        };
        return $form;
    }

    /**
     * Returns form component for registration
     * @param null|Form $form             concrete form
     * @param null|ArrayHash $instructions user instructions
     * @return Form registration form
     */
    public function createRegisterForm($instructions = null, Form $form = null) {
        $form = $this->createBasicForm($form);
        $form->addPassword('password_repeat', 'Heslo znovu')
                ->addRule(Form::EQUAL, 'Hesla nesouhlasí.', $form['password']);
        $form->addText('y', 'Zadejte aktuální rok (antispam)')->setType('number')->setRequired()
                ->addRule(Form::EQUAL, 'Špatně vyplněný antispam.', date("Y"));
        $form->addSubmit('register', 'Registrovat');
        $form->onSuccess[] = function (Form $form) use ($instructions) {
            $this->login($form, $instructions, true);
        };
        return $form;
    }

}
