<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter
{
    /** Load and render default list of user contacts
     *
     */
    public function renderError() {
        $this->template->error = "Stránka nenalezena!";
    }
}