<?php

namespace App\Model;

use Nette\Database\Context;
use Nette\Object;

/**
 * basic model
 * ensures access to other models and to db
 * @package App\Model
 */
abstract class BaseManager extends Object
{
        /** @var Context DB object */
        protected $database;

        /**
         * constructor for injected db object 
         * @param Context $database injected class DB
         */
        public function __construct(Context $database)
        {
                $this->database = $database;
        }
}