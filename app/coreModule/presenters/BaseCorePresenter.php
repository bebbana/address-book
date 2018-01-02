<?php

namespace App\CoreModule\Presenters;

use App\Presenters\BasePresenter;

/**
 * Basic presenter for all coreModule presenters
 * @package App\CoreModule\Presenters
 */
abstract class BaseCorePresenter extends BasePresenter
{
        /** @var null|string presenter addres for logging within coreModule. */
        protected $loginPresenter = ':Core:Administration:login';
}