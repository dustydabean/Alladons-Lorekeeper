<?php

namespace App\Http\Controllers;

use App\Models\Criteria\Criterion;
use App\Models\Gallery\GalleryCriterion;
use App\Models\Prompt\PromptCriterion;
use Illuminate\Http\Request;

class CriterionController extends Controller {
    /**
     * returns a criterion's guide page.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCriterionGuide($id) {
        $criterion = Criterion::where('id', $id)->first();

        if (!$criterion->is_guide_active) {
            abort(404);
        }

        return view('criteria.guide', [
            'criterion' => $criterion,
        ]);
    }

    /**
     * returns a criterion's form based on steps.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCriterionFormLimited($id) {
        return view('criteria._minimum_requirements', [
            'criterion'       => Criterion::where('id', $id)->first(),
            'minRequirements' => null,
        ]);
    }

    /**
     * returns a criterion's form based on steps.
     *
     * @param mixed|null $entity
     * @param mixed      $id
     * @param mixed|null $entity_id
     * @param mixed|null $form_id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCriterionForm($entity, $id, $entity_id = null, $form_id = null) {
        if ($entity_id && $entity) {
            if ($entity === 'prompt') {
                $entityCriteria = PromptCriterion::where('prompt_id', $entity_id)->where('criterion_id', $id)->first();
            } elseif ($entity === 'gallery') {
                $entityCriteria = GalleryCriterion::where('gallery_id', $entity_id)->where('criterion_id', $id)->first();
            }
        }

        return view('criteria._minimum_requirements', [
            'criterion'       => Criterion::where('id', $id)->first(),
            'minRequirements' => isset($entityCriteria) ? $entityCriteria->minRequirements : null,
            'title'           => isset($entityCriteria) ? 'Criterion Options' : null,
            'limitByMinReq'   => isset($entityCriteria) ? true : null,
            'id'              => $form_id,
        ]);
    }

    /**
     * returns a criterion dd based on entity.
     *
     * @param mixed $entity
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCriterionSelector($entity, $id) {
        if ($entity === 'prompt') {
            $entityCriteria = PromptCriterion::where('prompt_id', $id)->pluck('criterion_id')->toArray();
        } elseif ($entity === 'gallery') {
            $entityCriteria = GalleryCriterion::where('gallery_id', $id)->pluck('criterion_id')->toArray();
        }

        $criteria = Criterion::whereIn('id', $entityCriteria)->pluck('name', 'id');

        return view('criteria._criterion_selector', [
            'criteria' => $criteria,
        ]);
    }

    /**
     * returns an amount based on a criterion id and data.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function postCriterionRewards($id, Request $request) {
        $stepData = $request->except('_token');
        $criterion = Criterion::where('id', $id)->first();

        return $criterion->currency->display($criterion->calculateReward($stepData));
    }
}
