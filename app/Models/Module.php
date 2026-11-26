<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Module
 * 
 * @property int $mod_ID
 * @property string $mod_title
 * @property int $mod_course
 * 
 * @property Course $course
 * @property Collection|Assignment[] $assignments
 * @property Collection|Lecture[] $lectures
 *
 * @package App\Models
 */
class Module extends Model
{
	protected $table = 'module';
	protected $primaryKey = 'mod_ID';
	public $timestamps = false;

	protected $casts = [
		'mod_course' => 'int'
	];

	protected $fillable = [
		'mod_title',
		'mod_course'
	];

    public function course()
    {
        return $this->belongsTo(Course::class, 'mod_course', 'crs_ID');
    }

	public function assignments()
	{
		return $this->hasMany(Assignment::class, 'sgn_module');
	}

	public function lectures()
	{
		return $this->hasMany(Lecture::class, 'lct_module');
	}
}
