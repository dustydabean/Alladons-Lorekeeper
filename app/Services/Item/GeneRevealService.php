<?php namespace App\Services\Item;

use App\Facades\Settings;
use App\Models\Character\CharacterGenome;
use App\Services\InventoryManager;
use App\Services\Service;

use DB;
use Auth;

class GeneRevealService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Gene Reveal Service
    |--------------------------------------------------------------------------
    |
    | Handles the editing and usage of gene reveal type items.
    |
    */

    /**
     * Retrieves any data that should be used in the item tag editing form.
     *
     * @return array
     */
    public function getEditData()
    {
        return [];
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format.
     *
     * @param  App\Models\Item\ItemTag  $tag
     * @return mixed
     */
    public function getTagData($tag)
    {
        $data['reveal_strength'] = isset($tag->data['reveal_strength']) ? $tag->data['reveal_strength'] == 1 : false;
        $data['fully_hidden_only'] = isset($tag->data['fully_hidden_only']) ? $tag->data['fully_hidden_only'] == 1 : false;
        return $data;
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format.
     *
     * @param  App\Models\Item\ItemTag  $tag
     * @param  array                    $data
     * @return bool
     */
    public function updateData($tag, $data)
    {
        $options['reveal_strength'] = isset($data['reveal_strength']) ? $data['reveal_strength'] == 1 : false;
        $options['fully_hidden_only'] = isset($data['fully_hidden_only']) ? $data['fully_hidden_only'] == 1 : false;

        DB::beginTransaction();
        try {
            $tag->update(['data' => json_encode($options)]);
            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Acts upon the item when used from the inventory.
     *
     * @param  \App\Models\User\UserItem  $stacks
     * @param  \App\Models\User\User      $user
     * @param  array                      $data
     * @return bool
     */
    public function act($stacks, $user, $data)
    {
        DB::beginTransaction();
        try {
            if(Settings::get('genome_default_visibility') == 2) throw new \Exception("Genomes are fully visible. Gene reveals are useless here.");
            if ($stacks->count() > 1 || $data['quantities'][0] > 1) throw new \Exception("That's too many gene reveals! You can only use these one at a time.");
            $stack = $stacks->first();
            if($stack->user_id != $user->id) throw new \Exception("This item does not belong to you.");

            $genome = CharacterGenome::where('id', $data['genome_id'])->first();
            if (!$genome) throw new \Exception("Couldn't find that genome.");
            if ($genome->character->user->id != $user->id) throw new \Exception("Cannot gene reveal a character that isn't yours.");

            $tagData = $stack->item->tag('gene_reveal')->getData();
            $vis = max($genome->visibility_level, Settings::get('genome_default_visibility'));
            if ($tagData['fully_hidden_only'] && $vis != 0) throw new \Exception("This item can only be used on fully hidden genomes");

            if((new InventoryManager)->debitStack($stack->user, 'Gene Reveal Used', ['data' => ''], $stack, 1)) {
                $vis += $tagData['reveal_strength'] ? 2 : 1;
                $genome->visibility_level = min(2, $vis);
                $genome->save();
                flash("The genome of ". $genome->character->displayName ." is now ". ($vis == 1 ? "half" : "fully") ."-known!");
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Gets the possible genomes this item can act upon.
     *
     * @param  App\Models\Item\ItemTag  $tag
     * @param  \App\Models\User\User    $user
     * @return array
     */
    public function getPossibleGenomes($tag, $user)
    {
        $list = [0 => "Select Genome"];
        $data = $tag->getData();
        $ids = CharacterGenome::whereIn('character_id', $user->allCharacters()->pluck('id')->toArray())->where('visibility_level', $data['fully_hidden_only'] ? "==" : "!=", $data['fully_hidden_only'] ? 0 : 2)->pluck('character_id')->toArray();
        foreach ($user->allCharacters()->whereIn('id', $ids)->get() as $character) {
            $i = 1;
            foreach ($character->genomes->where('visibility_level', $data['fully_hidden_only'] ? "==" : "!=", $data['fully_hidden_only'] ? 0 : 2) as $genome) {
                $list += [ $genome->id => $character->fullName." - #".$i ];
                $i++;
            }
        }
        return $list;
    }
}
