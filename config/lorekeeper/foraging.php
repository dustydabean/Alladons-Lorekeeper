<?php

return [

    // minutes to wait for forage to complete
    'forage_time' => 1,

    // future stuff...
    // characters are mostly just visual for the moment.
    'use_characters' => 0,
    // characters will use stamina on user_stamina table, unless you have a stat for that
    // in which case search for CHARACTER_STAMINA_DECREMENT comment in ForageService.php to edit

    // allows FTO / Non Owner users to use NPCs, and set the NPC character category
    'npcs' => [
        'enabled'            => false,
        'category_or_rarity' => 'category',
        'code' => 'npc',
        // if this is set to true, the ids array will be used instead of the category or rarity
        'use_ids' => true,
        // array of character ids that can be used as NPCs if your site does not define NPCs as a category or rarity
        'ids' => [
            // 7, 8,
        ],
    ],

    'use_foraging_stamina' => 1, // if this is set to 1, by default the stamina column on
                                // user_foraging is used, otherwise it will use whatever you preset
                                // search for USER_STAMINA_DECREMENT comment in ForageService.php to edit
];
