<?php

return [
    // Number between 0-100. Percentage chance to inherit traits from both parents upon generating offspring. Set 100 for always. Set 0 for never.
    'trait_inheritance' => 100,
    // 0: Any character can be paired. 1: Only male/female characters can be paired.
    'sex_restriction' => 0,
    // 0: Disabled aka characters do not need a set sex. 1: Enabled aka characters must have a set sex
    'force_sex' => 0,
    // 0: Disabled, do not roll sex. 1-100: Chance to generate a male offspring. Must total 100 with the pairing_female_percentage setting.
    'offspring_male_percentage' => 0,
    // 0: Disabled, do not roll sex. 1-100: Chance to generate a male offspring. Must total 100 with the pairing_male_percentage setting.
    'offspring_female_percentage' => 0,
    // 0: Disabled. Number of days to wait between pairing a character.
    'cooldown' => 0,
    // 0: rarity is random between rarity options from features, with boosts affecting character rarity
    // 1: rarity is chosen by highest rarity of inherited traits (vanilla) (recommended)
    'rarity_inheritance' => 1,

    /////////////////////////////////////
    //  COLOURS
    // 
    //  Recommended to leave these settings alone unless you know what you're doing
    /////////////////////////////////////

    // 0: Disabled, 1: Enabled
    'colours' => 0,

    // if colours from parents should be inherited, entirely visual, no actual colour checks occur,
    // just a colour palette generated from parents and displayed on the pairing slots
    'inherit_colours' => 1, // 0: Disabled, 1: Enabled

    // 5 recommended for base colours, accents are *very* hard to get into the actual palette due to the way the palette is generated
    'colour_count' => 5,

    // minimum amount of difference between colours, higher = more difference, but potentially less colour variety
    'colour_distance' => 75,

    // colour palettes for checks, myos only get one palette, so this is mostly for when making the pairing to see potential offspring colours
    'colour_palette_count' => 3,

    // 0: colours are blocks like on image pages, 1: colours are presented as a gradient (this is only for pairings)
    'blend_colours' => 1,

    // 0: characters do not automatically generate image colours on image upload, 1: characters automatically generate image colours on image upload
    // only for non-myos, myos only generate colours if from a pairing
    'auto_generate_colours' => 1,

    // 0: no alternative palettes, 1: alternative palettes are generated for pairing myos (minor adjustments to the main palette)
    'alternative_palettes' => 1,
];
