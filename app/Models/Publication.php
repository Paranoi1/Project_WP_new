<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Publication
 * 
 * @property int $pbl_ID
 * @property string $pbl_title
 * @property string $pbl_content
 * @property int $pbl_forum
 * @property int $pbl_publisher
 * 
 * @property Forum $forum
 * @property User $user
 *
 * @package App\Models
 */
class Publication extends Model
{
	protected $table = 'publication';
	protected $primaryKey = 'pbl_ID';
	public $timestamps = false;

	protected $casts = [
		'pbl_forum' => 'int',
		'pbl_publisher' => 'int'
	];

	protected $fillable = [
		'pbl_title',
		'pbl_content',
		'pbl_forum',
		'pbl_publisher'
	];

	public function forum()
	{
		return $this->belongsTo(Forum::class, 'pbl_forum');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'pbl_publisher');
	}
}
