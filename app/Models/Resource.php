<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** 
 * Resource
 * Handles resource table
 * 
 * @category Model
 * @author Smruti Ranjan
 */
class Resource extends Model
{
	// The table associated with this model.
	protected $table = 'resource';

	/**
	 * To select all data
	 *
	 * @param  void
	 * @return object $resources
	*/
	public static function retrieveData ()
	{
		$resources = Resource::all();
		return $resources;
	}
}
