<?php

namespace App\Http\Controllers\Admin\Data;

use Auth;
use App\Http\Controllers\Controller;
use App\Models\Character\Character;
use App\Models\Character\CharacterGenome;
use App\Models\Feature\FeatureCategory;
use App\Models\Genetics\Loci;
use App\Models\Genetics\LociAllele;
use App\Services\GeneticsService;
use Illuminate\Http\Request;

class GeneticsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Genetics Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of character genetics alleles and loci and such.
    |
    */

    /** Controller Middleware */
    public function __construct() {
        $this->middleware("power:view_hidden_genetics");
    }

    /**
     * Shows the gene group as a sorted index.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request)
    {
        $query = Loci::query();
        $data = $request->only(['name', 'type']);
        if(isset($data['name'])) $query->where('name', 'LIKE', '%'.$data['name'].'%');
        if(isset($data['type'])) {
            switch ($data['type']) {
                case 1:
                    $query->where('type', 'gene');
                    break;
                case 2:
                    $query->where('type', 'gradient');
                    break;
                case 3:
                    $query->where('type', 'numeric');
                    break;
                default: break;
            }
        }
        return view('admin.genetics.locis', [
            'locis' => $query->orderBy('sort', 'desc')->paginate(20)->appends($request->query()),
            'categories' => ['none' => 'Any Category'] + FeatureCategory::orderBy('sort', 'desc')->pluck('name', 'id')->toArray()
        ]);
    }

    /**
     * Shows the gene group as a sortable list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSortIndex()
    {
        return view('admin.genetics.loci_sort', [
            'categories' => loci::orderBy('sort', 'desc')->get()
        ]);
    }

    /**
     * Sorts the gene groups.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\FeatureService  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortLoci(Request $request, GeneticsService $service)
    {
        if($service->sortLoci($request->get('sort'))) {
            flash('Gene group order updated successfully.')->success();
        } else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Shows the create gene group page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateLoci()
    {
        return view('admin.genetics.create_edit_loci', [
            'category' => new Loci
        ]);
    }

    /**
     * Shows the edit gene group page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditLoci($id)
    {
        $category = Loci::find($id);
        if(!$category) abort(404);
        return view('admin.genetics.create_edit_loci', [
            'category' => $category,
        ]);
    }

    /**
     * Creates or edits a gene group.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\FeatureService  $service
     * @param  int|null                     $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditLoci(Request $request, GeneticsService $service, $id = null)
    {
        $data = $request->only([
            'name', 'type', 'length', 'chromosome',
            'is_dominant', 'allele_name', 'modifier', 'allele_sort',
            'edit_allele_dominance', 'edit_allele_name', 'edit_allele_modifier',
        ]);
        if ($id && $service->updateLoci(Loci::find($id), $data, Auth::user())) {
            flash('Category updated successfully.')->success();
        } else if (!$id && $category = $service->createLoci($data, Auth::user())) {
            flash('Category created successfully.')->success();
            return redirect()->to('admin/data/genetics/edit/'.$category->id);
        } else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Gets the gene group deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteLoci($id)
    {
        $category = Loci::find($id);
        return view('admin.genetics._delete_loci', [
            'loci' => $category,
        ]);
    }

    /**
     * Deletes a gene group.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\FeatureService  $service
     * @param  int|null                     $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteLoci(Request $request, GeneticsService $service, $id)
    {
        if($id && $service->deleteLoci(Loci::find($id))) {
            flash('Loci deleted successfully.')->success();
        } else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/genetics');
    }

    /**
     * Gets the gene group deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteAllele($id)
    {
        $loci = Loci::find($id);
        return view('admin.genetics._delete_loci_allele', [
            'loci' => $loci,
            'alleles' => $loci->alleles->pluck('full_name', 'id')->toArray(),
        ]);
    }

    /**
     * Creates or edits a feature category.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\FeatureService  $service
     * @param  int|null                     $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteAllele(Request $request, GeneticsService $service, $id)
    {
        $loci = Loci::find($id);
        if (!$loci) abort(404);
        $data = $request->only(['target_allele', 'replacement_allele']);
        if($id && $service->deleteLociAllele($data, $loci)) {
            flash('Allele deleted successfully.')->success();
        } else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/genetics/edit/'.$loci->id);
    }

    /**
     * Shows the breeding roller index.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getBreedingRoller(Request $request)
    {
        $ids = CharacterGenome::select('character_id')->distinct()->pluck('character_id')->toArray();
        $characters = Character::selectRaw("id, if(name is not null, concat(slug, ': ', name), slug) as select_name")->where('is_myo_slot', false)->whereIn('id', $ids)->pluck('select_name', 'id')->toArray();
        return view('admin.genetics.roller', [
            'characters' => $characters,
        ]);
    }

    /**
     * Grabs the list of a character's genomes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterGenomes(Request $request)
    {
        $character = Character::where('id', $request->input('id'))->where('is_myo_slot', false)->first();
        if (!$character) abort(404);
        if (!$character->genomes) abort(404);
        return view('admin.genetics._fetch_genomes', [
            'genomes' => $character->genomes,
        ]);
    }

    /**
     * Grabs a list of possible child genomes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPossibleChildGenomes(Request $request)
    {
        $sire = Character::where('id', $request->input('sire'))->where('is_myo_slot', false)->first();
        if (!$sire) abort(404);
        if (!$sire->genomes) abort(404);

        $dam = Character::where('id', $request->input('dam'))->where('is_myo_slot', false)->first();
        if (!$dam) abort(404);
        if (!$dam->genomes) abort(404);

        $children = $this->testCombineGenes($sire, $dam);

        return view('admin.genetics._fetch_genomes', [
            'genomes' => $children,
            'preview' => true,
        ]);
    }

    private function testCombineGenes($sire, $dam)
    {
        $children = [];
        for ($i = 0; $i < 3; $i++) {
            // gets random genomes from parents, allows for children to be from different genomes.
            $mother = $dam->genomes->random();
            $father = $sire->genomes->random();

            // a function inside CharacterGenome that will cross mother's genes with father's.
            // called from the mother's genome to ensure the matrilineal genes go first.
            $children += [ $i => $mother->crossWith($father) ];
        }
        return $children;
    }
}
