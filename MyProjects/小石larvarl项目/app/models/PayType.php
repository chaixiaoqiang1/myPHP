<?php
    class PayType extends Eloquent {

    protected $table = 'pay_types';

    protected $primaryKey = 'type_id';

    protected function getDateFormat()
    {
        return 'U';
    }

	public function scopeGetPayType($query, $pay_type_id)
	{
		return $query->where('pay_type_id', $pay_type_id)
			->where('platform_id', Session::get('platform_id'));
	}

	public function scopeCurrentPlatform($query)
	{
		return $query->where('platform_id', Session::get('platform_id'));
	}

}