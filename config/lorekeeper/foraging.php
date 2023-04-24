<?php

return [

    // minutes to wait for forage to complete
    'forage_time' => 1,

    // future stuff...
    'use_characters' => 0,
    // characters will use stamina on user_stamina table, unless you have a stat for that
    // in which case search for CHARACTER_STAMINA_DECREMENT comment in ForageService.php to edit

    'use_foraging_stamina' => 1, // if this is set to 1, by default the stamina column on
                                // user_foraging is used, otherwise it will use whatever you preset
                                // search for USER_STAMINA_DECREMENT comment in ForageService.php to edit   
];