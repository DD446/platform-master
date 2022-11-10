<?php
/**
 * User: fabio
 * Date: 03.05.19
 * Time: 22:09
 */

namespace App\Classes;

use Laminas\Feed\Writer\Exception\InvalidArgumentException;

class FeedWriter extends \Laminas\Feed\Writer\Feed {

    /**
     * Email address for person responsible for technical issues
     * Ignored if atom is used
     *
     * @param  string $webmaster
     * @param  string $name
     */
    public function setWebmaster(string $webmaster, ?string $name = null): void
    {
        $validator = new \Laminas\Validator\EmailAddress();

        if (!$validator->isValid($webmaster)) {
            // email is invalid
            $messages = $validator->getMessages();
            throw new InvalidArgumentException(array_pop($messages));
        }

        if (!is_null($name)) {
            $webmaster .= " ({$name})";
        }

        $this->_data['webmaster'] = $webmaster;
    }

    public function getWebmaster()
    {
        return $this->_data['webmaster'];
    }
}
