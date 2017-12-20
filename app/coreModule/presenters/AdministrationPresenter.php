<?php

namespace App\CoreModule\Presenters;

use App\Forms\UserForms;
use App\Presenters\BasePresenter;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Zpracovává vykreslování administrační sekce.
 * @package App\CoreModule\Presenters
 */
class AdministrationPresenter extends BasePresenter
{
        /** @var UserForms Továrnička na uživatelské formuláře. */
        private $userFormsFactory;

        /** @var array Společné instrukce pro přihlašovací a registrační formuláře. */
        private $instructions;

        /**
         * Konstruktor s injektovanou továrničkou na uživatelské formuláře.
         * @param UserForms $userForms automaticky injektovaná třída továrničky na uživatelské formuláře
         */
        public function __construct(UserForms $userForms)
        {
                parent::__construct();
                $this->userFormsFactory = $userForms;
        }

        /** Volá se před každou akcí presenteru a inicializuje společné proměnné. */
        public function startup()
        {
                parent::startup();
                $this->instructions = array(
                        'message' => null,
                        'redirection' => ':Core:Contact:default'
                );
        }

        /** Přesměrování do administrace, pokud je uživatel již přihlášen. */
        public function actionLogin()
        {
                if ($this->getUser()->isLoggedIn()) $this->redirect(':Core:Contact:default');
        }

        /** Odhlášení uživatele. */
        public function actionLogout()
        {
                $this->getUser()->logout();
                $this->redirect(':Core:Contact:default');
        }

        /** Vykreslí administrační stránku. 
        public function renderDefault()
        {
                $identity = $this->getUser()->getIdentity();
                if ($identity) $this->template->username = $identity->getData()['username'];
        } */

        /**
         * Vrací komponentu přihlašovacího formuláře z továrničky.
         * @return Form přihlašovací formulář
         */
        protected function createComponentLoginForm()
        {
                $this->instructions['message'] = 'Byl jste úspěšně přihlášen.';
                return $this->userFormsFactory->createLoginForm(ArrayHash::from($this->instructions));
        }

        /**
         * Vrací komponentu registračního formuláře z továrničky.
         * @return Form registrační formulář
         */
        protected function createComponentRegisterForm()
        {
                $this->instructions['message'] = 'Byl jste úspěšně zaregistrován.';
                return $this->userFormsFactory->createRegisterForm(ArrayHash::from($this->instructions));
        }
}