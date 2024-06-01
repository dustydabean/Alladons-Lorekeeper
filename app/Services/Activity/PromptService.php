<?php

namespace App\Services\Activity;

use App\Models\Currency\Currency;
use App\Models\Item\Item;
use App\Models\Prompt\Prompt;
use App\Services\Service;
use App\Services\SubmissionManager;
use DB;

class PromptService extends Service {

  /**
   * Retrieves any data that should be used in the activity module editing form on the admin side
   *
   * @return array
   */
  public function getEditData() {

    return [
      'prompts' => Prompt::where('is_active', 0)->where('start_at', null)->where('end_at', null)->pluck('name', 'id'),
    ];
  }

  /**
   * Retrieves any data that should be used in the activity module on the user side
   *
   * @return array
   */
  public function getActData($activity) {
    return [
      'items' => Item::orderBy('name')->released()->pluck('name', 'id'),
      'currencies' => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id'),
    ];
  }

  /**
   * Processes the data attribute of the activity and returns it in the preferred format.
   *
   * @param  string  $tag
   * @return mixed
   */
  public function getData($data) {
    $dataObj = json_decode($data);
    $dataObj->prompt = Prompt::find($dataObj->prompt_id);
    return $dataObj;
  }

  /**
   * Processes the data attribute of the activity and returns it in the preferred format.
   *
   * @param  object  $activity
   * @param  array   $data
   * @return bool
   */
  public function updateData($activity, $data) {
    return json_encode([
      'prompt_id' => $data['prompt_id'] ?? null,
      'template' => parse($data['template']) ?? null,
      'show_rewards' => isset($data['show_rewards']),
      'choose_reward' => isset($data['choose_reward']),
    ]);
  }

  /**
   * Code executed by the act post url being hit 
   * (expected from the user-side blade file at resources/views/activities/modules/name.blade.php)
   *
   * @param  \App\Models\User\UserItem  $stacks
   * @param  \App\Models\User\User      $user
   * @param  array                      $data
   * @return bool
   */
  public function act($activity, $data, $user) {
    DB::beginTransaction();

    try {
      if (isset($data['choose_reward'])) {
        $unselectedRewards = $activity->data->prompt->rewards->filter(function ($reward, $index) use ($data) {
          return $index != $data['choose_reward'];
        });

        // We negate the default rewards that standard prompt submission will add so we end up with just the selected one
        foreach ($unselectedRewards as $reward) {
          $data['rewardable_type'] = array_merge(($data['rewardable_type'] ?? []), [$reward->rewardable_type]);
          $data['rewardable_id'] = array_merge(($data['rewardable_id'] ?? []), [$reward->rewardable_id]);
          $data['quantity'] = array_merge(($data['quantity'] ?? []), [-$reward->quantity]);
        }
      }

      $data['prompt_id'] = $activity->data->prompt_id;

      $manager = new SubmissionManager;
      if (!$manager->createSubmission($data, $user, false, true)) throw new \Exception($manager->errors()->getMessages()['error'][0]);

      return $this->commitReturn(true);
    } catch (\Exception $e) {
      $this->setError('error', $e->getMessage());
    }
    return $this->rollbackReturn(false);
  }
}
