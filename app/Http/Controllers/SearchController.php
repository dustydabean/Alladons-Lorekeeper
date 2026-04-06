<?php

namespace App\Http\Controllers;

use App\Models\SiteIndex;
use Illuminate\Http\Request;

class SearchController extends Controller {
    public function siteSearch(Request $request) {
        $input = $request->input('s');
        $type = ucwords($request->input('type'));

        $query = SiteIndex::query();

        if ($type) {
            $query->where('key', '=', $type);
        }

        $query->where(function ($query) use ($input) {
            $query->where('title', 'like', '%'.$input.'%')
              ->orWhere('description', 'like', '%'.$input.'%');
        })
        ->limit(25);

        $result = $query->get();

        if(count($result) > 0 ) {
            foreach ($result as $r) {
                $url = $r->url ?? null;
                $image = $r->image_url ?? null;
                $row = '<div class="resultrow">
                            <a class="d-flex align-items-center justify-content-start" href="'. ( $url ?? $r->indexedModel->url ) .'">
                                '.($image ? '<img src="'.$image.'" class="img-fluid img-thumb rounded border mr-2" />' : '').'
                                <div class="title"><span class="badge badge-secondary">'.$r->typeLabel.'</span>'.$r->title.'</div>
                                </a>
                            </div>';
                echo $row;
            }
        } else {
            echo '<p class="text-muted mb-0">No results were found!</p>';
        }
    }
}
