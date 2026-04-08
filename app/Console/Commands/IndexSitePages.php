<?php

namespace App\Console\Commands;

use App\Models\Character\Character;
use App\Models\Feature\Feature;
use App\Models\Item\Item;
use App\Models\Prompt\Prompt;
use App\Models\Shop\Shop;
use App\Models\SiteIndex;
use App\Models\SitePage;
use App\Models\User\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class IndexSitePages extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index-search-pages {--index=?* : Comma separated items to index (characters, pages, users, items, prompts, shops, traits)} {--clear=?* : true|false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexes all site content for the ajax search.';

    /**
     * Static property to hold stopwords.
     *
     * @var string|null
     */
    protected static $stopwords;

    /**
     * Static property to hold the names of the core tables.
     *
     * @var array
     */
    protected static $core_tables;

    /**
     * Static property to hold the names/mappings of the custom tables.
     *
     * @var array
     */
    protected static $custom_tables;

    /**
     * Create a new command instance.
     */
    public function __construct() {
        parent::__construct();
        self::$stopwords = config('lorekeeper.ajax_search.stopwords');
        self::$core_tables = ['characters', 'site_pages', 'users', 'items', 'prompts', 'shops', 'features'];
        self::$custom_tables = $this->getCustomTables();
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

            $index_core = true;
            $to_index = $this->option('index');
            $clear_table = $this->option('clear');

            if (str_contains($to_index, ',')) {
                $to_index = explode(',', $to_index);
            } elseif (!str_contains($to_index, ',') && $to_index !== '?*' && !empty($to_index)) {
                $to_index = [$to_index];
            } else {
                $to_index = '*';
            }

            switch ($clear_table) {
                case 'true':
                    $clear_table = true;
                    break;
                case 'false':
                    $clear_table = false;
                case '?*':
                default:
                    $clear_table = $to_index === '*' ? true : false;
                    break;
            }

            // B-1. ------------------ Index the Core Tables
            if ($to_index === '*' || count(array_intersect(self::$core_tables, $to_index)) > 0) {
                $this->indexCoreTables($to_index);
            }

            // B-2. ------------------ Index the Custom Tables
            if ($to_index === '*' || count(array_intersect(array_keys(self::$custom_tables), $to_index)) > 0) {
                $this->indexCustomTables($to_index);
            }

            // C. ------------------ Duplicate data to new table
            if ($clear_table) {
                $this->info('Clearing the old data from site_index...');
                DB::table('site_index')->truncate();
            }
            $index = DB::table('site_temp_index')->get();
            foreach ($index as $row) {
                SiteIndex::create([
                    // input all neccessary fields
                    'id'          => $row->id,
                    'title'       => $row->title,
                    'type'        => $row->type,
                    'key'         => $row->key ?? 'Unknown',
                    'identifier'  => $row->identifier,
                    'description' => $row->description,
                    'url'         => $row->url,
                    'image_url'   => $row->image_url,
                ]);
            }

            // D. ------------------ Dump the Temp Table
            DB::table('site_temp_index')->truncate();
        }
    }

    private function indexCoreTables($indexes) {
        $tables_to_index = self::$core_tables;
        $enabled_tables = DB::table('site_settings')->where('key', 'ajax_search_core_tables')->pluck('value');
        $remove_tables = [];

        if ($indexes !== '*' && is_array($indexes)) {
            $tables_to_index = array_intersect($tables_to_index, $indexes);
        } else {
            if ($enabled_tables !== '' && is_array($enabled_tables)) {
                $enabled_tables = unserialize($enabled_tables[0]);
                $remove = array_keys($enabled_tables, 0);

                $this->info('These tables are not enabled and will not be indexed: '.implode(',', $remove));

                $tables_to_index = array_diff($tables_to_index, $remove);
            }
        }

        $this->info('Indexing these tables only: '.implode(',', $tables_to_index));

        foreach ($tables_to_index as $table_name) {
            switch ($table_name) {
                case 'characters':

                    $characters = Character::visible()->myo(0)->get();

                    $bar = $this->output->createProgressBar(count($characters));
                    $bar->start();

                    foreach ($characters as $character) {
                        DB::table('site_temp_index')->insert([
                            // input all neccessary fields
                            'id'          => $character->id,
                            'title'       => $character->slug.': '.$character->name,
                            'type'        => get_class($character),
                            'key'         => 'Character',
                            'identifier'  => $character->slug,
                            'description' => $character->name,
                            'url'         => method_exists($character, 'getUrlAttribute') ? $character->getUrlAttribute() : null,
                            'image_url'   => method_exists($character, 'getImageUrlAttribute') ? $character->getImageUrlAttribute() : null,
                        ]);
                        $bar->advance();
                    }

                    $bar->finish();
                    $this->info("\n '".count($characters)."' Characters Indexed.");

                    break;
                case 'site_pages':

                    $pages = SitePage::where('is_visible', 1)->get();
                    $bar = $this->output->createProgressBar(count($pages));
                    $bar->start();
                    foreach ($pages as $page) {
                        DB::table('site_temp_index')->insert([
                            // input all neccessary fields
                            'id'          => $page->id,
                            'title'       => $page->title,
                            'type'        => get_class($page),
                            'key'         => 'Page',
                            'identifier'  => $page->key,
                            'description' => $this->cleanDescription($page->parsed_text),
                            'url'         => method_exists($page, 'getUrlAttribute') ? $page->getUrlAttribute() : null,
                            'image_url'   => method_exists($page, 'getImageUrlAttribute') ? $page->getImageUrlAttribute() : null,
                        ]);
                        $bar->advance();
                    }
                    $bar->finish();
                    $this->info("\n '".count($pages)."' Pages Indexed.");

                    break;
                case 'users':

                    $users = User::all();
                    $bar = $this->output->createProgressBar(count($users));
                    $bar->start();
                    foreach ($users as $user) {
                        DB::table('site_temp_index')->insert([
                            // input all neccessary fields
                            'id'          => $user->id,
                            'title'       => $user->name,
                            'type'        => get_class($user),
                            'key'         => 'User',
                            'identifier'  => $user->name,
                            'description' => null,
                            'url'         => method_exists($user, 'getUrlAttribute') ? $user->getUrlAttribute() : null,
                            'image_url'   => method_exists($user, 'getImageUrlAttribute') ? $user->getImageUrlAttribute() : null,
                        ]);
                        $bar->advance();
                    }
                    $bar->finish();
                    $this->info("\n '".count($users)."' Users Indexed.");

                    break;
                case 'items':

                    $items = Item::all();
                    $bar = $this->output->createProgressBar(count($items));
                    $bar->start();
                    foreach ($items as $item) {
                        DB::table('site_temp_index')->insert([
                            // input all neccessary fields
                            'id'          => $item->id,
                            'title'       => $item->name,
                            'type'        => get_class($item),
                            'key'         => 'Item',
                            'identifier'  => $item->name,
                            'description' => $this->cleanDescription($item->parsed_description),
                            'url'         => method_exists($item, 'getUrlAttribute') ? $item->getUrlAttribute() : null,
                            'image_url'   => method_exists($item, 'getImageUrlAttribute') ? $item->getImageUrlAttribute() : null,
                        ]);
                        $bar->advance();
                    }
                    $bar->finish();
                    $this->info("\n '".count($items)."' Items Indexed.");

                    break;
                case 'prompts':

                    $prompts = Prompt::active()->get();
                    $bar = $this->output->createProgressBar(count($prompts));
                    $bar->start();
                    foreach ($prompts as $prompt) {
                        DB::table('site_temp_index')->insert([
                            // input all neccessary fields
                            'id'          => $prompt->id,
                            'title'       => $prompt->name,
                            'type'        => get_class($prompt),
                            'key'         => 'Prompt',
                            'identifier'  => $prompt->id,
                            'description' => $this->cleanDescription($prompt->parsed_description),
                            'url'         => method_exists($prompt, 'getUrlAttribute') ? $prompt->getUrlAttribute() : null,
                            'image_url'   => method_exists($prompt, 'getImageUrlAttribute') ? $prompt->getImageUrlAttribute() : null,
                        ]);
                        $bar->advance();
                    }
                    $bar->finish();
                    $this->info("\n '".count($prompts)."' Prompts Indexed.");

                    break;
                case 'shops':

                    $shops = Shop::where('is_active', 1)->get();
                    $bar = $this->output->createProgressBar(count($shops));
                    $bar->start();
                    foreach ($shops as $shop) {
                        DB::table('site_temp_index')->insert([
                            // input all neccessary fields
                            'id'          => $shop->id,
                            'title'       => $shop->name,
                            'type'        => get_class($shop),
                            'key'         => 'Shop',
                            'identifier'  => $shop->id,
                            'description' => $this->cleanDescription($shop->parsed_description),
                            'url'         => method_exists($shop, 'getUrlAttribute') ? $shop->getUrlAttribute() : null,
                            'image_url'   => method_exists($shop, 'getImageUrlAttribute') ? $shop->getImageUrlAttribute() : null,
                        ]);
                        $bar->advance();
                    }
                    $bar->finish();
                    $this->info("\n '".count($shops)."' Shops Indexed.");

                    break;
                case 'features':

                    $features = Feature::visible()->get();
                    $bar = $this->output->createProgressBar(count($features));
                    $bar->start();
                    foreach ($features as $feature) {
                        DB::table('site_temp_index')->insert([
                            // input all neccessary fields
                            'id'          => $feature->id,
                            'title'       => $feature->name,
                            'type'        => get_class($feature),
                            'key'         => 'Trait',
                            'identifier'  => $feature->name,
                            'description' => $this->cleanDescription($feature->parsed_description),
                            'url'         => method_exists($feature, 'getUrlAttribute') ? $feature->getUrlAttribute() : null,
                            'image_url'   => method_exists($feature, 'getImageUrlAttribute') ? $feature->getImageUrlAttribute() : null,
                        ]);
                        $bar->advance();
                    }
                    $bar->finish();
                    $this->info("\n '".count($features)."' Traits Indexed.");

                    break;
            }
        }

        $this->info("\n".'Completed indexing the core tables.');
    }

    private function indexCustomTables($indexes) {
        $custom_tables = DB::table('site_settings')->where('key', 'ajax_search_custom_tables')->pluck('value');
        $custom_tables = isset($custom_tables[0]) ? unserialize($custom_tables[0]) : null;
        if (!$custom_tables) {
            $this->info('There were no custom tables to index.');

            return;
        }
        $tables_to_index = array_keys($custom_tables);

        if ($indexes !== '*' && is_array($indexes)) {
            $tables_to_index = array_intersect($tables_to_index, $indexes);
            $this->info('custom tables to index: '.implode(',', $tables_to_index));
        }

        foreach ($custom_tables as $table_name => $d) {
            if (!in_array($table_name, $tables_to_index)) {
                continue;
            }

            $modelName = $d['type'];
            $data = $modelName::all();

            if (count($data) > 0) {
                $bar = $this->output->createProgressBar(count($data));
                $bar->start();
                foreach ($data as $row) {
                    DB::table('site_temp_index')->insert([
                        'id'          => $row[$d['identifier']],
                        'title'       => $row[$d['title']],
                        'type'        => get_class($row),
                        'key'         => substr(strrchr($modelName, '\\'), 1),
                        'identifier'  => $row[$d['identifier']],
                        'description' => $this->cleanDescription($row[$d['description']]),
                        'url'         => method_exists($row, 'getUrlAttribute') ? $row->getUrlAttribute() : null,
                        'image_url'   => method_exists($row, 'getImageUrlAttribute') ? $row->getImageUrlAttribute() : null,
                    ]);
                    $bar->advance();
                }
                $bar->finish();
                $this->info("\n '".count($data)."' ".$table_name.' Indexed.');
            } else {
                $this->info('No data to index under '.$table_name);
                continue;
            }
        }
        $this->info("\n".'Completed indexing custom tables.');
    }

    private function getCustomTables() {
        $custom_tables = DB::table('site_settings')->where('key', 'ajax_search_custom_tables')->pluck('value');
        $custom_tables = isset($custom_tables[0]) ? unserialize($custom_tables[0]) : null;

        return $custom_tables;
    }

    private function cleanDescription($string) {
        $cleaned = strip_tags($string);
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $cleaned = str_replace(["\xc2\xa0", "\xa0"], ' ', $cleaned);
        $cleaned = iconv('UTF-8', 'UTF-8//IGNORE', $cleaned);
        $cleaned = str_replace(["\r\n", "\r", "\n"], ' ', $cleaned);
        $cleaned = preg_replace('/[^A-Za-z0-9 ]/', '', $cleaned);
        $cleaned = strtolower($cleaned);

        $stopwords = self::$stopwords;
        if ($stopwords) {
            $stopwords = preg_split('/\s+/', trim($stopwords));
            $pattern = '/\b('.implode('|', array_map('preg_quote', $stopwords)).')\b/i';
            $cleaned = preg_replace($pattern, ' ', $cleaned);
            $cleaned = preg_replace('/\s+/', ' ', $cleaned);
            $cleaned = trim($cleaned);
            $clean_array = explode(' ', $cleaned);
            $unique = array_unique($clean_array);
            $cleaned = implode(' ', $unique);
        }

        if (mb_strlen($cleaned) > 100) {
            $cleaned = mb_substr($cleaned, 0, 100);
        }

        return $cleaned;
    }
}
