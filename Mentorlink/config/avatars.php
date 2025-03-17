<?php
return [
    'avatars' => array_map(
        fn($num) => "avatar/avatar " . str_pad($num, 2, '0', STR_PAD_LEFT) . ".png",
        range(1, 38)
    ),
];
