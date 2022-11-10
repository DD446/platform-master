<?php
/**
 * User: fabio
 * Date: 27.08.18
 * Time: 12:17
 */

namespace App\Classes;

class Activity {

    const REFUND            = 0;
    const FUNDS             = 1;
    const PACKAGE           = 2;
    const EXTRAS            = 3;
    const ENCODING          = 4;
    const VOUCHER           = 5;

    public function get() {

        $reflect = new \ReflectionClass(get_class($this));

        return $reflect->getConstants();
    }
}
