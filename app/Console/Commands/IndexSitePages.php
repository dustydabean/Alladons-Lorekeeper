<?php

namespace App\Console\Commands;

use DB;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

use App\Models\Character\Character;
use App\Models\SitePage;
use App\Models\IndexSiteData;

class IndexSitePages extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index-new-search-pages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexes all site content for the ajax search.';

    /**
     * Create a new command instance.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {

        if (Schema::hasTable('site_temp_index')) {

            //A. ------------------ Clear the temp table for extra insurance
            DB::table('site_temp_index')->truncate();
            
            //B. ------------------ Index types of content
            //1. FIND ALL CHARACTERS TO INDEX
            $existingCharacters = DB::table('characters')->pluck('id');
            $characters = Character::visible()->myo(0)->whereNotIn('slug', $existingCharacters)->get();
            foreach ($characters as $character) {
                DB::table('site_temp_index')->insert([
                    // input all neccessary fields
                    'id' => $character->id,
                    'title' => $character->slug.': '.$character->name,
                    'type'  => 'Character',
                    'identifier' => $character->slug,
                    'description' => $character->name,
                ]);
            }

            //2. FIND ALL PAGES TO INDEX
            $pages = DB::table('site_pages')->get();
            foreach ($pages as $page) {
                DB::table('site_temp_index')->insert([
                    // input all neccessary fields
                    'id' => $page->id,
                    'title' => $page->title,
                    'type'  => 'Page',
                    'identifier' => $page->key,
                    'description' => substr_replace(strip_tags($page->text), '...', 100),
                ]);
            }

            //3. FIND ALL USERS TO INDEX
            $users = DB::table('users')->get();
            foreach ($users as $user) {
                DB::table('site_temp_index')->insert([
                    // input all neccessary fields
                    'id' => $user->id,
                    'title' => $user->name,
                    'type'  => 'User',
                    'identifier' => $user->name,
                    'description' => NULL,
                ]);
            }

            //4. FIND ALL ITEMS TO INDEX
            $items = DB::table('items')->get();
            foreach ($items as $item) {
                DB::table('site_temp_index')->insert([
                    // input all neccessary fields
                    'id' => $item->id,
                    'title' => $item->name,
                    'type'  => 'Item',
                    'identifier' => $item->id,
                    'description' => substr_replace(strip_tags($item->description), '...', 100),
                ]);
            }

            //5. FIND ALL PROMPTS TO INDEX
            $prompts = DB::table('prompts')->get();
            foreach ($prompts as $prompt) {
                DB::table('site_temp_index')->insert([
                    // input all neccessary fields
                    'id' => $prompt->id,
                    'title' => $prompt->name,
                    'type'  => 'Prompt',
                    'identifier' => $prompt->id,
                    'description' => substr_replace(strip_tags($prompt->description), '...', 100),
                ]);
            }

            //6. FIND ALL SHOPS TO INDEX
            $shops = DB::table('shops')->get();
            foreach ($shops as $shop) {
                DB::table('site_temp_index')->insert([
                    // input all neccessary fields
                    'id' => $shop->id,
                    'title' => $shop->name,
                    'type'  => 'Shop',
                    'identifier' => $shop->id,
                    'description' => substr_replace(strip_tags($shop->description), '...', 100),
                ]);
            }

            /* IMPORTANT
            * If you would like to add your own areas to search you can easily add them here! Just copy one of the sections above and replace the data as needed.
            * note: identifier field should always match the URL parameter. (Characters use slug, pages use key, etc)
            * Ensure if you use content for the description it does NOT go over 1024 characters.
            * ID is not an incremental field.
            */

            // ------------------ C. Duplicate data to new table
            DB::table('site_index')->truncate();
            $index = DB::table('site_temp_index')->get();
            foreach ($index as $row) {
                DB::table('site_index')->insert([
                    // input all neccessary fields
                    'id' => $row->id,
                    'title' => $row->title,
                    'type'  => $row->type,
                    'identifier' => $row->identifier,
                    'description' => $row->description,
                ]);
            }

            // ------------------ D. Dump the Temp Table
            DB::table('site_temp_index')->truncate();

        }
    }
}