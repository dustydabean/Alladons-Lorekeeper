<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
use Settings;
use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\Currency\Currency;

use App\Services\CurrencyService;
use App\Services\CurrencyManager;

class AddBreedingPermissionCurrency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-breeding-permission-currency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a character-only currency for breeding permissions and accompanying setting.';

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
        $this->info('************************************');
        $this->info('* ADD BREEDING PERMISSION CURRENCY *');
        $this->info('************************************'."\n");

        $this->line("This will create a new currency with which to track total breeding permissions available to a given character and add a site setting with its ID preentered.\n");
        $this->line("It will also allow to to set up or disable automatic grants of this currency to newly created characters, as well as grant all current characters a certain amount of the currency if desired.\n");
        $this->line('After initial setup, these settings can be changed either by running this command again, or by editing them in the site settings admin panel.');

        if($this->confirm('Do you wish to create a new currency for the purpose? If there is already an existing currency fulfilling this role, decline to configure settings using it instead.')) {
            $this->line("Adding currency...\n");
            $data = [
                'is_user_owned' => 0,
                'is_character_owned' => 1,
                'name' => 'Total Breeding Permissions',
                'abbreviation' => 'Breeding Permissions',
                'description' => '<p>The total number of breeding permissions a given character is allowed.</p>'
            ];

            $currency = (new CurrencyService)->createCurrency($data, User::find(Settings::get('admin_user')));
            $this->info("Added:   Breeding Permission Currency");

            // Site Setting
            $this->line("Adding site setting...\n");

            if(!DB::table('site_settings')->where('key', 'breeding_permission_currency')->exists()) {
                DB::table('site_settings')->insert([
                    [
                        'key' => 'breeding_permission_currency',
                        'value' => $currency->id,
                        'description' => 'ID of the currency used for tracking total breeding permissions per-character.'
                    ]

                ]);
                $this->info("Added:   breeding_permission_currency");
            }
            else {
                DB::table('site_settings')->where('key', 'breeding_permission_currency')->update(['value' => $currency->id]);
                $this->info("Updated: breeding_permission_currency");
            }
        }
        elseif($this->confirm('Do you wish to configure settings for the breeding permission currency without creating a new currency?')) {

            $this->line('What currency would you like to use for breeding permission tracking?');

            foreach(Currency::pluck('name', 'id') as $id=>$name)
                $this->info('['.$id.'] '.$name);

            $currency = $this->ask('Please enter the numeric ID of the currency you wish to use');

            // Site Setting
            $this->line("Adding or adjusting site setting...\n");

            if(!DB::table('site_settings')->where('key', 'breeding_permission_currency')->exists()) {
                DB::table('site_settings')->insert([
                    [
                        'key' => 'breeding_permission_currency',
                        'value' => $currency,
                        'description' => 'ID of the currency used for tracking total breeding permissions per-character.'
                    ]

                ]);
                $this->info("Added:   breeding_permission_currency");
            }
            else {
                DB::table('site_settings')->where('key', 'breeding_permission_currency')->update(['value' => $currency]);
                $this->info("Updated: breeding_permission_currency");
            }
        }
        else
            $this->line('Skipped:  Currency configuration...');

        if($this->confirm('Should new characters be automatically given an amount of this currency upon creation?')) {
            $amount = $this->ask('What amount should new characters be given?');

            // Site Setting
            $this->line("Adding or adjusting site setting...\n");

            if(!DB::table('site_settings')->where('key', 'breeding_permission_autogrant')->exists()) {
                DB::table('site_settings')->insert([
                    [
                        'key' => 'breeding_permission_autogrant',
                        'value' => $amount,
                        'description' => 'Amount of breeding permission currency automatically given to characters on creation. Set to 0 to disable.'
                    ]

                ]);
                $this->info("Added:   breeding_permission_autogrant");
            }
            else {
                DB::table('site_settings')->where('key', 'breeding_permission_autogrant')->update(['value' => $amount]);
                $this->info("Updated: breeding_permission_autogrant");
            }
        }
        else {
            // Site Setting
            $this->line("Adding or adjusting site setting...\n");

            if(!DB::table('site_settings')->where('key', 'breeding_permission_autogrant')->exists()) {
                DB::table('site_settings')->insert([
                    [
                        'key' => 'breeding_permission_autogrant',
                        'value' => 0,
                        'description' => 'Amount of breeding permission currency automatically given to characters on creation. Set to 0 to disable.'
                    ]

                ]);
                $this->info("Added:   breeding_permission_autogrant");
            }
            else {
                DB::table('site_settings')->where('key', 'breeding_permission_autogrant')->update(['value' => 0]);
                $this->info("Updated: breeding_permission_autogrant");
            }
        }

        if($this->confirm('Would you like to grant all currently existing characters an amount of this currency at this time? Note that if there are many characters, this may take some time.')) {
            $amount = $this->ask('What amount should extant characters be given?');

            if($amount > 0) {
                $currencyManager = new CurrencyManager;

                $characters = Character::all();
                $bar = $this->output->createProgressBar(count($characters));

                $bar->start();
                foreach($characters as $character) {
                    $currencyManager->creditCurrency(User::find(Settings::get('admin_user')), $character, 'Breeding Permission Grant', null, Settings::get('breeding_permission_currency'), $amount);

                    $bar->advance();
                }

                $bar->finish();
            }
        }

        $this->line('Done!');

    }
}
