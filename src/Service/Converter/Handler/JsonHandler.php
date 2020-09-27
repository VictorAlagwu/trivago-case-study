<?php

namespace App\Service\Converter\Handler;

use App\Domain\Dto\Request\Converter\ParseFileDto;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class JsonHandler
{
    private string $inputDirectory;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->inputDirectory = $parameterBag->get('kernel.project_dir') . 'var/in/';
    }

    public function parseFile(string $path): ParseFileDto
    {
        try {
            $file = file_get_contents($path, FILE_USE_INCLUDE_PATH);
            $jsonObj = json_decode($file);

            return new ParseFileDto(true, $jsonObj, 'File parsed');
        } catch (Exception $e) {
            return new ParseFileDto(false, [], $e->getMessage());
        }
    }

    public function validateData($data)
    {
    }

    public function convertToCsv($data)
    {
    }

    protected static function cryptoRandSecure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) {
            return $min;
        } // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);
        return $min + $rnd;
    }


    public static function getToken($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[self::cryptoRandSecure(0, $max - 1)];
        }

        return $token;
    }
}