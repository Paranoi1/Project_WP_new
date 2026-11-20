<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Submission
 * 
 * @property int $sbm_ID
 * @property string $sbm_content
 * @property int $sbm_StudentAssignment
 * @property float $sbm_score
 * @property string $sbm_feedback
 * @property Carbon $sbm_date
 * @property int $sbm_reviewedBy
 * 
 * @property Studentassignment $studentassignment
 * @property User $user
 *
 * @package App\Models
 */
class Submission extends Model
{
	protected $table = 'submission';
	protected $primaryKey = 'sbm_ID';
	public $timestamps = false;

	protected $casts = [
		'sbm_StudentAssignment' => 'int',
		'sbm_score' => 'float',
		'sbm_date' => 'datetime',
		'sbm_reviewedBy' => 'int'
	];

	protected $fillable = [
		'sbm_content',
		'sbm_StudentAssignment',
		'sbm_score',
		'sbm_feedback',
		'sbm_date',
		'sbm_reviewedBy'
	];

	public function studentassignment()
	{
		return $this->belongsTo(Studentassignment::class, 'sbm_StudentAssignment');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'sbm_reviewedBy');
	}
}
