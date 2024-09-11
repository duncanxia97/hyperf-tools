<?php
/**
 * Created by XJ.
 * Date: 2021/12/8
 */

namespace Hyperf\Database\Model;

use Hyperf\Database\Model\Model as BaseModel;

/**
 * @mixin BaseModel
 */
abstract class Model
{

	/**
	 * Begin querying the model.
	 *
	 * @return \Hyperf\Database\Model\Builder | static
	 */
	public static function query()
	{
		return (new static())->newQuery();
	}

}