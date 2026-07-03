<?php
// Training family palette — shared between admin (family dropdown) and the
// public training-calendar.php (per-card colour swatches + calendar legend).
// Keys are the values stored in training_sessions.family; values are the
// hex colours rendered next to each training and on the calendar.

$TRAINING_FAMILIES = [
    'Quality'         => '#99CC00', // ISO 9001
    'Auditing'        => '#455A64', // ISO 19011
    'Food Safety'     => '#00B5C5', // ISO 22000 / HACCP
    'Health & Safety' => '#8C66DE', // ISO 45001
    'Environmental'   => '#F24B4B', // ISO 14001
    'Hazmat'          => '#FFC000', // hazard / warning
    'Risk'            => '#809CD0', // ISO 31000
    'Wellness'        => '#E85D75', // wellness
    'Agriculture'     => '#2E7D32', // Global GAP
];
