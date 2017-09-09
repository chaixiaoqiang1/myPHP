<?php

class Rate extends Eloquent {

    protected $table = 'exchange_rates';

    protected $primaryKey = 'rate_id';

    protected function getDateFormat()
    {
        return 'U';
    }

}