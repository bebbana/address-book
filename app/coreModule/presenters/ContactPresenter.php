<?php

namespace App\CoreModule\Presenters;

use App\CoreModule\Model\ContactManager;
use App\CoreModule\Presenters\BaseCorePresenter;
//use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

/**
 * processing of rendering of contacts
 * @package App\CoreModule\Presenters
 */
class ContactPresenter extends BaseCorePresenter {

    const //form validation constants
            FORM_MSG_REQUIRED = 'Tohle pole je povinné.';

    /**
     * @var App\CoreModule\Model\ContactManager */
    public $contactManager;

    /**
     * Constructor 
     * @param ContactManager $contactManager automaticaly injected class for users
     */
    public function __construct(ContactManager $contactManager) {
        parent::__construct();
        $this->contactManager = $contactManager;
    }

    /** Load and render default list of contacts
     *
     */
    public function renderDefault() {
        $this->template->contacts = $this->contactManager->getContacts();
    }

    /**
     * return contact edit form
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
                $this->contactManager->saveContact($values);
            } else {
                $this->contactManager->updateContact($values);
            }
            $this->flashMessage('Kontakt byl úspěšně uložen!', 'success');
        } catch (UniqueConstraintViolationException $ex) {
            Debugger::log($ex);
            $this->flashMessage('Kontakt s touto URL už existuje!', 'danger');
        }
        $this->redirect('default');
    }

    /**
     * render editation of contact by urlcounter
     * @param array $urlcounter contacts url with counter
     */
    public function actionEditor($urlcounter) {
        
        if ($urlcounter != "editor") {
            $dbContact = $this->contactManager->getContact($urlcounter);
            $this['editorForm']->setDefaults($dbContact);
        } else {
            $this['editorForm']->setDefaults([]);
        }
    }

    /**
     * Delete contact
     * @param int $id
     */
    public function actionRemove($id) {
        try {
            $this->contactManager->removeContact($id);
            $this->flashMessage('Kontaktní záznam byl úspěšně smazán!', 'success');
        } catch (\Exception $ex) {
            Debugger::log($ex);
            $this->flashMessage('Kontaktní záznam se nepodařilo smazat', 'danger');
        }
        $this->redirect('default');
    }

}
