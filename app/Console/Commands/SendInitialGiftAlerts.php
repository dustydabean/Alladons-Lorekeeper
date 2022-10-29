<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
use Notifications;

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
        foreach($submissionCharacters as $submissionCharacter) {
            $characterOwnerId = Character::where('id', $submissionCharacter)->pluck('user_id');
            $notificationData = DB::table('notifications')->where('user_id', $characterOwnerId)->where('notification_type_id', 1004)->pluck('data');

            //get the character each notif is for
            $notificationCharacter = array();
            foreach($notificationData as $data) {
                $notificationCharacter[] = json_decode($data)->character_id;
            }

            //get only gift submissions
            $submissionIds = SubmissionCharacter::where('character_id', $submissionCharacter)->pluck('submission_id');
            $giftSubmissionCount = 0;
            foreach ($submissionIds as $id) {
                $add = Submission::where('id', $id)->where('user_id', '!=' , $characterOwnerId)->where('status', 'Approved')->count();
                $giftSubmissionCount += $add;
            }

            $excludesSubmissionCharacter = !in_array($submissionCharacter, $notificationCharacter);

            //create or skip notification
            if($excludesSubmissionCharacter && $giftSubmissionCount != 0) {
                $characterDetails = Character::where('id', $submissionCharacter)->first();
                $characterOwnerData = User::where('id', $characterOwnerId)->first();

                Notifications::create('GIFT_SUBMISSION_ALERT', $characterOwnerData, [
                    'character_id' => $submissionCharacter,
                    'character_url' => 'character/'.$characterDetails->slug,
                    'character_name' => $characterDetails->slug . ($characterDetails->name ? ': '.$characterDetails->name : ''),
                    'count' => $giftSubmissionCount,
                ]);
            }
        }

        $this->info('Notifications Sent Successfully!');
    }
}