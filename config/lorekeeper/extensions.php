<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Extensions
    |--------------------------------------------------------------------------
    |
    | This enables/disables a selection of extensions which provide QoL and are
    | broadly applicable, but perhaps not universally, and which are contained
    | in scope enough to be readily opt-in.
    |
    | Extensions with a single value for their setting are enabled/disabled via it
    | and have no additional configuration necessary here. 0 = disabled, 1 = enabled.
    | All of the extensions here are disabled by default.
    |
    | Please refer to the readme for more information on each of these extensions.
    |
    */

    // Navbar News Notif - Juni
    'navbar_news_notif' => 1,

    /*  Visual Trait Indexes
     *
     *  Species Trait Index - Mercury
     *  Subtype Trait Index - Speedy
     *  Universal Trait Index - CHERVB
     *  All Traits Index (Kitchen Sink Index) - Speedy
     *  Trait Modals addition - Moif
     */
    'visual_trait_index'                  => [
        'enable_species_index'   => 1, // Enables the Species Trait Index
        'enable_subtype_index'   => 0, // Enables the Subtype Trait Index
        'enable_universal_index' => 1, // Enables the Universal Trait Index
        'enable_all_trait_index' => 0, // Enables the All Traits Index
        'trait_modals'           => 1, // Enables modals when you click on a trait for more info instead of linking to the traits page
    ],

    // Character Status Badges - Juni
    'character_status_badges' => 1,

    // Character TH Profile Link - Juni
    'character_TH_profile_link' => 1,

    // Design Update Voting - Mercury
    'design_update_voting' => 0,

    // Item Entry Expansion - Mercury
    'item_entry_expansion'                 => [
        'extra_fields'    => 1,
        'resale_function' => 1,
        'loot_tables'     => [
            // Adds the ability to use either rarity criteria for items or item categories with rarity criteria in loot tables. Note that disabling this does not apply retroactively.
            'enable'              => 1,
        ],
    ],

    // Group Traits By Category - Uri
    'traits_by_category'                   => 1,

    // Scroll To Top - Uri
    'scroll_to_top'                        => 1, // 1 - On, 0 - off

    // Character Reward Expansion - Uri
    'character_reward_expansion' => [
        'expanded'          => 1,
        'default_recipient' => 1, // 0 to default to the character's owner (if a user), 1 to default to the submission user.
    ],

    // MYO Image Hide/Remove - Mercury
    // Adds an option when approving MYO submissions to hide or delete the MYO placeholder image
    'remove_myo_image' => 0,

    // Auto-populate New Image Traits - Mercury
    // Automatically adds the traits present on a character's active image to the list when uploading a new image for an extant character.
    'autopopulate_image_features'          => 1,

    // Staff Rewards - Mercury
    'staff_rewards' => [
        'enabled'     => 0,
        'currency_id' => 1,
    ],

    // Organised Traits Dropdown - Draginraptor
    'organised_traits_dropdown'            => 1,

    // Previous & Next buttons on Character pages - Speedy
    // Adds buttons linking to the previous character as well as the next character on all character pages.
    'previous_and_next_characters' => [
        'display' => 1,
        'reverse' => 1, // By default, 0 has the lower number on the 'Next' side and the higher number on the 'Previous' side, reflecting the default masterlist order. Setting this to 1 reverses this.
    ],

    // Aliases on Userpage - Speedy
    'aliases_on_userpage' => 0, // By default, does not display the aliases on userpage. Enable to add a small arrow to display these underneath the primary alias.

    // Show All Recent Submissions - Speedy
    'show_all_recent_submissions' => [
        'enable' => 1,
        'links'  => [
            'sidebar'      => 1,      // By default, ON, and will display in the sidebar.
            'indexbutton'  => 1, // By default, ON, and will display a button on the index.
        ],
        'section_on_front' => 1, // By default, does not display on the front page. Enable to add a block above the footer.
    ],

    // collapsible admin sidebar - Newt
    'collapsible_admin_sidebar' => 0,

    // use gravatar for user avatars - Newt
    'use_gravatar' => 1,

    // Use ReCaptcha to check new user registrations - Mercury
    // Requires site key and secret be set in your .env file!
    'use_recaptcha' => 1,

    // Show Small Badges on the User's Characters/MYO Slots Page
    // Indicating Trading Status (and Gift Art & Gift Writing Status)
    'badges_on_user_character_page' => 1,

    // Allow users to return a pending design update to drafts, for instance if they make a mistake. - Uri
    'design_return_to_draft' => 1,

    // Multiple Subtypes - Newt
    'exclusionary_search'    => 0, // If enabled, searching for multiple subtypes will only return results that have all of the subtypes specified. If disabled, it will return results that have any of the subtypes specified.
    'multiple_subtype_limit' => 10, // The maximum number of subtypes a character can have.

    // TinyMCE Code Editor - Moif
    'tinymce_code_editor'   => 1, // If enabled, uses the more advanced code editor instead of TinyMCE's default.
];
