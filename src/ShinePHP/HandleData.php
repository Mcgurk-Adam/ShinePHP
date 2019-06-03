<?php
declare(strict_types=1);

namespace ShinePHP;

/**
 * HandleData is a class to make a cleaner, simpler interface for working input data
 * HandleData is an interface built for PHP developers to reduce all of the code repeating in sanitizing and validating data
 * 
 * EXAMPLE USAGE:
 * $DataInput = new HandleData($_POST);
 * $email = $DataInput->email($DataInput['email']);
 * echo HandleData::output($email);
 *
 * @package HandleData
 * @author Adam McGurk <amcgurk@shinesolar.com>
 * @access public
 * @see https://github.com/ShineSolar/ShinePHP
 * 
 */

final class HandleData {

	/**
	 *
	 * Makes it easy to validate an email address AND gives you control over the domain if you want
	 *
	 * @access public
	 *
	 * @param string $email this is the string you want validated as an email address
	 * @param OPTIONAL string $optional_domain only pass a paramter to this if you only want email addresses belonging to certain domains to be validated
	 * 
	 * @return mixed valid email address or false on failure
	 *
	 */

	public static function email(string $email, string $optional_domain = '') {

		// setting the original variables
		$sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);
		$domain = substr($sanitized_email, strpos($sanitized_email, "@") + 1);

		// Checking if it is actually a valid email after the sanitization
		if (filter_var($sanitized_email, FILTER_VALIDATE_EMAIL) !== false) {

			// doing the domain check
			if ($optional_domain !== '' && $domain !== $optional_domain) {
				return false;
			} else {
				return $sanitized_email;
			}

		} else {
			return false;
		}

	}

	/**
	 *
	 * Sanitize and validate a United States Phone Number, optionally including the "1" area code
	 *
	 * @access public
	 *
	 * @param string $phone string you want validated as a phone number
	 * @param OPTIONAL bool $include_country_code decide if you want a leading one in it or not
	 * 
	 * @return mixed validated phone or false on failure
	 *
	 */

	public static function american_phone(string $phone, bool $include_country_code = false) {

		$stripped_phone = preg_replace('/[^0-9]/', '', self::string($phone, false));

		// checking to see if it just matches basic phone validation anyways
		if (preg_match('/^1?[2-9]{1}[0-9]{2}[0-9]{3}[0-9]{4}$/', $stripped_phone) !== 1) {

			// return false on failure
			return false;

		} else {

			// checking the country code flag
			if ($include_country_code) { return (substr($stripped_phone,0,1) === '1' ? $stripped_phone : '1'.$stripped_phone); } 
			else { return (substr($stripped_phone,0,1) === '1' ? substr($stripped_phone,1) : $stripped_phone); }

		}

	}

	/**
	 *
	 * Sanitize a string
	 *
	 * @access public
	 *
	 * @param string $string this is the string you want sanitized
	 * @param OPTIONAL bool $canBeEmpty set to false when the string cannot be empty
	 * 
	 * @return mixed the sanitized string if successful, or false if not
	 *
	 */

	public static function string($string, bool $can_be_empty = true) {

		// sanitizing the actual string
		$sanitized_string = filter_var($string, FILTER_SANITIZE_STRING);

		// running the check
		return ($can_be_empty === false && $sanitized_string === '' ? false : $sanitized_string);

	}

	/**
	 *
	 * Sanitize and validate url
	 * TODO add domain validation
	 *
	 * @access public
	 *
	 * @param string $url string you want validated as URL
	 * 
	 * @return mixed validated URL on success or false
	 *
	 */

	public static function url(string $url) {

		// sanitizing the url
		$sanitized_url = filter_var(self::string($url, false), FILTER_SANITIZE_URL);

		// returning the values
		return (filter_var($sanitized_url, FILTER_VALIDATE_URL) ? $sanitized_url : false);

	}

	/**
	 *
	 * Return a boolean based on any data you provide
	 *
	 * @access public
	 *
	 * @param mixed $variable_to_make_boolean variable you want returned as a boolean
	 * 
	 * @return bool
	 *
	 */

	public static function boolean($variable_to_make_boolean) : bool { return filter_var($variable_to_make_boolean, FILTER_VALIDATE_BOOLEAN); }

	/**
	 *
	 * Validate and return an ip address
	 *
	 * @access public
	 *
	 * @param string $ip variable you want validated as an ip address
	 * 
	 * @return string of the ip address on success or false on failure
	 *
	 */

	public static function ip(string $ip) { return filter_var($ip, FILTER_VALIDATE_IP); }

	/**
	 *
	 * Validate and return a float value
	 *
	 * @access public
	 *
	 * @param mixed $number variable you want validated as a float
	 * @param OPTIONAL bool $can_be_zero if set to true, the return can be 0.00
	 * 
	 * @return float on success, if not valid float, return false
	 *
	 */

	public static function float($number, bool $can_be_zero = false) {

		// sanitizing and validating the input as a float
		$sanitized_number = filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$validated_float = filter_var($sanitized_number, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);

		// Doing the float checks and throwing exceptions or returning the valid float
		if ($validated_float === false) {
			return false;
		} else if (!$can_be_zero && $validated_float === 0.00) {
			return false;
		} else {
			return $validated_float;
		}

	}

	/**
	 *
	 * Validate and return an integer variable
	 *
	 * GOTCHA: If you pass a float, like 7.50, it will just drop the decimal and trailing zeroes. So 7.50 will be returned as 75
	 * 
	 * @access public
	 *
	 * @param mixed $number variable you want validated as an integer
	 * @param OPTIONAL bool $can_be_zero if set to true, the return can be 0
	 * 
	 * @return int on success, if not valid int, return false
	 *
	 */

	public static function integer($number, bool $can_be_zero = false) {

		// sanitizing and validating the input as an integer
		$sanitized_number = filter_var($number, FILTER_SANITIZE_NUMBER_INT);
		$validated_int = filter_var($sanitized_number, FILTER_VALIDATE_INT);

		// Doing the integer checks and throwing exceptions or returning the valid integer
		if ($validated_int === false) {
			return false;
		} else if (!$can_be_zero && $validated_int === 0) {
			return false;
		} else {
			return $validated_int;
		}

	}

}
