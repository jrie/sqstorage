<?php
    $fieldTypesPos = [
        'intNeg' => 0,
        'intPos' => 1,
        'intNegPos' => 2,
        'floatNeg' => 3,
        'floatPos' => 4,
        'string' => 5,
        'selection' => 6,
        'mselection' => 7,
        'qrcode' => 8
    ];

    $fieldTypes = [
        'intNeg' => gettext('Negative Ganzzahl'),
        'intPos' => gettext('Positive Ganzzahl'),
        'intNegPos' => gettext('Negative oder positive Ganzzahl'),
        'floatNeg' => gettext('Negative oder positive Gleitkommazahl'),
        'floatPos' => gettext('Positive Gleitkommazahl'),
        'string' => gettext('Zeichenfolge < 512 Zeichen'),
        'selection' => gettext('Einzelauswahl (Single-Dropdown)'),
        'mselection' => gettext('Mehrfachauswahl (Multi-Dropdown)'),
        'qrcode' => gettext('QR-Code Feld'),
    ];

    $fieldLimits = [
        'intNeg' => array(-4294967294, 0),
        'intPos' => array(0, 4294967925),
        'intNegPos' => array(-2147483638, 214747483637),
        'floatNeg' => array(-2147483638, 0),
        'floatPos' => array(0, 4294967925),
        'string' => array('\'\'', 0, 256),
        'selection' => array('\'\'', 0, 1280),
        'mselection' => array('\'\'', 0, 1280),
        'qrcode' => array('\'\'', 0, 256)
    ];

    $fieldConverts = [
        'intNeg' => 'int',
        'intPos' => 'int',
        'intNegPos' => 'int',
        'floatNeg' => 'float',
        'floatPos' => 'float',
        'string' => 'string',
        'selection' => 'string',
        'mselection' => 'string',
        'qrcode' => 'string'
    ];

    $dataExamples = [
        '-1' => gettext('Datentyp Beispielwerte'),
        'intNeg' => gettext('Einzelwert: -131 oder -7019 oder kleiner gleich 0'),
        'intPos' => gettext('Einzelwert: 1 oder 291 oder 4016, größer gleich 0'),
        'intNegPos' => gettext('Einzelwert: -10 oder -2 oder 2000 oder 4000'),
        'floatNeg' => gettext('Einzelwert: -11.4 oder -0.032 kleiner gleich 0'),
        'floatPos' => gettext('Einzelwert: 3.4172 oder 5.179 oder 27.4'),
        'string' => gettext('Einzelwert: Zeichenfolge mit bis zu < 256 Zeichen'),
        'selection' => gettext('Einzelauswahl, durch Semikolon getrennt: erlaubt "Neu" oder "Gebraucht" oder "Refurbished" wenn "Neu;Gebraucht;Refurbished" angegeben. Und oder numerische Werte (Ganzzahl, Gleitkommazahlen)'),
        'mselection' => gettext('Mehrfachauswahl, durch Semikolon getrennt: erlaubt "Neu" und oder "Netzwerk", wenn "Neu;Netzwerk" angegeben. Und oder numerische Werte (Ganzzahl, Gleitkommanzahlen).'),
        'qrcode' => gettext('Einzelauswahl: Name des verknüpften Feldes auf dem der QR-Code basiert')
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
        )
    ];