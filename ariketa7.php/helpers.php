<?php
// NANaren letra egiaztatzeko eta zuzena zein den lortzeko funtzioa
function balidatuNAN($nan) {
    $nan = strtoupper(trim($nan));
    
    // Expressio erregularra: 8 zenbaki eta letra bat
    if (!preg_match('/^[0-9]{8}[A-Z]$/', $nan)) {
        return [
            'baliozkoa' => false,
            'mezua' => 'NANak 8 zenbaki eta letra bat izan behar ditu (Adibidez: 12345678Z).'
        ];
    }

    $zenbakia = substr($nan, 0, 8);
    $sartutakoLetra = substr($nan, -1);

    // 23 Moduluko algoritmoa
    $letragunea = "TRWAGMYFPDXBNJZSQVHLCKE";
    $hondarra = $zenbakia % 23;
    $letraZuzena = $letragunea[$hondarra];

    if ($sartutakoLetra === $letraZuzena) {
        return [
            'baliozkoa' => true,
            'letraZuzena' => $letraZuzena
        ];
    } else {
        return [
            'baliozkoa' => false,
            'mezua' => "NANaren letra ez da zuzena. Zure zenbakiarentzat letra zuzena <strong>$letraZuzena</strong> da."
        ];
    }
}

// Emaila egiaztatzeko funtzioa (@ duela eta formatu egokia duela)
function balidatuEmaila($email) {
    $email = trim($email);
    if (empty($email)) {
        return 'Email eremua ezin da hutsik egon.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strpos($email, '@') === false) {
        return 'Emaila ez da baliozkoa (ez dauka @ edo formatu egokia).';
    }
    return true;
}

// Data balidatu eta itzulketa-data kalkulatzeko funtzioa (10 egun beranduago)
function kalkulatuItzulketaData($data) {
    if (empty($data)) {
        return false;
    }
    // Data baten 10 egun gehitzea
    $dataObj = new DateTime($data);
    $dataObj->modify('+10 days');
    return $dataObj->format('Y-m-d');
}
?>