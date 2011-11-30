<?php

namespace Tobion\TropaionBundle\Util;

abstract class SignedIntToSortableStringConverter
{

	/**
	 * This method is not usefull when the value has decimal places or 
	 * when the value has more digits than the specified number of chars used to encode it as string.
	 */
	public static function convertValue($value, $chars = 3)
	{
		if ($value < 0) {
			return '0' . str_pad((string) (pow(10, $chars) + $value), $chars, '0', STR_PAD_LEFT);
		}
		else {
			return '1' . str_pad((string) $value, $chars, '0', STR_PAD_LEFT);
		}
	}


	public static function convertArray($array, $chars = 3)
	{
		$str = '';

		foreach ($array as $key => $value) {
			$str .= self::convertValue($value, is_array($chars) ? $chars[$key] : $chars);
		}

		return $str;
	}

}
