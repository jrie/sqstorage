<?php
    $fieldTypesPos = [
        'intNeg' => 0,
        'intPos' => 1,
        'intNegPos' => 2,
        'floatNeg' => 3,
        'floatPos' => 4,
        'floatNegPos' => 5,
        'string' => 6,
        'selection' => 7,
        'mselection' => 8,
        'qrcode' => 9,
        'datetime' => 10
    ];

    $fieldTypes = [
        'intNeg' => gettext('Negative Ganzzahl'),
        'intPos' => gettext('Positive Ganzzahl'),
        'intNegPos' => gettext('Negative oder positive Ganzzahl'),
        'floatNeg' => gettext('Negative Gleitkommazahl'),
        'floatPos' => gettext('Positive Gleitkommazahl'),
        'floatNegPos' => gettext('Negative oder positive Gleitkommazahl'),
        'string' => gettext('Zeichenfolge < 512 Zeichen'),
        'selection' => gettext('Einzelauswahl (Single-Dropdown)'),
        'mselection' => gettext('Mehrfachauswahl (Multi-Dropdown)'),
        'qrcode' => gettext('QR-Code Feld'),
        'datetime' => gettext('Datum und Uhrzeit'),
    ];

    $fieldLimits = [
        'intNeg' => array(-9223372036854775808, 0),
        'intPos' => array(0, 18446744073709551615),
        'intNegPos' => array(-9223372036854775808, 9223372036854775807),
        'floatNeg' => array(-21474836380000000.0, 0),
        'floatPos' => array(0, 18446744073710000000.0),
        'floatNegPos' => array(-222507385850720140.0, 179769313486231570.0),
        'string' => array('\'\'', 0, 256),
        'selection' => array('\'\'', 0, 1280),
        'mselection' => array('\'\'', 0, 1280),
        'qrcode' => array('\'\'', 0, 256),
        'datetime' => array('\'\'', 0, 0)
    ];

    $fieldConverts = [
        'intNeg' => 'int',
        'intPos' => 'int',
        'intNegPos' => 'int',
        'floatNeg' => 'float',
        'floatPos' => 'float',
        'floatNegPos' => 'float',
        'string' => 'string',
        'selection' => 'string',
        'mselection' => 'string',
        'qrcode' => 'string',
        'datetime' => 'datetime'
    ];

    $dataExamples = [
        '-1' => gettext('Datentyp Beispielwerte'),
        'intNeg' => gettext('Einzelwert: -131 oder -7019 oder kleiner gleich 0'),
        'intPos' => gettext('Einzelwert: 1 oder 291 oder 4016, größer gleich 0'),
        'intNegPos' => gettext('Einzelwert: -10 oder -2 oder 2000 oder 4000'),
        'floatNeg' => gettext('Einzelwert: -11.4 oder -0.032 kleiner gleich 0'),
        'floatPos' => gettext('Einzelwert: 3.4172 oder 5.179 oder 27.4'),
        'floatNegPos' => gettext('Einzelwert: -3.4172 oder 5.179'),
        'string' => gettext('Einzelwert: Zeichenfolge mit bis zu < 256 Zeichen'),
        'selection' => gettext('Einzelauswahl, durch Semikolon getrennt: erlaubt "Neu" oder "Gebraucht" oder "Refurbished" wenn "Neu;Gebraucht;Refurbished" angegeben. Und oder numerische Werte (Ganzzahl, Gleitkommazahlen)'),
        'mselection' => gettext('Mehrfachauswahl, durch Semikolon getrennt: erlaubt "Neu" und oder "Netzwerk", wenn "Neu;Netzwerk" angegeben. Und oder numerische Werte (Ganzzahl, Gleitkommanzahlen).'),
        'qrcode' => gettext('Einzelauswahl: Name des verknüpften Feldes auf dem der QR-Code basiert'),
        'datetime' => gettext('Datum und Uhrzeit: Datum und Zeitangabe')
    ];

    $qrBaseFields = [
        'label' => array(
            'id' => 0,
            'type' => 'base',
            'text' => gettext('Bezeichnung')
        ),
        'serialnumber' => array(
            'id' => 1,
            'type' => 'base',
            'text' => gettext('Seriennummer/Artikelnummer')
        ),
        'storage' => array(
            'id' => 2,
            'type' => 'extend',
            'text' => gettext('Lagerplatz')
        ),
        'checkinout' => array(
            'id' => 3,
            'type' => 'base',
            'text' => gettext('Check in/check out')
        ),
        'increaseone' => array(
            'id' => 4,
            'type' => 'base',
            'text' => gettext('Anzahl um 1 erhöhen')
        ),
        'decreaseone' => array(
            'id' => 5,
            'type' => 'base',
            'text' => gettext('Anzahl um 1 senken')
        )
    ];
