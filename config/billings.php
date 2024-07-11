<?php

return [
    "records_per_generate" => 2000,
    "types" => [
        "simcard" => \App\Models\mobile\Simcard::class,
        "datasim" => \App\Models\mobile\DataSim::class,
        "rakuten" => \App\Models\RakutenDataSim::class
    ],
    // "call_log_types" => [
    //     "sms" => [
    //         "per_unit" =>
    //     ]
    // ]
];