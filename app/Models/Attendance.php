<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Attendance
 * 
 * @property int $atd_ID
 * @property int $atd_student
 * @property Carbon $atd_enteredAt
 * @property int $atd_lecture
 * 
 * @property Lecture $lecture
 * @property User $user
 *
 * @package App\Models
 */
class Attendance extends Model
{
	protected $table = 'attendance';
	protected $primaryKey = 'atd_ID';
	public $timestamps = false;

	protected $casts = [
		'atd_student' => 'int',
		'atd_enteredAt' => 'datetime',
		'atd_lecture' => 'int'
	];

	protected $fillable = [
		'atd_student',
		'atd_enteredAt',
		'atd_lecture'
	];

	public function lecture()
	{
		return $this->belongsTo(Lecture::class, 'atd_lecture');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'atd_student');
	}
}
