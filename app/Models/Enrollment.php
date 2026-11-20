<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Enrollment
 * 
 * @property int $enr_ID
 * @property int $enr_user
 * @property int $enr_course
 * @property Carbon $enr_date
 * 
 * @property User $user
 * @property Course $course
 *
 * @package App\Models
 */
class Enrollment extends Model
{
	protected $table = 'enrollment';
	protected $primaryKey = 'enr_ID';
	public $timestamps = false;

	protected $casts = [
		'enr_user' => 'int',
		'enr_course' => 'int',
		'enr_date' => 'datetime'
	];

	protected $fillable = [
		'enr_user',
		'enr_course',
		'enr_date'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'enr_user');
	}

	public function course()
	{
		return $this->belongsTo(Course::class, 'enr_ID');
	}
}
