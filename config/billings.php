<?php

return [
    "records_per_generate" => 80,
    "types" => [
        "simcard" => \App\Models\mobile\Simcard::class,
        "datasim" => \App\Models\mobile\DataSim::class,
        "rakuten" => \App\Models\RakutenDataSim::class,
        "rakuten_call" => \App\Models\RakutenCallSim::class,
        'simcard_b' => \App\Models\mobile\SimcardB::class
    ],
];
