<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Lecture
 * 
 * @property int $lct_ID
 * @property string $lct_title
 * @property string $lct_description
 * @property Carbon $lct_date
 * @property int $lct_lecturer
 * @property int $lct_module
 * 
 * @property Module $module
 * @property User $user
 * @property Collection|Attendance[] $attendances
 *
 * @package App\Models
 */
class Lecture extends Model
{
	protected $table = 'lecture';
	protected $primaryKey = 'lct_ID';
	public $timestamps = false;

	protected $casts = [
		'lct_date' => 'datetime',
		'lct_lecturer' => 'int',
		'lct_module' => 'int'
	];

	protected $fillable = [
		'lct_title',
		'lct_description',
		'lct_date',
		'lct_lecturer',
		'lct_module'
	];

	public function module()
	{
		return $this->belongsTo(Module::class, 'lct_module');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'lct_lecturer');
	}

	public function attendances()
	{
		return $this->hasMany(Attendance::class, 'atd_lecture');
	}
}
