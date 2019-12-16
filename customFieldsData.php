<?php
    $fieldTypesPos = [
        'intNeg' => 0,
        'intPos' => 1,
        'intNegPos' => 2,
        'floatNeg' => 3,
        'floatPos' => 4,
        'string' => 5,
        'selection' => 6,
        'mselection' => 7
    ];

    $fieldTypes = [
        'intNeg' => 'Negative Ganzzahl',
        'intPos' => 'Positive Ganzzahl',
        'intNegPos' => 'Negative oder positive Ganzzahl',
        'floatNeg' => 'Negative oder positive Gleitkommazahl',
        'floatPos' => 'Positive Gleitkommazahl',
        'string' => 'Zeichenfolge < 512 Zeichen',
        'selection' => 'Einzelauswahl (Single-Dropdown)',
        'mselection' => 'Mehrfachauswahl (Multi-Dropdown)'
    ];

    $fieldLimits = [
        'intNeg' => array(-4294967294, 0),
        'intPos' => array(0, 4294967925),
        'intNegPos' => array(-2147483638, 214747483637),
        'floatNeg' => array(-2147483638, 0),
        'floatPos' => array(0, 4294967925),
        'string' => array('\'\'', 0, 512),
        'selection' => array('\'\'', 0, 1024),
        'mselection' => array('\'\'', 0, 1024)
    ];

    $dataExamples = [
        '-1' => 'Datentyp Beispielwerte',
        'intNeg' => 'Einzelwert: -131 oder -7019 oder kleiner gleich 0',
        'intPos' => 'Einzelwert: 1 oder 291 oder 4016, größer gleich 0',
        'intNegPos' => 'Einzelwert: -10 oder -2 oder 2000 oder 4000',
        'floatNeg' => 'Einzelwert: -11.4 oder -0.032 kleiner gleich 0',
        'floatPos' => 'Einzelwert: 3.4172 oder 5.179 oder 27.4',
        'string' => 'Einzelwert: Zeichenfolge mit bis zu < 512 Zeichen',
        'selection' => 'Einzelauswahl, durch Komma getrennt: erlaubt "Neu" oder "Gebraucht" oder "Refurbished" wenn "Neu,Gebraucht,Refurbished" angegeben. Und oder numerische Werte (Ganzzahl, Gleitkommazahlen)',
        'mselection' => 'Mehrfachauswahl, durch Komma getrennt: erlaubt "Neu" und oder "Netzwerk", wenn "Neu,Netzwerk" angegeben. Und oder numerische Werte (Ganzzahl, Gleitkommanzahlen).'
    ];
?>