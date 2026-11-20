<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Forum
 * 
 * @property int $frm_ID
 * @property string $frm_title
 * @property string $frm_desc
 * @property int $frm_course
 * 
 * @property Course $course
 * @property Collection|Publication[] $publications
 *
 * @package App\Models
 */
class Forum extends Model
{
	protected $table = 'forum';
	protected $primaryKey = 'frm_ID';
	public $timestamps = false;

	protected $casts = [
		'frm_course' => 'int'
	];

	protected $fillable = [
		'frm_title',
		'frm_desc',
		'frm_course'
	];

	public function course()
	{
		return $this->belongsTo(Course::class, 'frm_ID');
	}

	public function publications()
	{
		return $this->hasMany(Publication::class, 'pbl_forum');
	}
}
