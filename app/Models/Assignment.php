<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Assignment
 * 
 * @property int $sgn_ID
 * @property string $sgn_desc
 * @property Carbon $sgn_deadline
 * @property int $sgn_maxScore
 * @property int $sgn_module
 * @property int $sgn_publisher
 * 
 * @property Module $module
 * @property User $user
 * @property Collection|Studentassignment[] $studentassignments
 *
 * @package App\Models
 */
class Assignment extends Model
{
	protected $table = 'assignment';
	protected $primaryKey = 'sgn_ID';
	public $timestamps = false;

	protected $casts = [
		'sgn_deadline' => 'datetime',
		'sgn_maxScore' => 'int',
		'sgn_module' => 'int',
		'sgn_publisher' => 'int'
	];

	protected $fillable = [
		'sgn_desc',
		'sgn_deadline',
		'sgn_maxScore',
		'sgn_module',
		'sgn_publisher'
	];

	public function module()
	{
		return $this->belongsTo(Module::class, 'sgn_module');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'sgn_publisher');
	}

	public function studentassignments()
	{
		return $this->hasMany(Studentassignment::class, 'stSgn_assignment');
	}
}
