<?php

namespace AlephTools\DDD\Common\Infrastructure;

class Sanitizer {

    public static function sanitizeName(?string $name): string
    {
        return $name === null ? '' : preg_replace('/[[:cntrl:]]/', '', trim($name));
    }

    public static function sanitizeEmail(?string $email): string
    {
        return $email === null ? '' : trim($email);
    }

    public static function sanitizePhone(?string $phone): string
    {
        if ($phone === null) {
            return '';
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 10) {
            $phone = '7' . $phone;
        }
        return $phone;
    }

    public static function sanitizeZip(?string $zip): string
    {
        return $zip === null ? '' : preg_replace('/[^0-9-]/', '', $zip);
    }
}