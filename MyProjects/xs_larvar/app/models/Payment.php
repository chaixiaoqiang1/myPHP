<?php
    class Payment extends Eloquent {

    protected $table = 'payments';

    protected $primaryKey = 'pay_id';

    protected function getDateFormat()
    {
        return 'U';
    }

	public function scopeGetPayments($query, $pay_type_id, $method_id, $zone='')
	{
		$query = $query->where('pay_type_id', $pay_type_id)
			->where('method_id', $method_id)
			->where('platform_id', Session::get('platform_id'));
		if($zone && (int)$zone >0){
			$query = $query->where('zone', $zone);
		}
		return $query;
	}

    public function scopeGetPlatformPayments($query){
        $query = $query->where('platform_id',Session::get('platform_id'));
        return $query;
    }
}