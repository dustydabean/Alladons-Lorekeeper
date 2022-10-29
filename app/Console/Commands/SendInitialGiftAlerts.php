<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App;
use DB;
use Carbon\Carbon;
use Notifications;
use App\Services\Service;
use App\Models\Character\Character;
use App\Models\Submission\Submission;
use App\Models\Submission\SubmissionCharacter;
use App\Models\User\User;

class SendInitialGiftAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-initial-gift-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a notification to all users that have characters with submissions not made by them.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $this->info('*****************************');
        $this->info('* Send Initial Gift Alerts *');
        $this->info('*****************************'."\n");

        $this->line('Retriveing Submission Characters...');
        $submissionCharacters = SubmissionCharacter::distinct()->pluck('character_id');


        $this->line('Creating Notifications...'."\n");
        //run to check and create for each character
        foreach($submissionCharacters as $submissionCharacter) {
            $characterOwnerId = Character::where('id', $submissionCharacter)->pluck('user_id');
            //get owners' Initial Gift Alert notification(s)
            $notificationData = DB::table('notifications')->where('user_id', $characterOwnerId)->where('notification_type_id', 1004)->pluck('data');

            //put all characters the user's notificiations are for into an array
            $notificationFor = array();
            foreach($notificationData as $data) {
                $notificationFor[] = json_decode($data)->character_id;
            }

            $submissionIds = SubmissionCharacter::where('character_id', $submissionCharacter)->pluck('submission_id');
            $count = 0;
            foreach ($submissionIds as $id) {
                $add = Submission::where('id', $id)->where('user_id', '!=' , $characterOwnerId)->where('status', 'Approved')->count();
                $count += $add;
            }

            //now we're actually creating or skipping the notification creation
            if(!in_array($submissionCharacter, $notificationFor) && $count != 0) {
                //submissionCharacter only accesses submission_character table 
                //we now need to access submissionCharacter on the character table
                $characterDetails = Character::where('id', $submissionCharacter)->first();

                $characterOwnerData = User::where('id', $characterOwnerId)->first();

                Notifications::create('GIFT_SUBMISSION_ALERT', $characterOwnerData, [
                    'character_id' => $submissionCharacter,
                    'character_url' => 'character/'.$characterDetails->slug,
                    'character_name' => $characterDetails->slug . ($characterDetails->name ? ': '.$characterDetails->name : ''),
                    'count' => $count,
                ]);
            }
        }

        $this->info('Notifications Sent Successfully!');
    }
}