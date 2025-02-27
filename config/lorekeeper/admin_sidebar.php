<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Sidebar Links
    |--------------------------------------------------------------------------
    |
    | Admin panel sidebar links.
    | Add links here to have them show up in the admin panel.
    | Users that do not have the listed power will not be able to
    | view the links in that section.
    |
    */

    'Admin'      => [
        'power' => 'admin',
        'links' => [
            [
                'name' => 'User Ranks',
                'url'  => 'admin/users/ranks',
            ],
            [
                'name' => 'Admin Logs',
                'url'  => 'admin/admin-logs',
            ],
            [
                'name' => 'Staff Reward Settings',
                'url'  => 'admin/staff-reward-settings',
            ],
        ],
    ],
    'Reports'    => [
        'power' => 'manage_reports',
        'links' => [
            [
                'name' => 'Report Queue',
                'url'  => 'admin/reports/pending',
            ],
        ],
    ],
    'Site'       => [
        'power' => 'edit_pages',
        'links' => [
            [
                'name' => 'News',
                'url'  => 'admin/news',
            ],
            [
                'name' => 'Dev Logs',
                'url'  => 'admin/devlogs',
            ],
        ],
    ],
    'Sales' => [
        'power' => 'manage_sales',
        'links' => [
            [
                'name' => 'Sales',
                'url'  => 'admin/sales',
            ],
        ],
    ],
    'Pages'       => [
        'power' => 'edit_pages',
        'links' => [
            [
                'name' => 'Pages',
                'url'  => 'admin/pages',
            ],
        ],
    ],
    'Users'      => [
        'power' => 'edit_user_info',
        'links' => [
            [
                'name' => 'User Index',
                'url'  => 'admin/users',
            ],
            [
                'name' => 'Invitation Keys',
                'url'  => 'admin/invitations',
            ],
        ],
    ],
    'Queues'     => [
        'power' => 'manage_submissions',
        'links' => [
            [
                'name' => 'Gallery Submissions',
                'url'  => 'admin/gallery/submissions',
            ],
            [
                'name' => 'Gallery Currency Awards',
                'url'  => 'admin/gallery/currency',
            ],
            [
                'name' => 'Prompt Submissions',
                'url'  => 'admin/submissions',
            ],
            [
                'name' => 'Claim Submissions',
                'url'  => 'admin/claims',
            ],
        ],
    ],
    'Grants'     => [
        'power' => 'edit_inventories',
        'links' => [
            [
                'name' => 'Currency Grants',
                'url'  => 'admin/grants/user-currency',
            ],
            [
                'name' => 'Item Grants',
                'url'  => 'admin/grants/items',
            ],
            [
                'name' => 'Pet Grants',
                'url'  => 'admin/grants/pets',
            ],
            [
                'name' => 'Recipe Grants',
                'url'  => 'admin/grants/recipes',
            ],
            [
                'name' => 'Event Settings',
                'url' => 'admin/event-settings',
            ],
        ],
    ],
    'Masterlist' => [
        'power' => 'manage_characters',
        'links' => [
            [
                'name' => 'Create Character',
                'url'  => 'admin/masterlist/create-character',
            ],
            [
                'name' => 'Create MYO Slot',
                'url'  => 'admin/masterlist/create-myo',
            ],
            [
                'name' => 'Character Transfers',
                'url'  => 'admin/masterlist/transfers/incoming',
            ],
            [
                'name' => 'Character Trades',
                'url'  => 'admin/masterlist/trades/incoming',
            ],
            [
                'name' => 'Design Updates',
                'url'  => 'admin/design-approvals/pending',
            ],
            [
                'name' => 'MYO Approvals',
                'url'  => 'admin/myo-approvals/pending',
            ],
        ],
    ],
    'Data'       => [
        'power' => 'edit_data',
        'links' => [
            [
                'name' => 'Galleries',
                'url'  => 'admin/data/galleries',
            ],
            [
                'name' => 'Species Categories',
                'url'  => 'admin/data/character-categories',
            ],
            [
                'name' => 'Sub Masterlists',
                'url'  => 'admin/data/sublists',
            ],
            [
                'name' => 'Mutation Points',
                'url'  => 'admin/data/rarities',
            ],
            [
                'name' => 'Species',
                'url'  => 'admin/data/species',
            ],
            [
                'name' => 'Species Contents',
                'url'  => 'admin/data/subtypes',
            ],
            [
                'name' => 'Mutations',
                'url'  => 'admin/data/traits',
            ],
            [
                'name' => 'Shops',
                'url'  => 'admin/data/shops',
            ],
            [
                'name' => 'Dailies',
                'url'  => 'admin/data/dailies',
            ],
            [
                'name' => 'Activities',
                'url'  => 'admin/data/activities',
            ],
            [
                'name' => 'Currencies',
                'url'  => 'admin/data/currencies',
            ],
            [
                'name' => 'Prompts',
                'url'  => 'admin/data/prompts',
            ],
            [
                'name' => 'Loot Tables',
                'url'  => 'admin/data/loot-tables',
            ],
            [
                'name' => 'Items',
                'url'  => 'admin/data/items',
            ],
            [
                'name' => 'Pets',
                'url'  => 'admin/data/pets',
            ],
            [
                'name' => 'Collections',
                'url'  => 'admin/data/collections',
            ],
            [
                'name' => 'FAQ',
                'url'  => 'admin/data/faq',
            ],
            [
                'name' => 'Character Generations',
                'url'  => 'admin/data/character-generations',
            ],
            [
                'name' => 'Character Pedigrees',
                'url'  => 'admin/data/character-pedigrees',
            ],
            [
                'name' => 'Scavenger Hunts',
                'url'  => 'admin/data/hunts',
            ],
            [
                'name' => 'Criteria Rewards',
                'url'  => 'admin/data/criteria',
            ],
            [
                'name' => 'Recipes',
                'url'  => 'admin/data/recipes',
            ],
        ],
    ],
    'Raffles'    => [
        'power' => 'manage_raffles',
        'links' => [
            [
                'name' => 'Raffles',
                'url'  => 'admin/raffles',
            ],
        ],
    ],
    'Pairings'   => [
        'power' => 'edit_data',
        'links' => [
            [
                'name' => 'Pairing Roller',
                'url'  => 'admin/pairings/roller',
            ],
        ],
    ],
    'Settings'   => [
        'power' => 'edit_site_settings',
        'links' => [
            [
                'name' => 'Site Settings',
                'url'  => 'admin/settings',
            ],
            [
                'name' => 'Site Images',
                'url'  => 'admin/images',
            ],
            [
                'name' => 'File Manager',
                'url'  => 'admin/files',
            ],
            [
                'name' => 'Theme Manager',
                'url'  => 'admin/themes',
            ],
            [
                'name' => 'Log Viewer',
                'url'  => 'admin/logs',
            ],
        ],
    ],
];
