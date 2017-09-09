<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	protected $primaryKey = 'user_id';

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	protected function getDateFormat()
	{
		return 'U';
	}
	
	public function department()
	{
		return $this->belongsTo('Department');
	}

	public function scopeOrganization($query)
	{
		return $query->where('organization_id', Auth::user()->organization_id);
	}

	public function isAdminStr()
	{
		return $this->is_admin ? 'Yes' : 'No';
	}

	public function permissions()
	{
		if (!$this->permissions) {
			return array();	
		}
		return explode(',', $this->permissions);
	}

	public function games()
	{
		if (!$this->games) {
			return array();
		}
		return explode(',', $this->games);
	}

	public static function boot()
	{
		parent::boot();
		static::created(function($user) {
			$log = new EastBlueLog;
			$log->log_key = 'users';
			$log->desc = '[' . Auth::user()->username . ']创建用户[' . $user->username . ']于' . $user->created_at;
			$log->user_id = Auth::user()->user_id;
			$log->new_value = $user->toJson();
			$log->save();
		});

		static::updated(function($user) {
			$log = new EastBlueLog;
			$log->log_key = 'users';
			$old_user = $user->getOriginal();
			$msg = '[' . Auth::user()->username . ']修改用户[' . $user->username . ']'; 
			if ($user->password != $old_user['password']) {
				$msg .= '的密码于' . $user->updated_at;
				$diff_old = array(
					'password' => $old_user['password'],
				);
				$diff_new = array(
					'password' => $user->password,
				);	
			} else {
			   	if ($user->permissions != $old_user['permissions']) {
					$msg .= '的权限于' . $user->updated_at;
				} else {
					$msg .= '的个人资料于'. $user->updated_at;
				}	
				$new_user = $user->toArray();
				unset($old_user['password']);
				$intersectArr = array_intersect($new_user, $old_user);
				$diff_old = array_diff($old_user, $intersectArr);
				$diff_new = array_diff($new_user, $intersectArr);
			}
			$log->desc = $msg;
			$log->user_id = Auth::user()->user_id;
			$log->old_value = json_encode($diff_old);
			$log->new_value = json_encode($diff_new);
			$log->save();
		});
	}

}