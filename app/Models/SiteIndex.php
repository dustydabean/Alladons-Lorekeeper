<?php

namespace App\Models;

class SiteIndex extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'title', 'type', 'identifier', 'description', 'key', 'url', 'image_url'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'site_index';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the indexed item.
     */
    public function indexedModel() {
        return $this->morphTo(null, 'type', 'id');
    }

    /**********************************************************************************************

        ATTRIBUTES

     **********************************************************************************************/

    /**
     * Gets the clean label for a given model.
     * If necessary (the name of the model doesn't match the desired label), add new cases here.
     *
     * @return string
     */
    public function getTypeLabelAttribute() {
        switch ($this->type) {
            case 'App\Models\SitePage':
                return 'Page';
                break;
            case 'App\Models\Feature\Feature':
                return 'Trait';
                break;
            default:
                return class_basename($this->indexedModel);
                break;
        }
    }
}
