<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Course
 * 
 * @property int $crs_ID
 * @property string $crs_title
 * @property string $crs_description
 * 
 * @property Enrollment|null $enrollment
 * @property Forum|null $forum
 * @property Collection|Module[] $modules
 *
 * @package App\Models
 */
class Course extends Model
{
	protected $table = 'course';
	protected $primaryKey = 'crs_ID';
	public $timestamps = false;

	protected $fillable = [
		'crs_title',
		'crs_description'
	];

	public function enrollment()
	{
		return $this->hasOne(Enrollment::class, 'enr_ID');
	}

	public function forum()
	{
		return $this->hasOne(Forum::class, 'frm_ID');
	}

	public function modules()
	{
		return $this->hasMany(Module::class, 'mod_course');
	}

	public function users()
	{
    return $this->belongsToMany(
        User::class,
        'enrollment',
        'enr_course',
        'enr_user',
        'crs_ID',
        'id'
    )->withPivot('enr_ID', 'enr_date');
}
}
