<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class SearchController extends Controller {
    public function siteSearch(Request $request) {
        $input = $request->input('s');

        $result = DB::table('site_index')
            ->where('title', 'like', '%'.$input.'%')
            ->orWhere('description', 'like', '%'.$input.'%')
            ->limit(25)
            ->get();

        $result_list = [];

        foreach ($result as $r) {
            $url = findPageUrlStructure($r->type, $r->identifier);
            $row = '<div class="resultrow"><a href="'.$url.'"><div class="title"><span class="badge badge-secondary">'.$r->type.'</span>'.$r->title.'</div></a></div>';
            echo $row;
        }
    }
}

function findPageUrlStructure($type, $key) {
    $search = strtolower($type);

    $item = '/world/items?name=';
    $character = '/character/';
    $user = '/user/';
    $page = '/info/';
    $pet = '/world/pets/';
    $prompt = '/prompts/';
    $shop = '/shops/';
    $feature = '/world/traits?name=';
    //Add additional variables here with structure for custom search types

    $domain = $_SERVER['SERVER_NAME'];

    return ${$search}.$key;
}
