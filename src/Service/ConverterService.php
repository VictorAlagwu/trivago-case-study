<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ConverterService implements IConverterService
{
    private string $inputDirectory;
    private string $outputDirectory;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->inputDirectory = $parameterBag->get('kernel.project_dir') . '/var/in/';
        $this->outputDirectory = $parameterBag->get('kernel.project_dir') . '/var/out/';
    }

    public function getFile($fileLocation)
    {
        try {
            $path = $this->inputDirectory . $fileLocation;

            $file = file_get_contents($path, FILE_USE_INCLUDE_PATH);


            $jsonObj = json_decode($file);
            $fp = fopen($this->outputDirectory . 'file' . $this->getToken(5) . '.csv', 'w+');

            foreach ($jsonObj as $field) {
                fputcsv($fp, (array) $field);
            }
            fclose($fp);
            return 'Hello World';
        } catch (Exception $e) {
            return $e->getMessage();
        }
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


    public function validateData($data)
    {
    }

    public function convertToCsv($data)
    {
    }
}
