<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * 
 * @property int $rl_ID
 * @property string $rl_name
 * 
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Role extends Model
{
	protected $table = 'role';
	protected $primaryKey = 'rl_ID';
	public $timestamps = false;

	protected $fillable = [
		'rl_name'
	];

	public function users()
	{
		return $this->hasMany(User::class, 'rl_ID');
	}
}
