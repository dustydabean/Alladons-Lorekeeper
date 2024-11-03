<?php

/*
    |--------------------------------------------------------------------------
    | Activity Modules
    |--------------------------------------------------------------------------
    |
    | This is a list of modules that can be attached to an activity.
    | Add modules here to make them selectable in the admin panel.
    | The key must be unique, but names do not have to be.
    |
    | Modules are expected to be fairly simple, focused types of user interactivity.
    */

return [] +
  // Turn in any items from a defined set in an inventory picker and get rewards back out
  // Item Turn-in can be limited by count, item, and item categories
  ['recycle' => 'Recycle'] +
  // Simplified prompt explanation and submissions page in one
  // Activity page for a user would include standard activity description, default rewards description, textarea for the prompt (with optional template), optional additional rewards
  ['prompt' => 'Prompt'] +
  // Own all items from a set of defined items, displayed on the activity, and get a reward once you have them all
  // Requires:: Collection Extension
  (class_exists('\App\Models\Collection\Collection') ? ['collection' => 'Collection'] : []) +
  // in-page craft between 1-6 selected recipes, highlighting them a little better compared to the overall crafting extension
  (class_exists('\App\Models\Recipe\Recipe') ? ['crafting' => 'Crafting'] : []);

//********* Future Module Ideas ***********
// Turn in Currency / nothing, limited by time, to get randomized rewards
// 'wishingWell => 'Wishing Well'
