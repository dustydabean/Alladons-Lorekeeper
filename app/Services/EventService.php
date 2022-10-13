<?php namespace App\Services;

use App\Facades\Settings;
use App\Models\EventTeam;
use App\Models\User\UserCurrency;
use App\Services\Service;

use Illuminate\Support\Facades\DB;

class EventService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Event Service
    |--------------------------------------------------------------------------
    |
    | Handles functions relating to events.
    |
    */

    /**
     * Zeroes currently owned event currency for all users.
     *
     * @param \App\Models\User\User $user
     *
     * @return string
     */
    public function clearEventCurrency($user)
    {
        DB::beginTransaction();

        try {
            if(UserCurrency::where('currency_id', Settings::get('event_currency'))->exists()) {
                UserCurrency::where('currency_id', Settings::get('event_currency'))->update(['quantity' => 0]);
            } else {
                throw new \Exception('No event currency exists to be cleared!');
            }

            if(EventTeam::where('score', '>', 0)->exists()) {
                EventTeam::where('score', '>', 0)->update(['score' => 0]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates team information.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return string
     */
    public function updateTeams($data, $user)
    {
        DB::beginTransaction();

        try {
            // It's important that this processing run even if
            // no information has been provided, e.g. if all
            // teams are being removed. Setting this indicates
            // as much and will allow removals, etc to proceed.
            if(!isset($data['name'])) {
                $data['name'] = [];
            }

            // Set up access to a service for image handling
            $service = (new CurrencyService);

            if(EventTeam::all()->count()) {
                // Delete only removed teams
                foreach(EventTeam::all() as $team) {
                    if(!array_has($data['name'], $team->id)) {
                        if(Settings::get('event_teams')) throw new \Exception('Teams cannot be deleted while enabled/an event is ongoing!');

                        if($team->has_image) {
                            $service->deleteImage($team->imagePath, $team->imageFileName);
                        }

                        if($team->members->count()) {
                            $team->members()->update(['team_id' => null]);
                        }

                        $team->delete();
                    }
                }
            }

            if(isset($data['name'])) {
                foreach($data['name'] as $key=>$teamName) {
                    if($teamName == null) throw new \Exception('Team name is required.');
                    if(EventTeam::where('name', $teamName)->where('id', '!=', $key)->exists()) throw new \Exception('A team with this name already exists.');

                    if(EventTeam::where('id', $key)->exists()) {
                        $team = EventTeam::where('id', $key)->first();
                        if(!$team) {
                            throw new \Exception('Failed to find team.');
                        }

                        $team->update([
                            'name' => $teamName,
                            'score' => $data['score'][$key],
                        ]);

                        if(isset($data['image'][$key])) {
                            $team->update(['has_image' => 1]);
                            $service->handleImage($data['image'][$key], $team->imagePath, $team->imageFileName);
                        } elseif (isset($data['remove_image'][$key]) && $data['remove_image'][$key]) {
                            $team->update(['has_image' => 0]);
                            $service->deleteImage($team->imagePath, $team->imageFileName);
                        }
                    } else {
                        $team = EventTeam::create([
                            'name' => $teamName,
                        ]);

                        if(!$team) throw new \Exception('Failed to create team.');

                        if(isset($data['image'][$key])) {
                            $team->update(['has_image' => 1]);
                            $service->handleImage($data['image'][$key], $team->imagePath, $team->imageFileName);
                        }
                    }
                }
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}
