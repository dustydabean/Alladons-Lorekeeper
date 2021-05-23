<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Feature\FeatureCategory;
use App\Models\Feature\Feature;
use App\Models\Genetics\Loci;
use App\Models\Genetics\LociAllele;
use App\Models\Species\Species;
use App\Models\Species\Subtype;

class GeneticsService extends Service
{
    /**
     * Create a category.
     *
     * @param  array                            $data
     * @param  \App\Models\User\User            $user
     * @return \App\Models\Feature\Loci|bool
     */
    public function createLoci($data, $user)
    {
        DB::beginTransaction();

        try {
            if (!isset($data['name']) || $data['name'] == null || $data['name'] == "") throw new \Exception("Gene groups must have a name.");
            if (!isset($data['type'])) throw new \Exception("Gene groups must have a type.");
            if ( $data['type'] != 'gene'
              && $data['type'] != 'gradient'
              && $data['type'] != 'numeric'
            ) throw new \Exception("Invalid gene type selected.");
            if (!isset($data['length']) || $data['length'] == null || $data['length'] <= 0) throw new \Exception("Must have a length.");
            if ($data['length'] > 255) throw new \Exception("Length must be less than 256.");

            if (!isset($data['chromosome']) || $data['chromosome'] <= 0) $data['chromosome'] = null;

            $loci = Loci::create($data);
            return $this->commitReturn($loci);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Update a category.
     *
     * @param  \App\Models\Feature\Loci         $category
     * @param  array                            $data
     * @param  \App\Models\User\User            $user
     * @return \App\Models\Feature\Loci|bool
     */
    public function updateLoci($category, $data, $user)
    {
        DB::beginTransaction();

        try {
            // Name
            if (!isset($data['name']) || $data['name'] == null || $data['name'] == "") throw new \Exception("Gene groups must have a name.");
            if(Loci::where('name', $data['name'])->where('id', '!=', $category->id)->exists()) throw new \Exception("The name has already been taken.");

            // Type
            if (!isset($data['type'])) $data['type'] = $category->type;
            if ($data['type'] != $category->type) throw new \Exception("Gene group type cannot be changed.");

            // Length
            if (!isset($data['length']) || $data['length'] == null || $data['length'] <= 0) throw new \Exception("Must have a length.");
            if ($data['type'] == "gradient" && $data['length'] > 64) throw new \Exception("Length must be less than 65.");
            if ($data['length'] > 255) throw new \Exception("Length must be less than 256.");

            // Chromosome
            if (!isset($data['chromosome']) || $data['chromosome'] <= 0) $data['chromosome'] = null;

            // Alleles
            if ($category->type == "gene") {
                // Add New Alleles
                if (isset($data['allele_name'])) foreach($data['allele_name'] as $key => $alleleName) {
                    if($alleleName && $alleleName != "") {
                        $mod = isset($data['modifier'][$key]) ? $data['modifier'][$key] : "";
                        $dom = isset($data['is_dominant'][$key]) ? $data['is_dominant'][$key] == true : false;
                        $allele = LociAllele::create([
                            'loci_id' => $category->id,
                            'is_dominant' => $dom,
                            'name' => $alleleName,
                            'modifier' => $mod == "" ? null : $mod,
                        ]);
                    }
                }

                // Sort Child Alleles
                if (isset($data['allele_sort'])) {
                    $sort = array_reverse(explode(',', $data['allele_sort']));
                    foreach($sort as $key => $s)
                    {
                        $allele = LociAllele::where('id', $s)->first();
                        if (!$allele) throw new \Exception("Trying to edit an allele that does not exist.");
                        if ($allele->loci_id != $category->id) throw new \Exception("Trying to edit an allele that does not belong to this group.");
                        $isDom = isset($data['edit_allele_dominance'][$key]);
                        $name = $data['edit_allele_name'][$key];
                        if (!$name || $name == "") throw new \Exception("Allele names cannot be null.");
                        if (strlen($name) > 5) throw new \Exception("One of the allele names is too long.");
                        $modifier = isset($data['edit_allele_modifier'][$key]) ? $data['edit_allele_modifier'][$key] : "";
                        if (!$modifier) $modifier = "";
                        $allele->update([
                            'is_dominant' => $isDom,
                            'sort' => $key,
                            'name' => $name,
                            'modifier' => $modifier,
                        ]);
                    }
                }
            }

            $category->update($data);
            return $this->commitReturn($category);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Sorts category order.
     *
     * @param  array  $data
     * @return bool
     */
    public function sortLoci($data)
    {
        DB::beginTransaction();
        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));
            foreach($sort as $key => $s)
            {
                Loci::where('id', $s)->update(['sort' => $key]);
            }
            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}
