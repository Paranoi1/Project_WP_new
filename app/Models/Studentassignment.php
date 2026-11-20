<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Studentassignment
 * 
 * @property int $stSgn_ID
 * @property int $stSgn_student
 * @property int $stSgn_assignment
 * 
 * @property User $user
 * @property Assignment $assignment
 * @property Collection|Submission[] $submissions
 *
 * @package App\Models
 */
class Studentassignment extends Model
{
	protected $table = 'studentassignment';
	protected $primaryKey = 'stSgn_ID';
	public $timestamps = false;

	protected $casts = [
		'stSgn_student' => 'int',
		'stSgn_assignment' => 'int'
	];

	protected $fillable = [
		'stSgn_student',
		'stSgn_assignment'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'stSgn_student');
	}

	public function assignment()
	{
		return $this->belongsTo(Assignment::class, 'stSgn_assignment');
	}

	public function submissions()
	{
		return $this->hasMany(Submission::class, 'sbm_StudentAssignment');
	}
}
