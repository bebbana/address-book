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
class ContactManager extends BaseManager {

    /** Constants for model manipulation */
    const
            TABLE_NAME = 'user_contacts',
            COLUMN_ID = 'id',
            COLUMN_URL = 'url',
            COLUMN_COUNTER = 'counter',
            COLUMN_FULLNAME = 'fullname';

    /**
     * @return Selection list of contacts
     */
    public function getContacts() {
        return $this->database->table(self::TABLE_NAME)->order(self::COLUMN_ID . ' DESC');
    }

    /**
     * Returns contact by URL and counter
     * @param string $url URl of contact
     * @param string $counter counter of contact
     * @return bool|mixed|First line with required URL or returns false
     */
    public function getContact($urlcounter) {

        
            $temp = explode("_", $urlcounter);
            $url = $temp[0];
            if (!isset($temp[1])) {
                $counter = 0;
            } else {
                $counter = $temp[1];
            }
            $contact = $this->getContacts()->where(self::COLUMN_URL, $url)->where('counter', $counter)->select('*')->fetch();
            return $contact;
    }

    /**
     * Save new contact to table. 
     * @param array|ArrayHash $contact contact
     */
    public function saveContact($contact) {
        $slug = Strings::webalize($contact['fullname']);
        $contact = (array) $contact;
        $urlCounterDb = $this->getUrlCounterFromDb($slug);
        $urlCount = $this->createNewUrlCounter($urlCounterDb);
        $contact['url'] = $slug;
        $contact['counter'] = $urlCount;
        unset($contact['id']);
        $this->database->table(self::TABLE_NAME)->insert($contact);
    }

    /**
     * Update existing contact
     * @param array|ArrayHash $contact contact
     */
    public function updateContact($contact) {
        //controll in the case of fullname change

        $slug = Strings::webalize($contact['fullname']);

        $contactData = $this->getContact($slug);

        $contact = (array) $contact;
        unset($contact['url']);
        if ($contactData['url'] != $slug) {
            $urlCounterDb = $this->getUrlCounterFromDb($slug);
            $urlCount = $this->createNewUrlCounter($urlCounterDb);
            $contact['url'] = $slug;
            $contact['counter'] = $urlCount;
        }
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $contact[self::COLUMN_ID])->update($contact);
    }

    /**
     * create contact's url counter
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
     * Delete contact
     * @param int #id  contact id
     */
    public function removeContact($id) {
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $id)->delete();
    }

    /**
     * get max contact's url counter
     * @param int #counter url counter
     */
    private function getUrlCounterFromDb($url) {
        return $this->getContacts()->where(self::COLUMN_URL, $url)->max('counter');
    }

    /**
     * create url of contact for template edit link
     * @param array #contactData contact data 
     */
    public function createCompleteUrl($contactData) {
        return $contactData['url'] . ($contactData['counter'] > 0 ? '_' . $contactData['counter'] : null);
    }

}
