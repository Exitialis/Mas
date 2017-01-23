<?php

namespace Exitialis\Mas\Managers\Hash\Drivers;

class DLEHash implements HashContract
{
    /**
     * Захэшировать строку.
     *
     * @param $value
     * @return string
     */
    public function hash($value)
    {
        return md5(md5($value));
    }
    /**
     * Узнать длину пароля.
     *
     * @param $binary_string
     * @return int
     */	
	public function strlen_8bit($binary_string) {
		if (function_exists('mb_strlen')) {
			return mb_strlen($binary_string, '8bit');
		}
		return strlen($binary_string);
	}
    /**
     * Захэшировать строку.
     *
     * @param $binary_string
	 * @param $start
	 * @param $length
     * @return string
     */		
	public function substr_8bit($binary_string, $start, $length) {
		if (function_exists('mb_substr')) {
			return mb_substr($binary_string, $start, $length, '8bit');
		}
		return substr($binary_string, $start, $length);
	}
	
    /**
     * Получение информации о том как сгенерирован пароль(md5 или Blowfish).
     *
     * @param $hash
     * @return bool
     */
	public function passwordGetInfo($hash) {
		$return = true;
		if ($this->substr_8bit($hash, 0, 4) == '$2y$' && $this->strlen_8bit($hash) == 60) {
			$return = false;
		}
		return $return;
	}
	
	/**
     * Проверить значение на совпадение с хешем. Метод Blowfish
     *
     * @param $password
     * @param $hash
     * @return bool
     */
	public function passwordVerify($password, $hash) {
		$ret = crypt($password, $hash);

		if (!is_string($ret) || $this->strlen_8bit($ret) != $this->strlen_8bit($hash) || $this->strlen_8bit($ret) <= 13) {
			return false;
		}
		$status = 0;
		for ($i = 0; $i < $this->strlen_8bit($ret); $i++) {
			$status |= (ord($ret[$i]) ^ ord($hash[$i]));
		}
		return $status === 0;
	}
    /**
     * Проверить значение на совпадение с хешем.
     *
     * @param $value
     * @param $hash
     * @return bool
     */
    public function checkValue($value, $hash)
    {
		if($this->passwordGetInfo($hash)){
			 return $this->hash($value) === $hash;
		}else{
			return $this->passwordVerify($value, $hash);
		}
       
    }

}
