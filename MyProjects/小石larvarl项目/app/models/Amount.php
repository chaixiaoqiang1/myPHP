<?php
class Amount extends Eloquent {

    protected $table = 'pay_amount';

    protected $primaryKey = 'amount_id';

    protected function getDateFormat()
    {
        return 'U';
    }
}