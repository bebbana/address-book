<?php

namespace App\CoreModule\Presenters;

use App\CoreModule\Model\UserManager;
use App\Presenters\BasePresenter;
//use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

/**
 * processing of rendering of users
 * @package App\CoreModule\Presenters
 */
class UserPresenter extends BasePresenter {

    const //form validation constants
            FORM_MSG_REQUIRED = 'Tohle pole je povinné.';

    /**
     * @var App\CoreModule\Model\UserManager */
    public $userManager;

    /**
     * Constructor 
     * @param UserManager $userManager automaticaly injected class for users
     */
    public function __construct(UserManager $userManager) {
        parent::__construct();
        $this->userManager = $userManager;
    }

    /** Load and render default list of user contacts
     *
     */
    public function renderDefault() {
        $this->template->users = $this->userManager->getUsers();
    }

    /**
     * return user edit form
     * @return Form edit form
     */
    protected function createComponentEditorForm() {
        $form = new Form;
        $form->addHidden('id');
        $form->addText('fullname', 'Jméno a příjmení')->setRequired()
                ->addRule('App\CoreModule\Presenters\Validators\MyValidators::fullnameValidator', 'Celé jméno se skládá ze jména a přijmení oddělených mezerou. Používejte pouze velká a malá písmena. Diakritika je povolená, speciální znaky nikoliv.');
        $form->addText('telnumber', 'Telefonní číslo')->setRequired()->setDefaultValue('+420')
                ->addCondition(Form::FILLED) // if telnumber is filled in
                ->addRule('App\CoreModule\Presenters\Validators\MyValidators::telnumberValidator', 'Telefonni číslo musí být ve tvaru "+420XXXXXXXXX" .');
        $form->addText('email', 'E-mail')->setRequired()
                ->addRule(Form::EMAIL, 'E-mailová adresa není platná');
        $form->addTextArea('memo', 'Poznámka'); // jakou validaci? nebo není třeba?
        $form->addHidden('url', 'URL');
        $form->addProtection('Vypršel časový limit, odešlete formulář znovu');
        $form->addSubmit('submit', 'Uložit');
        $form->onSuccess[] = [$this, 'editorFormSucceeded'];
        return $form;
    }

    /**
     * process form data when is succesfuly sent
     * @param Form $form edit form
     * @param ArrayHash $values sent data
     */
    public function editorFormSucceeded($form, $values) {
        try {
            if (!$values['id']) {
                $this->userManager->saveUser($values);
            } else {
                $this->userManager->updateUser($values);
            }
            $this->flashMessage('Uživatel byl úspěšně uložen!', 'success');
        } catch (UniqueConstraintViolationException $ex) {
            Debugger::log($ex);
            $this->flashMessage('Uživatel s touto URL už existuje!', 'danger');
        }
        $this->redirect('default');
    }

    /**
     * render editation of user by urlcounter
     * @param array $urlcounter users url with counter
     */
    public function actionEditor($urlcounter) {
        
        if ($urlcounter != "editor") {
            $dbUser = $this->userManager->getUser($urlcounter);
            
            if (!$dbUser) {
                $this->redirect('error');
            }
            $this['editorForm']->setDefaults($dbUser);
        } else {
            $this['editorForm']->setDefaults([]);
        }
    }

    /**
     * Delete user
     * @param int $id
     */
    public function actionRemove($id) {
        try {
            $this->userManager->removeUser($id);
            $this->flashMessage('Uživatel byl úspěšně smazán!', 'success');
        } catch (\Exception $ex) {
            Debugger::log($ex);
            $this->flashMessage('Uživatele se nepodařilo smazat', 'danger');
        }
        $this->redirect('default');
    }

}
