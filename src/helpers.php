<?php

if ( ! function_exists('hash_password')) {

    /**
     * Захешировать пароль.
     *
     * @param $password
     * @param $realPass
     * @return string
     */
    function hash_password($password, $realPass)
    {
        $itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $cryptPass = '*0';
        if (substr($realPass, 0, 2) == $cryptPass)
            $cryptPass = '*1';

        $id = substr($realPass, 0, 3);
        # We use "$P$", phpBB3 uses "$H$" for the same thing
        if ($id != '$P$' && $id != '$H$')
            return $cryptPass = crypt($password, $realPass);

        $count_log2 = strpos($itoa64, $realPass[3]);
        if ($count_log2 < 7 || $count_log2 > 30)
            return $cryptPass = crypt($password, $realPass);

        $count = 1 << $count_log2;

        $salt = substr($realPass, 4, 8);
        if (strlen($salt) != 8)
            return $cryptPass = crypt($password, $realPass);

        $hash = md5($salt . $password, TRUE);
        do {
            $hash = md5($hash . $password, TRUE);
        } while (--$count);

        $cryptPass = substr($realPass, 0, 12);

        $encode64 = '';
        $i = 0;
        do {
            $value = ord($hash[$i++]);
            $encode64 .= $itoa64[$value & 0x3f];
            if ($i < 16)
                $value |= ord($hash[$i]) << 8;
            $encode64 .= $itoa64[($value >> 6) & 0x3f];
            if ($i++ >= 16)
                break;
            if ($i < 16)
                $value |= ord($hash[$i]) << 16;
            $encode64 .= $itoa64[($value >> 12) & 0x3f];
            if ($i++ >= 16)
                break;
            $encode64 .= $itoa64[($value >> 18) & 0x3f];
        } while ($i < 16);

        $cryptPass .= $encode64;

        if ($cryptPass[0] == '*')
            $cryptPass = crypt($password, $realPass);

        return $cryptPass;
    }
}

if ( ! function_exists('uuidFromString')) {

    /**
     * Получить uuid v4 из строки.
     *
     * @param $string
     * @return string
     */
    function uuidFromString($string)
    {
        $val = md5($string, true);
        $byte = array_values(unpack('C16', $val));

        $tLo = ($byte[0] << 24) | ($byte[1] << 16) | ($byte[2] << 8) | $byte[3];
        $tMi = ($byte[4] << 8) | $byte[5];
        $tHi = ($byte[6] << 8) | $byte[7];
        $csLo = $byte[9];
        $csHi = $byte[8] & 0x3f | (1 << 7);

        if (pack('L', 0x6162797A) == pack('N', 0x6162797A)) {
            $tLo = (($tLo & 0x000000ff) << 24) | (($tLo & 0x0000ff00) << 8) | (($tLo & 0x00ff0000) >> 8) | (($tLo & 0xff000000) >> 24);
            $tMi = (($tMi & 0x00ff) << 8) | (($tMi & 0xff00) >> 8);
            $tHi = (($tHi & 0x00ff) << 8) | (($tHi & 0xff00) >> 8);
        }

        $tHi &= 0x0fff;
        $tHi |= (3 << 12);

        $uuid = sprintf(
            '%08x-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x',
            $tLo, $tMi, $tHi, $csHi, $csLo,
            $byte[10], $byte[11], $byte[12], $byte[13], $byte[14], $byte[15]
        );
        return $uuid;
    }
}

if ( ! function_exists('generateStr')) {

    /**
     * Сгенерировать особую строку.
     *
     * @return null|string
     */
    function generateStr() {
        $chars="0123456789abcdef";
        $max=64;
        $size=StrLen($chars)-1;
        $password=null;
        while($max--)
            $password.=$chars[rand(0,$size)];

        return $password;
    }
}

if ( ! function_exists('check_files')) {

    /**
     * Проверить файлы клиента на соответствие.
     *
     * @param $path
     * @return string
     */
    function checkfiles($path) {
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
        $massive = "";
        foreach($objects as $name => $object) {
            $basename = basename($name);
            $isdir = is_dir($name);
            if ($basename!="." and $basename!=".." and !$isdir){
                $str = str_replace('clients/', "", str_replace($basename, "", $name));
                $massive = $massive.$str.$basename.':>'.md5_file($name).':>'.filesize($name).'<:>';
            }
        }
        return $massive;
    }
}

if ( ! function_exists('hashc')) {

    /**
     * 
     *
     * @param $client
     * @return string
     */
    function hashc($client) {
        $client_path = config("mas.path.clients");
        $hash_md5 = str_replace("\\", "/",$this->checkfiles($client_path . '/' .$client.'/bin/').$this->checkfiles($client_path . '/'.$client.'/mods/').$this->checkfiles($client_path . '/' .$client.'/coremods/').$this->checkfiles($client_path . '/' .$client.'/natives/')).'<::>'.$client.'/bin<:b:>'.$client.'/mods<:b:>'.$client.'/coremods<:b:>'.$client.'/natives<:b:>';
        return $hash_md5;
    }
}