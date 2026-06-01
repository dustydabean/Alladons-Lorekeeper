<?php

namespace App\Services\Item;

use App\Models\Item\Item;
use App\Models\Pet\Pet;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

class SpliceService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Splice Service
    |--------------------------------------------------------------------------
    |
    | Handles the editing and usage of splice type items.
    |
    */

    /**
     * Retrieves any data that should be used in the item tag editing form.
     *
     * @return array
     */
    public function getEditData() {
        // group the variants by their $variant->pet name, and pluck the variant name and id
        $variants = Pet::whereNotNull('parent_id')->with('parent')->get()->groupBy('parent.name')->map(function ($item) {
            return $item->pluck('name', 'id');
        })->toArray();

        $variantParents = Pet::whereNotNull('parent_id')->get()->unique('parent_id')->pluck('parent_id')->toArray();
        $parents = Pet::whereIn('id', $variantParents)->orderBy('name')->pluck('name', 'id')->toArray();

        return [
            'variants' => $variants,
            'parents'  => $parents,
        ];
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format.
     *
     * @param mixed $tag
     *
     * @return mixed
     */
    public function getTagData($tag) {
        $displayVariants = [];
        $variants = [];
        if (isset($tag->data['splice_type']) && $tag->data['splice_type'] == 'all') {
            $displayVariants[] = 'Any companions and all variants allowed';
        } elseif (isset($tag->data['splice_type']) && $tag->data['splice_type'] == 'by_species') {
            if (isset($tag->data['parent_ids']) && $tag->data['parent_ids']) {
                foreach ($tag->data['parent_ids'] as $parentId) {
                    $parent = Pet::find($parentId);
                    $displayVariants[] = 'All variants of <a href="'.$parent->url.'" target="_blank">'.$parent->name.'</a>';
                }

                $variants = Pet::visible()->whereNotNull('parent_id')->whereIn('parent_id', $tag->data['parent_ids'])->with('parent')->get()->groupBy('parent.name')->map(function ($item) {
                    return $item->pluck('name', 'id');
                })->toArray();
            }
        } elseif (isset($tag->data['splice_type']) && $tag->data['splice_type'] == 'by_variants') {
            if (isset($tag->data['variant_ids']) && $tag->data['variant_ids']) {
                foreach ($tag->data['variant_ids'] as $variantId) {
                    if ($variantId == 'default') {
                        $displayVariants[] = 'Default';
                    } else {
                        $variant = Pet::find($variantId);
                        $displayVariants[] = '<a href="'.$variant->parent->url.'" target="_blank">'.$variant->name.' ('.$variant->parent->name.')</a>';
                    }

                    $variants = Pet::visible()->whereNotNull('parent_id')->whereIn('id', $tag->data['variant_ids'])->with('parent')->get()->groupBy('parent.name')->map(function ($item) {
                        return $item->pluck('name', 'id');
                    })->toArray();
                }
            }
        }

        return [
            'variant_ids' => $tag->data['variant_ids'] ?? null,
            'variants'    => $variants,
            'display'     => $displayVariants ? implode(', ', $displayVariants) : null,
            'splice_type' => $tag->data['splice_type'] ?? null,
            'parent_ids'  => $tag->data['parent_ids'] ?? null,
        ];
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format.
     *
     * @param mixed $tag
     * @param array $data
     *
     * @return bool
     */
    public function updateData($tag, $data) {
        DB::beginTransaction();

        try {
            $tag->data = json_encode([
                'splice_type' => $data['splice_type'] ?? null,
                'parent_ids'  => $data['parent_ids'] ?? null,
                'variant_ids' => $data['variant_ids'] ?? null,
            ]);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
