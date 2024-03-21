<?php

return [

    // how many generations deep should the lineage be?
    // 0 = character only, lineage tab hidden
    // 1 = character, parents
    // 2 = character, parents, grandparents
    // 3 = character, parents, grandparents, great-grandparents
    // and so on
    // not larger numbers may begin to display oddly
    // recommended to keep at 3 or lower
    'tab_lineage_depth' => 2, // recommended to keep at 2 or lower
    'lineage_depth' => 3,

    // same as above, but for descendants
    // 0 = character only, descendants tab hidden
    // 1 = character, children
    // 2 = character, children, grandchildren
    // 3 = character, children, grandchildren, great-grandchildren
    // i recommend keeping at 2 to avoid some odd display issues
    'descendant_depth' => 2,

    // should the lineage tab show children
    'show_children_on_tab' => true,
];
