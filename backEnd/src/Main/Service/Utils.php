<?php

namespace Main\Service;


class Utils
{
    protected static $inst;
    private $epsilon;

    /**
     * @param int $length
     * @param string $alphabet
     * @return string
     * @throws \Exception
     */
    public function generateCode(
        int $length,
        string $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz'
    ): string {
        $base_count = strlen($alphabet);
        $string = random_bytes($length);

        for ($i = 0, $ic = strlen($string); $i < $ic; $i++) {
            $string[$i] = $alphabet[ord($string[$i]) % $base_count];
        }

        return $string;
    }

    /**
     * @param string|int|float $num
     * @param string $decPoint
     * @return string
     */
    public function numToRealStr($num, string $decPoint = ','): string
    {
        $num = (float)$num;
        $numStr = mb_strtolower((string)$num);
        $eIndex = mb_strpos($numStr, 'e');
        if ($eIndex !== false) {
            $dotIndex = mb_strpos($numStr, '.');
            $decCount = (int)mb_substr($numStr, $eIndex + 2);
            $dotEDiffIndex = $eIndex - $dotIndex;
            if (!($dotEDiffIndex === 2 && $numStr[$dotIndex + 1] === '0')) {
                $decCount += $dotEDiffIndex - 1;
            }
            return number_format($num, $decCount, $decPoint, '');
        } else {
            return str_replace('.', $decPoint, $num);
        }
    }

    /**
     * @param string|int|float $amount
     * @return int
     */
    public function getDecimalsCount($amount): int
    {
        $amount = $this->numToRealStr($amount);
        return strlen(substr(strrchr($amount, ","), 1));
    }

    /**
     * @param string|int|float $number
     * @return float|int
     */
    public function parseNumber($number)
    {
        $number = ''.$number;
        $number = str_replace(',', '.', $number);
        if (strpos($number, ',') !== false || strpos($number, '.') !== false) {
            return (float) $number;
        } else {
            return (int) $number;
        }
    }

    public function getArrById(array $array): array
    {
        $resultArray = [];
        if (empty($array)) {
            return $resultArray;
        }
        foreach ($array as $val) {
            if (is_object($val)) {
                if (is_callable([$val, 'getId'])) {
                    $key = $val->getId();
                } else {
                    $key = $val->id;
                }
            } else {
                $key = $val['id'];
            }
            $resultArray[$key] = $val;
        }
        return $resultArray;
    }

    public function getIdsFromArr(array $array): array
    {
        $ids = [];
        foreach ($array as $val) {
            if (is_object($val)) {
                if (is_callable([$val, 'getId'])) {
                    $key = $val->getId();
                } else {
                    $key = $val->id;
                }
            } else {
                $key = $val['id'];
            }
            $ids[] = $key;
        }
        return $ids;
    }

    public function floor(float $sum, int $prec): float
    {
        $factor = pow(10, $prec);
        return floor($sum * $factor) / $factor;
    }

    public function ceil(float $sum, int $prec): float
    {
        $factor = pow(10, $prec);
        return ceil($sum * $factor) / $factor;
    }

    public function getEpsilon(): float
    {
        if (is_null($this->epsilon)) {
            $a1 = (float)1.0;
            $a2 = (float)2.0;
            $macheps = $a1;
            do {
                $macheps /= $a2;
            } while ((float)($a1 + ($macheps / $a2)) != $a1);
            $this->epsilon = $macheps;
        }
        return $this->epsilon;
    }

    public function compareFloat(float $amount1, float $amount2, int $precision = null): int
    {
        if (is_null($precision)) {
            $precision = max($this->getDecimalsCount($amount1), $this->getDecimalsCount($amount2));
        }
        $accuracy = pow(10, - $precision) - $this->getEpsilon();
        if (abs($amount1 - $amount2) > $accuracy) {
            if ($amount1 > $amount2) {
                return 1;
            } elseif ($amount1 < $amount2) {
                return -1;
            }
        }
        return 0;
    }
}
