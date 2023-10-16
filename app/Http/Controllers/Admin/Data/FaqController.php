<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Services\FaqService;
use Auth;
use Config;
use Illuminate\Http\Request;

class FaqController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / FAQ Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of FAQ questions and answers.
    |
    */

    /**
     * Shows the faq index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFaqIndex(Request $request) {
        $query = Faq::query();
        $data = $request->only(['content', 'tags']);
        if (isset($data['content'])) {
            $content = $data['content'];
            $query->where(function ($query) use ($content) {
                $query->where('question', 'LIKE', '%'.$content.'%')->orWhere('answer', 'LIKE', '%'.$content.'%');
            });
        }

        if (isset($data['tags'])) {
            foreach ($data['tags'] as $tag) {
                // json decode the tag column
                $query->whereJsonContains('tags', $tag);
            }
        }

        $tags = Config::get('lorekeeper.faq');
        // tags is an array of names, make it so their key is their name also
        $tags = array_combine($tags, $tags);
        $tags = array_map(function ($tag) {
            return ucwords($tag);
        }, $tags);
        ksort($tags);
        return view('admin.faq.faq', [
            'faqs' => $query->paginate(20)->appends($request->query()),
            'tags' => Config::get('lorekeeper.faq'),
        ]);
    }

    /**
     * Shows the create faq page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateFaqQuestion() {
        $tags = Config::get('lorekeeper.faq');
        // tags is an array of names, make it so their key is their name also
        $tags = array_combine($tags, $tags);
        $tags = array_map(function ($tag) {
            return ucwords($tag);
        }, $tags);
        ksort($tags);
        return view('admin.faq.create_edit_question', [
            'faq'  => new Faq,
            'tags' => $tags,
        ]);
    }

    /**
     * Shows the edit faq page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditFaqQuestion($id) {
        $faq = Faq::find($id);
        if (!$faq) {
            abort(404);
        }
        $tags = Config::get('lorekeeper.faq');
        // tags is an array of names, make it so their key is their name also
        $tags = array_combine($tags, $tags);
        $tags = array_map(function ($tag) {
            return ucwords($tag);
        }, $tags);
        ksort($tags);
        return view('admin.faq.create_edit_question', [
            'faq'  => $faq,
            'tags' => $tags,
        ]);
    }

    /**
     * Creates or edits an faq.
     *
     * @param App\Services\FaqService $service
     * @param int|null                 $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditFaqQuestion(Request $request, FaqService $service, $id = null) {
        $id ? null : $request->validate(Faq::$createRules);
        $data = $request->only([
            'question', 'answer', 'tags', 'is_visible',
        ]);
        if ($id && $service->updateFaq(Faq::find($id), $data, Auth::user())) {
            flash('Faq updated successfully.')->success();
        } elseif (!$id && $faq = $service->createFaq($data, Auth::user())) {
            flash('Faq created successfully.')->success();

            return redirect()->to('admin/data/faq/edit/'.$faq->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the faq deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteFaqQuestion($id) {
        $faq = Faq::find($id);

        return view('admin.faq._delete_question', [
            'faq' => $faq,
        ]);
    }

    /**
     * Creates or edits an faq.
     *
     * @param App\Services\FaqService $service
     * @param int                      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteFaqQuestion(Request $request, FaqService $service, $id) {
        if ($id && $service->deleteFaq(Faq::find($id), Auth::user())) {
            flash('Faq deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/faq');
    }
}
