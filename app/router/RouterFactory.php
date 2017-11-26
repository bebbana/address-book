<?php

namespace App;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

/**
 * Routing factory for whole application
 * @package App
 */
class RouterFactory {

    /**
     * create router
     * @return RouteList Route list
     */
    public static function createRouter() {
        $router = new RouteList();
        $router[] = new Route('<action>/<id>', 'Core:User:remove');
        $router[] = new Route('<urlcounter>', 'Core:User:editor');
        $router[] = new Route('<action>/[<url>]', 'Core:User:default');
        return $router;
    }

}
