<?php

namespace App\Models\User;

use App\Facades\Settings;
use App\Models\Character\Character;
use App\Models\Character\CharacterBookmark;
use App\Models\Character\CharacterDesignUpdate;
use App\Models\Character\CharacterFolder;
use App\Models\Character\CharacterImageCreator;
use App\Models\Character\CharacterTransfer;
use App\Models\Collection\Collection;
use App\Models\Comment\CommentLike;
use App\Models\Currency\Currency;
use App\Models\Currency\CurrencyCategory;
use App\Models\Currency\CurrencyLog;
use App\Models\Gallery\GalleryCollaborator;
use App\Models\Gallery\GalleryFavorite;
use App\Models\Gallery\GallerySubmission;
use App\Models\Item\Item;
use App\Models\Item\ItemLog;
use App\Models\Limit\UserUnlockedLimit;
use App\Models\Notification;
use App\Models\Pet\PetLog;
use App\Models\Rank\Rank;
use App\Models\Rank\RankPower;
use App\Models\Recipe\Recipe;
use App\Models\Report\Report;
use App\Models\Shop\ShopLog;
use App\Models\Submission\Submission;
use App\Models\Theme;
use App\Models\Trade;
use App\Traits\Commenter;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail {
    use Commenter, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'alias', 'rank_id', 'email', 'email_verified_at', 'password', 'is_news_unread', 'is_dev_logs_unread', 'is_banned', 'has_alias', 'avatar', 'is_sales_unread', 'birthday', 'theme_id', 'decorator_theme_id',
        'is_deactivated', 'deactivater_id', 'is_raffles_unread', 'content_warning_visibility',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthday'          => 'datetime',
    ];

    /**
     * Accessors to append to the model.
     *
     * @var array
     */
    protected $appends = [
        'verified_name',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'rank',
    ];

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**********************************************************************************************

        RELATIONS

     **********************************************************************************************/

    /**
     * Get all of the user's update logs.
     */
    public function logs() {
        return $this->hasMany('App\Models\User\UserUpdateLog');
    }

    /**
     * Get user settings.
     */
    public function settings() {
        return $this->hasOne(UserSettings::class);
    }

    /**
     * Get user theme.
     */
    public function theme() {
        return $this->belongsTo(Theme::class);
    }

    /**
     * Get user decorator .
     */
    public function decoratorTheme() {
        return $this->belongsTo(Theme::class, 'decorator_theme_id');
    }

    /**
     * Get User Granted Themes.
     */
    public function themes() {
        return $this->belongsToMany(Theme::class, 'user_themes')->withPivot('id');
    }

    /**
     * Get user-editable profile data.
     */
    public function profile() {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Gets the account that deactivated this account.
     */
    public function deactivater() {
        return $this->belongsTo(self::class, 'deactivater_id');
    }

    /**
     * Get user staff profile data.
     */
    public function staffProfile() {
        return $this->hasOne('App\Models\User\StaffProfile');
    }

    /**
     * Get the user's aliases.
     */
    public function aliases() {
        return $this->hasMany(UserAlias::class);
    }

    /**
     * Get the user's primary alias.
     */
    public function primaryAlias() {
        return $this->hasOne(UserAlias::class)->where('is_primary_alias', 1);
    }

    /**
     * Get the user's notifications.
     */
    public function notifications() {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get all the user's characters, regardless of whether they are full characters of myo slots.
     */
    public function allCharacters() {
        return $this->hasMany(Character::class)->orderBy('sort', 'DESC');
    }

    /**
     * Get the user's characters.
     */
    public function characters() {
        return $this->hasMany(Character::class)->where('is_myo_slot', 0)->orderBy('sort', 'DESC');
    }

    /**
     * Get the user's MYO slots.
     */
    public function myoSlots() {
        return $this->hasMany(Character::class)->where('is_myo_slot', 1)->orderBy('id', 'DESC');
    }

    /**
     * Get the user's rank data.
     */
    public function rank() {
        return $this->belongsTo(Rank::class);
    }

    /**
     * Get the user's items.
     */
    public function items() {
        return $this->belongsToMany(Item::class, 'user_items')->withPivot('count', 'data', 'updated_at', 'id')->whereNull('user_items.deleted_at');
    }

    /**
     * Get the user's pets.
     */
    public function pets() {
        return $this->belongsToMany('App\Models\Pet\Pet', 'user_pets')->withPivot('data', 'updated_at', 'id', 'variant_id', 'character_id', 'pet_name', 'has_image', 'evolution_id')->whereNull('user_pets.deleted_at');
    }

    /**
     * Get the user's items.
     */
    public function recipes() {
        return $this->belongsToMany('App\Models\Recipe\Recipe', 'user_recipes')->withPivot('id');
    }

    /**
     * Get all of the user's gallery submissions.
     */
    public function gallerySubmissions() {
        return $this->hasMany(GallerySubmission::class)
            ->where('user_id', $this->id)
            ->orWhereIn('id', GalleryCollaborator::where('user_id', $this->id)
                ->where('type', 'Collab')->pluck('gallery_submission_id')->toArray())
            ->orderBy('created_at', 'DESC');
    }

    /**
     * Get all of the user's favorited gallery submissions.
     */
    public function galleryFavorites() {
        return $this->hasMany(GalleryFavorite::class)->where('user_id', $this->id);
    }

    /**
     * Get all of the user's character bookmarks.
     */
    public function bookmarks() {
        return $this->hasMany(CharacterBookmark::class)->where('user_id', $this->id);
    }

    /**
     * Gets all of a user's liked / disliked comments.
     */
    public function commentLikes() {
        return $this->hasMany(CommentLike::class);
    }

    /**
     * Gets all of the user's friends.
     */
    public function getFriendsAttribute() {
        // return this has many where initiator_id matches this id or where recipient_id matches this id
        return UserFriend::where('recipient_approved', 1)->where('initiator_id', $this->id)->orWhere('recipient_id', $this->id)->get();
    }

    /**
     * gets all the user's character folders.
     */
    public function folders() {
        return $this->hasMany(CharacterFolder::class);
    }

    /**
     * Gets all of the user's unlocked limits.
     */
    public function unlockedLimits() {
        return $this->hasMany(UserUnlockedLimit::class);
    }

    /**********************************************************************************************

        SCOPES

     **********************************************************************************************/

    /**
     * Scope a query to only include visible (non-banned) users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query) {
        return $query->where('is_banned', 0)->where('is_deactivated', 0);
    }

    /**
     * Scope a query to only show deactivated accounts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDisabled($query) {
        return $query->where('is_deactivated', 1);
    }

    /**
     * Scope a query based on the user's primary alias.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed                                 $reverse
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAliasSort($query, $reverse = false) {
        return $query->leftJoin('user_aliases', 'users.id', '=', 'user_aliases.user_id')
            ->orderByRaw('user_aliases.alias IS NULL ASC, user_aliases.alias '.($reverse ? 'DESC' : 'ASC'));
    }

    /**********************************************************************************************

        ACCESSORS

     **********************************************************************************************/

    /**
     * Checks if the user has the named recipe.
     *
     * @param mixed $theme_id
     *
     * @return bool
     */
    public function hasTheme($theme_id) {
        $theme = Theme::find($theme_id);
        $user_has = $this->recipes && $this->recipes->contains($theme);
        $default = $theme->is_user_selectable;

        return $default ? true : $user_has;
    }

    /**
     * Get the user's alias.
     *
     * @return string
     */
    public function getVerifiedNameAttribute() {
        return $this->name.($this->hasAlias ? '' : ' (Unverified)');
    }

    /**
     * Checks if the user has an alias (has an associated dA account).
     *
     * @return bool
     */
    public function getHasAliasAttribute() {
        if (!config('lorekeeper.settings.require_alias')) {
            return true;
        }

        return $this->attributes['has_alias'];
    }

    /**
     * Checks if the user has an email.
     *
     * @return bool
     */
    public function getHasEmailAttribute() {
        if (!config('lorekeeper.settings.require_email')) {
            return true;
        }

        return $this->attributes['email'] && $this->attributes['email_verified_at'];
    }

    /**
     * Checks if the user has an admin rank.
     *
     * @return bool
     */
    public function getIsAdminAttribute() {
        return $this->rank->isAdmin;
    }

    /**
     * Checks if the user is a staff member with powers.
     *
     * @return bool
     */
    public function getIsStaffAttribute() {
        return RankPower::where('rank_id', $this->rank_id)->exists() || $this->isAdmin;
    }

    /**
     * Checks if the user has the given power.
     *
     * @param mixed $power
     *
     * @return bool
     */
    public function hasPower($power) {
        return $this->rank->hasPower($power);
    }

    /**
     * Gets the powers associated with the user's rank.
     *
     * @return array
     */
    public function getPowers() {
        return $this->rank->getPowers();
    }

    /**
     * Gets the user's profile URL.
     *
     * @return string
     */
    public function getUrlAttribute() {
        return url('user/'.$this->name);
    }

    /**
     * Gets the URL for editing the user in the admin panel.
     *
     * @return string
     */
    public function getAdminUrlAttribute() {
        return url('admin/users/'.$this->name.'/edit');
    }

    /**
     * Displays the user's name, linked to their profile page.
     *
     * @return string
     */
    public function getDisplayNameAttribute() {
        return ($this->is_banned ? '<strike>' : '').'<a href="'.$this->url.'" class="display-user" style="'.($this->rank->color ? 'color: #'.$this->rank->color.'!important;' : '').($this->is_deactivated ? 'opacity: 0.5;' : '').'"><i class="'.($this->rank->icon ? $this->rank->icon : 'fas fa-user').' mr-1" style="opacity: 50%;"></i>'.$this->name.'</a>'.($this->is_banned ? '</strike>' : '');
    }

    /**
     * Gets the user's last username change.
     *
     * @return string
     */
    public function getPreviousUsernameAttribute() {
        // get highest id
        $log = $this->logs()->whereIn('type', ['Username Changed', 'Name/Rank Change'])->orderBy('id', 'DESC')->first();
        if (!$log) {
            return null;
        }

        return $log->data['old_name'];
    }

    /**
     * Displays the user's name, linked to their profile page.
     *
     * @return string
     */
    public function getCommentDisplayNameAttribute() {
        return ($this->is_banned ? '<strike>' : '').'<small><a href="'.$this->url.'" class="btn btn-primary btn-sm"'.($this->rank->color ? 'style="background-color: #'.$this->rank->color.'!important;color:#000!important;' : '').($this->is_deactivated ? 'opacity: 0.5;' : '').'"><i class="'.($this->rank->icon ? $this->rank->icon : 'fas fa-user').' mr-1" style="opacity: 50%;"></i>'.$this->name.'</a></small>'.($this->is_banned ? '</strike>' : '');
    }

    /**
     * Displays the user's primary alias.
     *
     * @return string
     */
    public function getDisplayAliasAttribute() {
        if (!config('lorekeeper.settings.require_alias') && !$this->attributes['has_alias']) {
            return '(No Alias)';
        }
        if (!$this->hasAlias) {
            return '(Unverified)';
        }

        return $this->primaryAlias->displayAlias;
    }

    /**
     * Displays the user's avatar.
     *
     * @return string
     */
    public function getAvatar() {
        return $this->avatar;
    }

    /**
     * Gets the display URL for a user's avatar, or the default avatar if they don't have one.
     *
     * @return url
     */
    public function getAvatarUrlAttribute() {
        if ($this->avatar == 'default.jpg' && config('lorekeeper.extensions.use_gravatar')) {
            // check if a gravatar exists
            $hash = md5(strtolower(trim($this->email)));
            $url = 'https://www.gravatar.com/avatar/'.$hash.'??d=mm&s=200';
            $headers = @get_headers($url);

            if (!preg_match('|200|', $headers[0])) {
                return url('images/avatars/default.jpg');
            } else {
                return 'https://www.gravatar.com/avatar/'.$hash.'?d=mm&s=200';
            }
        }

        return url('images/avatars/'.$this->avatar.'?v='.filemtime(public_path('images/avatars/'.$this->avatar)));
    }

    /**
     * Gets the user's log type for log creation.
     *
     * @return string
     */
    public function getLogTypeAttribute() {
        return 'User';
    }

    /**
     * Get's user birthday setting.
     */
    public function getBirthdayDisplayAttribute() {
        //
        $icon = null;
        $bday = $this->birthday;
        if (!isset($bday)) {
            return 'N/A';
        }

        if ($bday->format('d M') == Carbon::now()->format('d M')) {
            $icon = '<i class="fas fa-birthday-cake ml-1"></i>';
        }
        //
        switch ($this->settings->birthday_setting) {
            case 0:
                return null;
                break;
            case 1:
                if (Auth::check()) {
                    return $bday->format('d M').$icon;
                }
                break;
            case 2:
                return $bday->format('d M').$icon;
                break;
            case 3:
                return $bday->format('d M Y').$icon;
                break;
        }
    }

    /**
     * Check if user is of age.
     */
    public function getcheckBirthdayAttribute() {
        $bday = $this->birthday;
        if (!$bday || $bday->diffInYears(Carbon::now()) < 16) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the user's completed collections.
     */
    public function collections() {
        return $this->belongsToMany('App\Models\Collection\Collection', 'user_collections')->withPivot('id');
    }

    public function getIncompletedCollectionsAttribute() {
        return Collection::visible()->whereNotIn('id', UserCollection::where('user_id', $this->id)->pluck('collection_id')->unique());
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

     **********************************************************************************************/

    /**
     * Checks if the user can edit the given rank.
     *
     * @param mixed $rank
     *
     * @return bool
     */
    public function canEditRank($rank) {
        return $this->rank->canEditRank($rank);
    }

    /**
     * Get the user's held currencies.
     *
     * @param bool       $showAll
     * @param mixed|null $user
     * @param mixed      $showCategories
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCurrencies($showAll = false, $showCategories = false, $user = null) {
        // Get a list of currencies that need to be displayed
        // On profile: only ones marked is_displayed
        // In bank: ones marked is_displayed + the ones the user has

        $owned = UserCurrency::where('user_id', $this->id)->pluck('quantity', 'currency_id')->toArray();

        $currencies = Currency::where('is_user_owned', 1)
            ->whereHas('category', function ($query) use ($user) {
                $query->visible($user);
            })
            ->orWhereNull('currency_category_id')
            ->visible($user);
        if ($showAll) {
            $currencies->where(function ($query) use ($owned) {
                $query->where('is_displayed', 1)->orWhereIn('id', array_keys($owned));
            });

            if ($showCategories) {
                $categories = CurrencyCategory::visible()->orderBy('sort', 'DESC')->get();

                if ($categories->count()) {
                    $currencies->orderByRaw('FIELD(currency_category_id,'.implode(',', $categories->pluck('id')->toArray()).')');
                }
            }
        } else {
            $currencies = $currencies->where('is_displayed', 1);
        }

        $currencies = $currencies->orderBy('sort_user', 'DESC')->get();

        foreach ($currencies as $currency) {
            $currency->quantity = $owned[$currency->id] ?? 0;
        }

        if ($showAll && $showCategories) {
            $currencies = $currencies->groupBy(function ($currency) use ($categories) {
                if (!$currency->category) {
                    return 'Miscellaneous';
                }

                return $categories->where('id', $currency->currency_category_id)->first()->name;
            });
        }

        return $currencies;
    }

    /**
     * Get the user's held currencies as an array for select inputs.
     *
     * @param mixed $isTransferrable
     *
     * @return array
     */
    public function getCurrencySelect($isTransferrable = false) {
        $query = UserCurrency::query()->where('user_id', $this->id)->leftJoin('currencies', 'user_currencies.currency_id', '=', 'currencies.id')->orderBy('currencies.sort_user', 'DESC');
        if ($isTransferrable) {
            $query->where('currencies.allow_user_to_user', 1);
        }

        return $query->get()->pluck('name_with_quantity', 'currency_id')->toArray();
    }

    /**
     * Get the user's currency logs.
     *
     * @param int $limit
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getCurrencyLogs($limit = 10) {
        $user = $this;
        $query = CurrencyLog::with('currency')->where(function ($query) use ($user) {
            $query->with('sender')->where('sender_type', 'User')->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards', 'Claim Rewards', 'Gallery Submission Reward']);
        })->orWhere(function ($query) use ($user) {
            $query->with('recipient')->where('recipient_type', 'User')->where('recipient_id', $user->id)->where('log_type', '!=', 'Staff Removal');
        })->orderBy('id', 'DESC');
        if ($limit) {
            return $query->take($limit)->get();
        } else {
            return $query->paginate(30);
        }
    }

    /**
     * Get the user's item logs.
     *
     * @param int $limit
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getItemLogs($limit = 10) {
        $user = $this;
        $query = ItemLog::with('item')->where(function ($query) use ($user) {
            $query->with('sender')->where('sender_type', 'User')->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards', 'Claim Rewards']);
        })->orWhere(function ($query) use ($user) {
            $query->with('recipient')->where('recipient_type', 'User')->where('recipient_id', $user->id)->where('log_type', '!=', 'Staff Removal');
        })->orderBy('id', 'DESC');
        if ($limit) {
            return $query->take($limit)->get();
        } else {
            return $query->paginate(30);
        }
    }

    /**
     * Get the user's pet logs.
     *
     * @param int $limit
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getPetLogs($limit = 10) {
        $user = $this;
        $query = PetLog::with('sender')->with('recipient')->with('pet')->where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Staff Removal']);
        })->orWhere(function ($query) use ($user) {
            $query->where('recipient_id', $user->id);
        })->orderBy('id', 'DESC');
        if ($limit) {
            return $query->take($limit)->get();
        } else {
            return $query->paginate(30);
        }
    }

    /**
     * Get the user's recipe logs.
     *
     * @param int $limit
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getRecipeLogs($limit = 10) {
        $user = $this;
        $query = UserRecipeLog::with('recipe')->where(function ($query) use ($user) {
            $query->with('sender')->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards', 'Claim Rewards']);
        })->orWhere(function ($query) use ($user) {
            $query->with('recipient')->where('recipient_id', $user->id)->where('log_type', '!=', 'Staff Removal');
        })->orderBy('id', 'DESC');
        if ($limit) {
            return $query->take($limit)->get();
        } else {
            return $query->paginate(30);
        }
    }

    /**
     * Get the user's shop purchase logs.
     *
     * @param int $limit
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getShopLogs($limit = 10) {
        $user = $this;
        $query = ShopLog::where('user_id', $this->id)->with('character')->with('shop')->with('item')->with('currency')->orderBy('id', 'DESC');
        if ($limit) {
            return $query->take($limit)->get();
        } else {
            return $query->paginate(30);
        }
    }

    /**
     * Get the user's character ownership logs.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getOwnershipLogs() {
        $user = $this;
        $query = UserCharacterLog::with('sender.rank')->with('recipient.rank')->with('character')->where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)->whereNotIn('log_type', ['Character Created', 'MYO Slot Created', 'Character Design Updated', 'MYO Design Approved']);
        })->orWhere(function ($query) use ($user) {
            $query->where('recipient_id', $user->id);
        })->orderBy('id', 'DESC');

        return $query->paginate(30);
    }

    /**
     * Checks if there are characters credited to the user's alias and updates ownership to their account accordingly.
     */
    public function updateCharacters() {
        if (!$this->attributes['has_alias']) {
            return;
        }

        // Pluck alias from url and check for matches
        $urlCharacters = Character::whereNotNull('owner_url')->pluck('owner_url', 'id');
        $matches = [];
        $count = 0;
        foreach ($this->aliases as $alias) {
            // Find all urls from the same site as this alias
            foreach ($urlCharacters as $key=> $character) {
                preg_match_all(config('lorekeeper.sites.'.$alias->site.'.regex'), $character, $matches[$key]);
            }
            // Find all alias matches within those, and update the character's owner
            foreach ($matches as $key=> $match) {
                if ($match[1] != [] && strtolower($match[1][0]) == strtolower($alias->alias)) {
                    Character::find($key)->update(['owner_url' => null, 'user_id' => $this->id]);
                    $count += 1;
                }
            }
        }

        //
        if ($count > 0) {
            $this->settings->is_fto = 0;
        }
        $this->settings->save();
    }

    /**
     * Checks if there are art or design credits credited to the user's alias and credits them to their account accordingly.
     */
    public function updateArtDesignCredits() {
        if (!$this->attributes['has_alias']) {
            return;
        }

        // Pluck alias from url and check for matches
        $urlCreators = CharacterImageCreator::whereNotNull('url')->pluck('url', 'id');
        $matches = [];
        foreach ($this->aliases as $alias) {
            // Find all urls from the same site as this alias
            foreach ($urlCreators as $key=> $creator) {
                preg_match_all(config('lorekeeper.sites.'.$alias->site.'.regex'), $creator, $matches[$key]);
            }
            // Find all alias matches within those, and update the relevant CharacterImageCreator
            foreach ($matches as $key=> $match) {
                if ($match[1] != [] && strtolower($match[1][0]) == strtolower($alias->alias)) {
                    CharacterImageCreator::find($key)->update(['url' => null, 'user_id' => $this->id]);
                }
            }
        }
    }

    /**
     * Get the user's submissions.
     *
     * @param mixed|null $user
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getSubmissions($user = null) {
        return Submission::with('user')->with('prompt')->viewable($user ? $user : null)->where('user_id', $this->id)->orderBy('id', 'DESC');
    }

    /**
     * Checks if the user has bookmarked a character.
     * Returns the bookmark if one exists.
     *
     * @param mixed $character
     *
     * @return CharacterBookmark
     */
    public function hasBookmarked($character) {
        return CharacterBookmark::where('user_id', $this->id)->where('character_id', $character->id)->first();
    }

    /**
     * Get the user's collection logs.
     *
     * @param int $limit
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getCollectionLogs($limit = 10) {
        $user = $this;
        $query = UserCollectionLog::with('collection')->where(function ($query) use ($user) {
            $query->with('sender')->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards', 'Claim Rewards']);
        })->orWhere(function ($query) use ($user) {
            $query->with('recipient')->where('recipient_id', $user->id)->where('log_type', '!=', 'Staff Removal');
        })->orderBy('id', 'DESC');
        if ($limit) {
            return $query->take($limit)->get();
        } else {
            return $query->paginate(30);
        }
    }

    /**
     * Checks if the user has the named collection.
     *
     * @param mixed $collection_id
     *
     * @return bool
     */
    public function hasCollection($collection_id) {
        $collection = Collection::find($collection_id);
        $user_has = $this->collections->contains($collection);

        return $user_has;
    }

    /**
     * Returned collections listed that are completed
     * Reversal simply.
     *
     * @param mixed $ids
     * @param mixed $reverse
     *
     * @return object
     */
    public function ownedCollections($ids, $reverse = false) {
        $collections = Collection::find($ids);
        $collectionCollection = [];
        foreach ($collections as $collection) {
            if ($reverse) {
                if (!$this->collections->contains($collection)) {
                    $collectionCollection[] = $collection;
                }
            } else {
                if ($this->collections->contains($collection)) {
                    $collectionCollection[] = $collection;
                }
            }
        }

        return $collectionCollection;
    }

    /**
     * Check if there are any Admin Notifications.
     *
     * @param mixed $user
     *
     * @return int
     */
    public function hasAdminNotification($user) {
        $count = [];
        $count[] = $this->hasPower('manage_submissions') ? Submission::where('status', 'Pending')->whereNotNull('prompt_id')->count() : 0; //submissionCount
        $count[] = $this->hasPower('manage_submissions') ? Submission::where('status', 'Pending')->whereNull('prompt_id')->count() : 0; //claimCount
        $count[] = $this->hasPower('manage_characters') ? CharacterDesignUpdate::characters()->where('status', 'Pending')->count() : 0; //designCount
        $count[] = $this->hasPower('manage_characters') ? CharacterDesignUpdate::myos()->where('status', 'Pending')->count() : 0; //myoCount
        $count[] = $this->hasPower('manage_characters') ? CharacterTransfer::active()->where('is_approved', 0)->count() : 0; //transferCount
        $count[] = $this->hasPower('manage_characters') ? Trade::where('status', 'Pending')->count() : 0; //tradeCount
        $count[] = $this->hasPower('manage_characters') ? Trade::where('status', 'Pending')->count() : 0; //tradeCount
        $count[] = $this->hasPower('manage_submissions') ? GallerySubmission::pending()->collaboratorApproved()->count() : 0; //galleryCount
        $count[] = $this->hasPower('manage_reports') ? Report::where('status', 'Pending')->count() : 0; //reportCount
        $count[] = $this->hasPower('manage_reports') ? Report::assignedToMe($this)->count() : 0; //assignedReportCount

        // If Adoption Center is installed:
        // $count[] = $this->hasPower('manage_submissions') && $this->hasPower('manage_characters') ? Surrender::where('status', 'Pending')->count() : 0; //surrenderCount

        // If Affiliates is installed:
        // $count[] = $this->hasPower('manage_affiliates')     ? Affiliate::where('status', 'Pending')->count()                                : 0; //affiliateCount

        return array_sum($count);
    }

    /**
     * Checks if a user is blocked by another user.
     *
     * @param mixed $user
     */
    public function isBlocked($user) {
        return UserBlock::where('user_id', $user->id)->where('blocked_id', $this->id)->exists();
    }

    /**
     * Checks if a user is friends with another user.
     *
     * @param mixed $user
     */
    public function isFriendsWith($user) {
        // check both initiator_id, recipient_id for this user and the other user
        return UserFriend::where('recipient_approved', 1)
            ->where('initiator_id', $this->id)->where('recipient_id', $user->id)
            ->orWhere('initiator_id', $user->id)->where('recipient_id', $this->id)->exists();
    }

    /**
     * Checks if a user has a pending request with another user.
     *
     * @param mixed $user
     */
    public function isPendingFriendsWith($user) {
        // check both initiator_id, recipient_id for this user and the other user
        if ($this->isFriendsWith($user)) {
            return false;
        }

        return UserFriend::where('recipient_approved', 0)
            ->where('initiator_id', $this->id)->where('recipient_id', $user->id)
            ->orWhere('initiator_id', $user->id)->where('recipient_id', $this->id)->exists();
    }

    /**
     * gets userOptions for the user divided by friends and all.
     */
    public function getUserOptionsAttribute() {
        $userOptions = [];
        $friends = [];
        foreach ($this->friends as $friend) {
            $friends[] = $friend->other($this->id)->id;
        }
        $blocked = [];
        if (!Settings::get('allow_blocked_transfers')) {
            $blocked = UserBlock::where('blocked_id', $this->id)->pluck('user_id')->toArray();
            $friends = array_diff($friends, $blocked);
        }
        $userOptions['Friends'] = self::visible()->where('id', '!=', $this->id)->whereIn('id', $friends)->orderBy('name')->pluck('name', 'id')->toArray();
        $userOptions['All Users'] = self::visible()->where('id', '!=', $this->id)->whereNotIn('id', $friends)
            ->whereNotIn('id', $blocked)
            ->orderBy('name')->pluck('name', 'id')->toArray();

        return $userOptions;
    }

    /**
     * Checks if the user has the named recipe.
     *
     * @param mixed $recipe_id
     *
     * @return bool
     */
    public function hasRecipe($recipe_id) {
        $recipe = Recipe::find($recipe_id);
        $user_has = $this->recipes->contains($recipe);
        $default = !$recipe->needs_unlocking;

        return $default ? true : $user_has;
    }

    /**
     * Returned recipes listed that are owned
     * Reversal simply.
     *
     * @param mixed $ids
     * @param mixed $reverse
     *
     * @return object
     */
    public function ownedRecipes($ids, $reverse = false) {
        $recipes = Recipe::find($ids);
        $recipeCollection = [];
        foreach ($recipes as $recipe) {
            if ($reverse) {
                if (!$this->recipes->contains($recipe)) {
                    $recipeCollection[] = $recipe;
                }
            } else {
                if ($this->recipes->contains($recipe)) {
                    $recipeCollection[] = $recipe;
                }
            }
        }

        return $recipeCollection;
    }
}
