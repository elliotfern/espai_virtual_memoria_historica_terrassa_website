<?php

// tipus 1: anys guerra civil 1936 - 1939
// tipus 2: anys guerra civil i dictadura 1936 - 1979
function convertirDataFormatMysql(string $fecha, int $tipus): ?string
{
    $dt = DateTime::createFromFormat('!j/n/Y', $fecha);

    if (!($dt instanceof DateTime)) {
        return null;
    }

    $errors = DateTime::getLastErrors();
    if ($errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
        return null;
    }

    $any = (int)$dt->format('Y');

    if ($tipus === 1 && ($any < 1936 || $any > 1939)) {
        return null;
    }

    if ($tipus === 2 && ($any < 1936 || $any > 1979)) {
        return null;
    }

    if ($tipus === 3 && ($any < 1800 || $any > 2025)) {
        return null;
    }

    return $dt->format('Y-m-d');
}
