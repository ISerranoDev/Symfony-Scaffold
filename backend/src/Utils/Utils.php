<?php

namespace App\Utils;

use Exception;
use LengthException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Utils
{

    public static function uploadFile(File $file, string $directory): array
    {
        $extension = $file->getExtension();
        $fileName  = Utils::secure_random_string(40) . '.' . $extension;
        $fullPath = $directory.'/'.$fileName;
        $file->move('../storage/' . $directory, $fileName);
        return [
            'fileName' => $fileName,
            'extension' => $extension,
            'path' => $fullPath
        ];
    }

    public static function loadFileUri(string $path): string
    {
        $type = pathinfo(self::getStoragePath($path), PATHINFO_EXTENSION);
        $data = file_get_contents(self::getStoragePath($path));
        if($type == 'svg'){
            return 'data:image/svg+xml;utf8,' . urlencode($data);
        }
        return 'data:image/' . $type . ';base64,' . base64_encode($data);

    }

    public static function loadFileContent(string $path): string
    {
        $type = pathinfo(self::getStoragePath($path), PATHINFO_EXTENSION);
        $data = file_get_contents(self::getStoragePath($path));
        if($type == 'svg'){
            return $data;
        }
        return $data;

    }

    public static function removeFile(string $path): void
    {
        try {
            unlink(self::getStoragePath($path));
        }catch (Exception $e) {

        }

    }

    public static function getStoragePath(string $path): string
    {
        return '../storage/' . $path;
    }

    public static function secure_random_string(int $length = 16): string
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length * 2);

            if ($bytes === false) {
                throw new \LengthException('$length is not accurate, unable to generate random string');
            }

            return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
        }

        // @codeCoverageIgnoreStart
        return static::random_string($length);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Generates a string of random characters.
     *
     * @param integer $length The length of the string to
     *                                      generate
     * @param boolean $human_friendly Whether or not to make the
     *                                      string human friendly by
     *                                      removing characters that can be
     *                                      confused with other characters (
     *                                      O and 0, l and 1, etc)
     * @param boolean $include_symbols Whether or not to include
     *                                      symbols in the string. Can not
     *                                      be enabled if $human_friendly is
     *                                      true
     * @param boolean $no_duplicate_chars Whether or not to only use
     *                                      characters once in the string.
     * @return  string
     * @throws  LengthException  If $length is bigger than the available
     *                           character pool and $no_duplicate_chars is
     *                           enabled
     *
     */
    public static function random_string($length = 16, $human_friendly = true, $include_symbols = false, $no_duplicate_chars = false)
    {
        $nice_chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefhjkmnprstuvwxyz23456789';
        $all_an     = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        $symbols    = '!@#$%^&*()~_-=+{}[]|:;<>,.?/"\'\\`';
        $string     = '';

        // Determine the pool of available characters based on the given parameters
        if ($human_friendly) {
            $pool = $nice_chars;
        } else {
            $pool = $all_an;

            if ($include_symbols) {
                $pool .= $symbols;
            }
        }

        if (!$no_duplicate_chars) {
            return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
        }

        // Don't allow duplicate letters to be disabled if the length is
        // longer than the available characters
        if ($no_duplicate_chars && strlen($pool) < $length) {
            throw new \LengthException('$length exceeds the size of the pool and $no_duplicate_chars is enabled');
        }

        // Convert the pool of characters into an array of characters and
        // shuffle the array
        $pool       = str_split($pool);
        $poolLength = count($pool);
        $rand       = mt_rand(0, $poolLength - 1);

        // Generate our string
        for ($i = 0; $i < $length; $i++) {
            $string .= $pool[$rand];

            // Remove the character from the array to avoid duplicates
            array_splice($pool, $rand, 1);

            // Generate a new number
            if (($poolLength - 2 - $i) > 0) {
                $rand = mt_rand(0, $poolLength - 2 - $i);
            } else {
                $rand = 0;
            }
        }

        return $string;
    }

}