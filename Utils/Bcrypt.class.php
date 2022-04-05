<?php
namespace Utils;

use Exception;

class Bcrypt
{
    private $rounds;
    private ?string $randomState = null;

    /**
     * @throws Exception
     */
    public function __construct($rounds = 12)
    {
        if (CRYPT_BLOWFISH != 1) {
            throw new Exception("bcrypt not supported in this installation. See https://php.net/crypt");
        }
        $this->rounds = $rounds;
    }

    public function hash($input)
    {
        $hash = crypt($input, $this->getSalt());
        if (strlen($hash) > 13) {
            return $hash;
        }
        return false;
    }

    private function getSalt(): string
    {
        $salt = sprintf('$2y$%02d$', $this->rounds);
        $bytes = $this->getRandomBytes();
        $salt .= $this->encodeBytes($bytes);
        return $salt;
    }

    private function getRandomBytes()
    {
        $bytes = '';
        if (function_exists('openssl_random_pseudo_bytes') && (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')) {
            $bytes = openssl_random_pseudo_bytes(16);
        }
        if ($bytes === '' && is_readable('/dev/urandom') && ($hRand = @fopen('/dev/urandom', 'rb')) !== false) {
            $bytes = fread($hRand, 16);
            fclose($hRand);
        }
        if (strlen($bytes) < 16) {
            $bytes = '';
            if ($this->randomState === null) {
                $this->randomState = microtime();
                if (function_exists('getmypid')) {
                    $this->randomState .= getmypid();
                }
            }
            for ($i = 0; $i < 16; $i += 16) {
                $this->randomState = md5(microtime() . $this->randomState);
                $bytes .= (PHP_VERSION >= '5')?md5($this->randomState, true):pack('H*', md5($this->randomState));
            }
            $bytes = substr($bytes, 0, 16);
        }
        return $bytes;
    }

    private function encodeBytes($input): string
    {
        $itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $output = '';
        $i = 0;
        do {
            $c1 = ord($input[$i++]);
            $output .= $itoa64[$c1 >> 2];
            $c1 = ($c1 & 0x03) << 4;
            if ($i >= 16) {
                $output .= $itoa64[$c1];
                break;
            }
            $c2 = ord($input[$i++]);
            $c1 |= $c2 >> 4;
            $output .= $itoa64[$c1];
            $c1 = ($c2 & 0x0f) << 2;
            $c2 = ord($input[$i++]);
            $c1 |= $c2 >> 6;
            $output .= $itoa64[$c1];
            $output .= $itoa64[$c2 & 0x3f];
        } while (1);
        return $output;
    }

    public function verify($input, $existingHash): bool
    {
        $hash = crypt($input, $existingHash);
        return $hash === $existingHash;
    }
}