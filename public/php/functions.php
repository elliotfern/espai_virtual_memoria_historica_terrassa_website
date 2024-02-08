<?php

require_once(APP_ROOT . APP_DEV . '/vendor/autoload.php');
use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Cargar variables de entorno desde .env
$dotenv = Dotenv::createImmutable(APP_ROOT . APP_DEV . '/');
$dotenv->load();

$jwtSecret = $_ENV['TOKEN'];

/**
 * Formats a price with the given currency symbol, decimal separator, and thousands separator.
 *
 * @param float $price The price to format.
 * @return string The formatted price with currency symbol.
 */
function wc_price( $price ) {
    $currency_symbol = '€'; // replace with your currency symbol
    $decimal_separator = ','; // replace with your decimal separator
    $thousands_separator = '.'; // replace with your thousands separator
    
    $price = number_format( $price, 2, $decimal_separator, $thousands_separator );
    
    return $price . $currency_symbol;
}

function data_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  function validateURLExists($url) {
    $headers = @get_headers($url);
    if ($headers && strpos($headers[0], '200') !== false) {
        // El código de respuesta es 200, la URL existe
        return true;
    } else {
        // El código de respuesta no es 200 o la URL no es accesible
        return false;
    }
}

/**
 * Validates a form field.
 *
 * @param string $fieldName The name of the field to validate.
 * @param bool $isMoney Flag to indicate if the field should be treated as money.
 * @param bool $isInt Flag to indicate if the field should be treated as an integer.
 * @return mixed The validated field value.
 */
function validateFormField($fieldName, $isMoney = false, $isInt = false, $optional = false, $isEmail = false, $isWeb = false) {
    $hasError = false; // Bandera que indica si hay un error
    $fieldValue = data_input($fieldName); // Obtener el valor del campo
    
    // Validar si el campo está vacío y no es opcional
    if (empty($fieldValue) && !$optional) {
        $hasError = true;
        $fieldValue = NULL;
    } else {
        $hasError = false;
  
        if ($isMoney) {
            // Check decimal separator and convert to dot if necessary
            if (strpos($fieldValue, ',') !== false && strpos($fieldValue, '.') === false) {
                $fieldValue = str_replace(',', '.', $fieldValue); // Replace comma with dot
            }
  
            // Check if the field value is numeric and has two decimal places
            if (!is_numeric($fieldValue) || round($fieldValue, 2) != $fieldValue) {
                $hasError = true; // Invalid field value is considered an error
            }
        } elseif ($isInt) {
            // Check if the field value is a valid integer
            if (!filter_var($fieldValue, FILTER_VALIDATE_INT)) {
                $hasError = true; // Invalid field value is considered an error
            }
        } elseif ($isEmail) {
            // Validar campo de correo electrónico
            if (!filter_var($fieldValue, FILTER_VALIDATE_EMAIL)) {
                $hasError = true;
                $fieldValue = NULL;
            } else {
                $domain = substr(strrchr($fieldValue, "@"), 1);
                if (!checkdnsrr($domain, "MX")) {
                    $hasError = true;
                    $fieldValue = NULL;
                }
            }
        } elseif ($isWeb) {
           // Validar campo de dirección web (URL)
            if (!filter_var($fieldValue, FILTER_VALIDATE_URL)) {
                $hasError = true;
                $fieldValue = NULL;
            } else {
                if (!validateURLExists($fieldValue)) {
                    $hasError = true;
                    $fieldValue = NULL;
                }
            }
        } 
    }

    return array(
        'value' => $fieldValue,
        'hasError' => $hasError
    );
}


function verificarToken($token) {
    $jwtSecretKey = obtenerClaveSecretaPorKid("key_api");

    
    try {
        // Decodificar el token
        $decoded = JWT::decode($token, new Key($jwtSecretKey, 'HS256'));
        return true;  // El token es válido
    } catch (Throwable $e) {
        echo 'Error al decodificar el token: ' . $e->getMessage();
        return false;
    }
}

function obtenerClaveSecretaPorKid($kid) {
    global $jwtSecret;

    $clavesSecretas = array(
        "key_api" => $jwtSecret,
    );

    if (isset($clavesSecretas[$kid])) {
        return $clavesSecretas[$kid];
    } else {
        return null; // "kid" no encontrado, devuelve null o maneja el caso según tus necesidades
    }
}

?>