<?php

namespace App\CoreModule\Model;

use App\Model\BaseManager;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;
use Tracy\Debugger;

/**
 * Class for management of users
 * @package App\CoreModule\Model
 */
class UserManager extends BaseManager {

    /** Constants for model manipulation */
    const
            TABLE_NAME = 'users',
            COLUMN_ID = 'id',
            COLUMN_URL = 'url',
            COLUMN_COUNTER = 'counter',
            COLUMN_FULLNAME = 'fullname';

    /**
     * @return Selection list of user contacts
     */
    public function getUsers() {
        return $this->database->table(self::TABLE_NAME)->order(self::COLUMN_ID . ' DESC');
    }

    /**
     * Returns user by URL and counter
     * @param string $url URl of user contact
     * @param string $counter counter of user contact
     * @return bool|mixed|First line with required URL or returns false
     */
    public function getUser($urlcounter) {

        
            $temp = explode("_", $urlcounter);
            $url = $temp[0];
            if (!isset($temp[1])) {
                $counter = 0;
            } else {
                $counter = $temp[1];
            }
            $user = $this->getUsers()->where(self::COLUMN_URL, $url)->where('counter', $counter)->select('*')->fetch();
            return $user;
    }

    /**
     * Save new user to table. 
     * @param array|ArrayHash $user user
     */
    public function saveUser($user) {
        $slug = Strings::webalize($user['fullname']);
        $user = (array) $user;
        $urlCounterDb = $this->getUrlCounterFromDb($slug);
        $urlCount = $this->createNewUrlCounter($urlCounterDb);
        $user['url'] = $slug;
        $user['counter'] = $urlCount;
        unset($user['id']);
        $this->database->table(self::TABLE_NAME)->insert($user);
    }

    /**
     * Update existing user
     * @param array|ArrayHash $user user
     */
    public function updateUser($user) {
        //controll in the case of fullname change

        $slug = Strings::webalize($user['fullname']);

        $userData = $this->getUser($slug);

        $user = (array) $user;
        unset($user['url']);
        if ($userData['url'] != $slug) {
            $urlCounterDb = $this->getUrlCounterFromDb($slug);
            $urlCount = $this->createNewUrlCounter($urlCounterDb);
            $user['url'] = $slug;
            $user['counter'] = $urlCount;
        }
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $user[self::COLUMN_ID])->update($user);
    }

    /**
     * create user's url counter
     * @param int #urlCounter url counter
     */
    private function createNewUrlCounter($urlCounter) {
        if ($urlCounter === null) {
            $urlCounter = 0;
        } else {
            $urlCounter++;
        }
        return $urlCounter;
    }

    /**
     * Delete user
     * @param int #id  user contact id
     */
    public function removeUser($id) {
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $id)->delete();
    }

    /**
     * get max user's url counter
     * @param int #counter url counter
     */
    private function getUrlCounterFromDb($url) {
        return $this->getUsers()->where(self::COLUMN_URL, $url)->max('counter');
    }

    /**
     * create url of user for template edit link
     * @param array #userData user data 
     */
    public function createCompleteUrl($userData) {
        return $userData['url'] . ($userData['counter'] > 0 ? '_' . $userData['counter'] : null);
    }

}
