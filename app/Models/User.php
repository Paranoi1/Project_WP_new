<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $rl_ID
 * 
 * @property Role $role
 * @property Assignment|null $assignment
 * @property Collection|Attendance[] $attendances
 * @property Enrollment|null $enrollment
 * @property Collection|Lecture[] $lectures
 * @property Collection|Publication[] $publications
 * @property Collection|Studentassignment[] $studentassignments
 * @property Collection|Submission[] $submissions
 *
 * @package App\Models
 */


class User extends Authenticatable
{
	protected $table = 'users';

	protected $casts = [
		'email_verified_at' => 'datetime',
		'rl_ID' => 'int'
	];

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'name',
		'email',
		'email_verified_at',
		'password',
		'remember_token',
		'rl_ID'
	];

	public function role()
	{
		return $this->belongsTo(Role::class, 'rl_ID');
	}

	public function assignment()
	{
		return $this->hasOne(Assignment::class, 'sgn_publisher');
	}

	public function attendances()
	{
		return $this->hasMany(Attendance::class, 'atd_student');
	}

	public function enrollment()
	{
		return $this->hasOne(Enrollment::class, 'enr_user');
	}

	public function lectures()
	{
		return $this->hasMany(Lecture::class, 'lct_lecturer');
	}

	public function publications()
	{
		return $this->hasMany(Publication::class, 'pbl_publisher');
	}

	public function studentassignments()
	{
		return $this->hasMany(Studentassignment::class, 'stSgn_student');
	}

	public function submissions()
	{
		return $this->hasMany(Submission::class, 'sbm_reviewedBy');
	}

		
	public function courses()
	{
 	return $this->belongsToMany(
        	Course::class,
        	'enrollment',
        	'enr_user',
        	'enr_course', 
        	'id',         
        	'crs_ID'      
    	)->withPivot('enr_ID', 'enr_date');
	}
}
