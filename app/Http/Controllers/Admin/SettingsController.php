<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use File;
use Str;

class SettingsController extends Controller {
    /**
     * Shows the settings index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('admin.settings.settings', [
            'settings' => DB::table('site_settings')->where('show_in_settings', 1)->orderBy('key')->paginate(20),
        ]);
    }

    /**
     * Edits a setting.
     *
     * @param string $key
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditSetting(Request $request, $key) {
        if (DB::table('site_settings')->where('key', $key)->update(['value' => $request->get('value')])) {
            flash('Setting updated successfully.')->success();
        } else {
            flash('Invalid setting selected.')->success();
        }

        return redirect()->back();
    }

    /**
     * Shows the settings index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAjaxSearchSettings() {

        $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name NOT LIKE '%log%'");
        $tableNames = array_map(function($table) {
            return $table->table_name;
        }, $tables);
        $tableNames = array_combine($tableNames, $tableNames);
        unset($tableNames['characters'], $tableNames['shops']);
        $tableCols = [];
        $counts = [
            'characters'    => DB::table('characters')->count() ?? 0,
            'site_pages'    => DB::table('site_pages')->count() ?? 0,
            'users'         => DB::table('users')->count() ?? 0,
            'items'         => DB::table('items')->count() ?? 0,
            'prompts'       => DB::table('prompts')->count() ?? 0,
            'shops'         => DB::table('shops')->count() ?? 0,
            'features'      => DB::table('features')->count() ?? 0,
        ];

        foreach($tableNames as $tableName) {
            $tableCols[$tableName] = DB::getSchemaBuilder()->getColumnListing($tableName);
            $tableCols[$tableName] = array_combine($tableCols[$tableName], $tableCols[$tableName]);
        }

        $core_tables = DB::table('site_settings')->where('key', 'ajax_search_core_tables')->pluck('value') ?? null;
        if ($core_tables !== '') {
            $core_tables = unserialize($core_tables[0]);
        }
        $custom_tables = DB::table('site_settings')->where('key', 'ajax_search_custom_tables')->pluck('value') ?? null;
        if ($custom_tables !== '') {
            $custom_tables = unserialize($custom_tables[0]);
        }

        return view('admin.settings.ajax_search', [
            'tables'            => ['', 'Select table...'] + $tableNames,
            'columns'           => $tableCols,
            'counts'            => $counts,
            'core_tables'       => $core_tables ?? null,
            'custom_tables'    => $custom_tables ?? null,
        ]);
    }

    /**
     * Saves the AJAX search settings.
     */
    public function postEditAjaxSearchSettings(Request $request) {

        $data = $request->only(['tables', 'custom_tables']);

        //Custom Tables
        $c_tables = [];
        if($data['custom_tables'] && count($data['custom_tables']) > 0 ) {
            foreach($data['custom_tables']['table_name'] as $i => $table_name) {

                $model = $this->getModelFromTable($table_name);

                $c_tables[$table_name] = [
                    'title'         => $data['custom_tables']['title'][$i],
                    'type'          => $model,
                    'key'           => ltrim(strrchr($model, '\\'), '\\'),
                    'identifier'    => $data['custom_tables']['identifier'][$i],
                    'description'   => $data['custom_tables']['description'][$i],
                    'url'           => null,
                    'image_url'     => null,
                ];
            }
        }

        //Core Tables
        $core_tables = [
            'characters'    => isset($data['tables']['characters']) ? 1 : 0,
            'site_pages'    => isset($data['tables']['site_pages']) ? 1 : 0,
            'items'         => isset($data['tables']['items']) ? 1 : 0,
            'users'         => isset($data['tables']['users']) ? 1 : 0,
            'prompts'       => isset($data['tables']['prompts']) ? 1 : 0,
            'shops'         => isset($data['tables']['shops']) ? 1 : 0,
            'features'      => isset($data['tables']['features']) ? 1 : 0,
        ];

        if (DB::table('site_settings')->where('key', 'ajax_search_custom_tables')->update(['value' => serialize($c_tables)]) &&
            DB::table('site_settings')->where('key', 'ajax_search_core_tables')->update(['value' => serialize($core_tables)])
        ) {
            flash('Setting updated successfully.')->success();
        } else {
            flash('Invalid setting selected.')->success();
        }

        return redirect()->back();

    }

    private function getModelFromTable($table) {
        $modelPath = app_path('Models'); // Path to the Models directory
        $files = File::allFiles($modelPath); // Recursively get all files in Models and subdirectories

        foreach ($files as $file) {
            // Build the fully qualified class name
            $relativePath = Str::replaceFirst(app_path(), '', $file->getPathname());
            $namespace = 'App' . str_replace(['/', '\\', '.php'], ['\\', '\\', ''], $relativePath);

            if (class_exists($namespace) && is_subclass_of($namespace, 'Illuminate\Database\Eloquent\Model')) {
                $model = new $namespace;

                if ($model->getTable() === $table) {
                    return $namespace;
                }
            }
        }

        return false;
    }

}
