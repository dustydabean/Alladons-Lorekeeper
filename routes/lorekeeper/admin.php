<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Routes for users with powers.
|
*/

Route::get('/', 'HomeController@getIndex');

Route::get('admin-logs', 'HomeController@getLogs');
Route::group(['middleware' => 'admin'], function () {
    Route::get('staff-reward-settings', 'HomeController@getStaffRewardSettings');
    Route::post('staff-reward-settings/{key}', 'HomeController@postEditStaffRewardSetting');
});

Route::group(['prefix' => 'users', 'namespace' => 'Users'], function () {
    // USER LIST
    Route::group(['middleware' => 'power:edit_user_info'], function () {
        Route::get('/', 'UserController@getIndex');

        Route::get('{name}/edit', 'UserController@getUser');
        Route::post('{name}/basic', 'UserController@postUserBasicInfo');
        Route::post('{name}/alias/{id}', 'UserController@postUserAlias');
        Route::post('{name}/account', 'UserController@postUserAccount');
        Route::post('{name}/birthday', 'UserController@postUserBirthday');
        Route::post('{name}/staff-profile', 'UserController@postStaffProfile');
        Route::post('{name}/staff-links', 'UserController@postStaffLinks');
        Route::get('{name}/updates', 'UserController@getUserUpdates');

        Route::get('{name}/ban', 'UserController@getBan');
        Route::get('{name}/ban-confirm', 'UserController@getBanConfirmation');
        Route::post('{name}/ban', 'UserController@postBan');
        Route::get('{name}/unban-confirm', 'UserController@getUnbanConfirmation');
        Route::post('{name}/unban', 'UserController@postUnban');

        Route::get('{name}/deactivate', 'UserController@getDeactivate');
        Route::get('{name}/deactivate-confirm', 'UserController@getDeactivateConfirmation');
        Route::post('{name}/deactivate', 'UserController@postDeactivate');
        Route::get('{name}/reactivate-confirm', 'UserController@getReactivateConfirmation');
        Route::post('{name}/reactivate', 'UserController@postReactivate');
    });

    // RANKS
    Route::group(['middleware' => 'admin'], function () {
        Route::get('ranks', 'RankController@getIndex');
        Route::get('ranks/create', 'RankController@getCreateRank');
        Route::get('ranks/edit/{id}', 'RankController@getEditRank');
        Route::get('ranks/delete/{id}', 'RankController@getDeleteRank');
        Route::post('ranks/create', 'RankController@postCreateEditRank');
        Route::post('ranks/edit/{id?}', 'RankController@postCreateEditRank');
        Route::post('ranks/delete/{id}', 'RankController@postDeleteRank');
        Route::post('ranks/sort', 'RankController@postSortRanks');
    });
});

// SETTINGS
Route::group(['prefix' => 'invitations', 'middleware' => 'power:edit_site_settings'], function () {
    Route::get('/', 'InvitationController@getIndex');

    Route::post('create', 'InvitationController@postGenerateKey');
    Route::post('delete/{id}', 'InvitationController@postDeleteKey');
});

// FILE MANAGER
Route::group(['prefix' => 'files', 'middleware' => 'power:edit_site_settings'], function () {
    Route::get('/{folder?}', 'FileController@getIndex');

    Route::post('upload', 'FileController@postUploadFile');
    Route::post('move', 'FileController@postMoveFile');
    Route::post('rename', 'FileController@postRenameFile');
    Route::post('delete', 'FileController@postDeleteFile');
    Route::post('folder/create', 'FileController@postCreateFolder');
    Route::post('folder/delete', 'FileController@postDeleteFolder');
    Route::post('folder/rename', 'FileController@postRenameFolder');
});

// THEME MANAGER
Route::group(['prefix' => 'themes', 'middleware' => 'power:edit_site_settings'], function () {
    Route::get('/', 'ThemeController@getIndex');

    Route::get('create', 'ThemeController@getCreateTheme');
    Route::get('edit/{id}', 'ThemeController@getEditTheme');
    Route::get('delete/{id}', 'ThemeController@getDeleteTheme');
    Route::post('create', 'ThemeController@postCreateEditTheme');
    Route::post('edit/{id}', 'ThemeController@postCreateEditTheme');
    Route::post('delete/{id}', 'ThemeController@postDeleteTheme');
});

// LOG VIEWER
Route::group(['prefix' => 'logs', 'middleware' => 'power:edit_site_settings'], function () {
    Route::get('/', 'LogController@getIndex');
    Route::get('/{name}', 'LogController@getLog');
    Route::post('/delete', 'LogController@postDeleteLog');
});

// SITE IMAGES
Route::group(['prefix' => 'images', 'middleware' => 'power:edit_site_settings'], function () {
    Route::get('/', 'FileController@getSiteImages');

    Route::post('upload/css', 'FileController@postUploadCss');
    Route::post('upload', 'FileController@postUploadImage');
    Route::post('reset', 'FileController@postResetFile');
});

// GENETICS
Route::group(['prefix' => 'genetics', 'namespace' => 'Data', 'middleware' => ['power:view_hidden_genetics']], function () {
    // GENETIC DATA
    Route::middleware(['power:edit_data'])->group(function () {
        Route::get('genes', 'GeneticsController@getIndex');
        Route::get('sort', 'GeneticsController@getSortIndex');
        Route::get('create', 'GeneticsController@getCreateLoci');
        Route::get('edit/{id}', 'GeneticsController@getEditLoci');
        Route::get('delete/{id}', 'GeneticsController@getDeleteLoci');
        Route::get('delete-allele/{id}', 'GeneticsController@getDeleteAllele');

        Route::post('sort', 'GeneticsController@postSortLoci');
        Route::post('create', 'GeneticsController@postCreateEditLoci');
        Route::post('edit/{id}', 'GeneticsController@postCreateEditLoci');
        Route::post('delete/{id}', 'GeneticsController@postDeleteLoci');
        Route::post('delete-allele/{id}', 'GeneticsController@postDeleteAllele');
    });

    // ROLLERS & SUCH
    Route::middleware(['power:manage_characters'])->group(function () {
        Route::get('roller', 'GeneticsController@getBreedingRoller');
        Route::get('fetch-genomes', 'GeneticsController@getCharacterGenomes');
        Route::get('preview-breeding', 'GeneticsController@getPossibleChildGenomes');
        Route::get('logs', 'GeneticsController@getBreedingLogs');
        Route::get('logs/breeding/{id}', 'GeneticsController@getBreedingLog');

        Route::post('roll-litter', 'GeneticsController@postBreedingRoll');
    });
});

// DATA
Route::group(['prefix' => 'data', 'namespace' => 'Data', 'middleware' => 'power:edit_data'], function () {
    // GALLERIES
    Route::get('galleries', 'GalleryController@getIndex');
    Route::get('galleries/create', 'GalleryController@getCreateGallery');
    Route::get('galleries/edit/{id}', 'GalleryController@getEditGallery');
    Route::get('galleries/delete/{id}', 'GalleryController@getDeleteGallery');
    Route::post('galleries/create', 'GalleryController@postCreateEditGallery');
    Route::post('galleries/edit/{id?}', 'GalleryController@postCreateEditGallery');
    Route::post('galleries/delete/{id}', 'GalleryController@postDeleteGallery');
    Route::post('galleries/sort', 'GalleryController@postSortGallery');

    // CURRENCIES
    Route::get('currency-categories', 'CurrencyController@getIndex');
    Route::get('currency-categories/create', 'CurrencyController@getCreateCurrencyCategory');
    Route::get('currency-categories/edit/{id}', 'CurrencyController@getEditCurrencyCategory');
    Route::get('currency-categories/delete/{id}', 'CurrencyController@getDeleteCurrencyCategory');
    Route::post('currency-categories/create', 'CurrencyController@postCreateEditCurrencyCategory');
    Route::post('currency-categories/edit/{id?}', 'CurrencyController@postCreateEditCurrencyCategory');
    Route::post('currency-categories/delete/{id}', 'CurrencyController@postDeleteCurrencyCategory');
    Route::post('currency-categories/sort', 'CurrencyController@postSortCurrencyCategory');

    Route::get('currencies', 'CurrencyController@getCurrencyIndex');
    Route::get('currencies/sort', 'CurrencyController@getSort');
    Route::get('currencies/create', 'CurrencyController@getCreateCurrency');
    Route::get('currencies/edit/{id}', 'CurrencyController@getEditCurrency');
    Route::get('currencies/delete/{id}', 'CurrencyController@getDeleteCurrency');
    Route::post('currencies/create', 'CurrencyController@postCreateEditCurrency');
    Route::post('currencies/edit/{id?}', 'CurrencyController@postCreateEditCurrency');
    Route::post('currencies/delete/{id}', 'CurrencyController@postDeleteCurrency');
    Route::post('currencies/sort/{type}', 'CurrencyController@postSortCurrency')->where('type', 'user|character');

    // RARITIES
    Route::get('rarities', 'RarityController@getIndex');
    Route::get('rarities/create', 'RarityController@getCreateRarity');
    Route::get('rarities/edit/{id}', 'RarityController@getEditRarity');
    Route::get('rarities/delete/{id}', 'RarityController@getDeleteRarity');
    Route::post('rarities/create', 'RarityController@postCreateEditRarity');
    Route::post('rarities/edit/{id?}', 'RarityController@postCreateEditRarity');
    Route::post('rarities/delete/{id}', 'RarityController@postDeleteRarity');
    Route::post('rarities/sort', 'RarityController@postSortRarity');

    // SPECIES
    Route::get('species', 'SpeciesController@getIndex');
    Route::get('species/create', 'SpeciesController@getCreateSpecies');
    Route::get('species/edit/{id}', 'SpeciesController@getEditSpecies');
    Route::get('species/delete/{id}', 'SpeciesController@getDeleteSpecies');
    Route::post('species/create', 'SpeciesController@postCreateEditSpecies');
    Route::post('species/edit/{id?}', 'SpeciesController@postCreateEditSpecies');
    Route::post('species/delete/{id}', 'SpeciesController@postDeleteSpecies');
    Route::post('species/sort', 'SpeciesController@postSortSpecies');
    Route::get('subtypes', 'SpeciesController@getSubtypeIndex');
    Route::get('subtypes/create', 'SpeciesController@getCreateSubtype');
    Route::get('subtypes/edit/{id}', 'SpeciesController@getEditSubtype');
    Route::get('subtypes/delete/{id}', 'SpeciesController@getDeleteSubtype');
    Route::post('subtypes/create', 'SpeciesController@postCreateEditSubtype');
    Route::post('subtypes/edit/{id?}', 'SpeciesController@postCreateEditSubtype');
    Route::post('subtypes/delete/{id}', 'SpeciesController@postDeleteSubtype');
    Route::post('subtypes/sort', 'SpeciesController@postSortSubtypes');

    Route::get('transformations', 'TransformationController@getTransformationIndex');
    Route::get('transformations/create', 'TransformationController@getCreateTransformation');
    Route::get('transformations/edit/{id}', 'TransformationController@getEditTransformation');
    Route::get('transformations/delete/{id}', 'TransformationController@getDeleteTransformation');
    Route::post('transformations/create', 'TransformationController@postCreateEditTransformation');
    Route::post('transformations/edit/{id?}', 'TransformationController@postCreateEditTransformation');
    Route::post('transformations/delete/{id}', 'TransformationController@postDeleteTransformation');
    Route::post('transformations/sort', 'TransformationController@postSortTransformations');

    // ITEMS
    Route::get('item-categories', 'ItemController@getIndex');
    Route::get('item-categories/create', 'ItemController@getCreateItemCategory');
    Route::get('item-categories/edit/{id}', 'ItemController@getEditItemCategory');
    Route::get('item-categories/delete/{id}', 'ItemController@getDeleteItemCategory');
    Route::post('item-categories/create', 'ItemController@postCreateEditItemCategory');
    Route::post('item-categories/edit/{id?}', 'ItemController@postCreateEditItemCategory');
    Route::post('item-categories/delete/{id}', 'ItemController@postDeleteItemCategory');
    Route::post('item-categories/sort', 'ItemController@postSortItemCategory');

    Route::get('items', 'ItemController@getItemIndex');
    Route::get('items/create', 'ItemController@getCreateItem');
    Route::get('items/edit/{id}', 'ItemController@getEditItem');
    Route::get('items/delete/{id}', 'ItemController@getDeleteItem');
    Route::post('items/create', 'ItemController@postCreateEditItem');
    Route::post('items/edit/{id?}', 'ItemController@postCreateEditItem');
    Route::post('items/delete/{id}', 'ItemController@postDeleteItem');

    Route::get('items/delete-tag/{id}/{tag}', 'ItemController@getDeleteItemTag');
    Route::post('items/delete-tag/{id}/{tag}', 'ItemController@postDeleteItemTag');
    Route::get('items/tag/{id}/{tag}', 'ItemController@getEditItemTag');
    Route::post('items/tag/{id}/{tag}', 'ItemController@postEditItemTag');
    Route::get('items/tag/{id}', 'ItemController@getAddItemTag');
    Route::post('items/tag/{id}', 'ItemController@postAddItemTag');

    // PETS
    Route::get('pet-categories', 'PetController@getIndex');
    Route::get('pet-categories/create', 'PetController@getCreatePetCategory');
    Route::get('pet-categories/edit/{id}', 'PetController@getEditPetCategory');
    Route::get('pet-categories/delete/{id}', 'PetController@getDeletePetCategory');
    Route::post('pet-categories/create', 'PetController@postCreateEditPetCategory');
    Route::post('pet-categories/edit/{id?}', 'PetController@postCreateEditPetCategory');
    Route::post('pet-categories/delete/{id}', 'PetController@postDeletePetCategory');
    Route::post('pet-categories/sort', 'PetController@postSortPetCategory');

    Route::get('pets', 'PetController@getPetIndex');
    Route::get('pets/create', 'PetController@getCreatePet');
    Route::get('pets/edit/{id}', 'PetController@getEditPet');
    Route::get('pets/delete/{id}', 'PetController@getDeletePet');
    Route::post('pets/create', 'PetController@postCreateEditPet');
    Route::post('pets/edit/{id?}', 'PetController@postCreateEditPet');
    Route::post('pets/delete/{id}', 'PetController@postDeletePet');

    // variants
    Route::get('pets/edit/{pet_id}/variants/create', 'PetController@getCreateEditVariant');
    Route::get('pets/edit/{pet_id}/variants/edit/{id}', 'PetController@getCreateEditVariant');
    Route::post('pets/edit/{pet_id}/variants/create', 'PetController@postCreateEditVariant');
    Route::post('pets/edit/{pet_id}/variants/edit/{id}', 'PetController@postCreateEditVariant');

    // evolutions
    Route::get('pets/edit/{pet_id}/evolution/create', 'PetController@getCreateEditEvolution');
    Route::get('pets/edit/{pet_id}/evolution/edit/{id}', 'PetController@getCreateEditEvolution');
    Route::post('pets/edit/{pet_id}/evolution/create', 'PetController@postCreateEditEvolution');
    Route::post('pets/edit/{pet_id}/evolution/edit/{id}', 'PetController@postCreateEditEvolution');

    Route::get('pets/drops', 'PetController@getDropIndex');
    Route::get('pets/drops/create', 'PetController@getCreateDrop');
    Route::get('pets/drops/edit/{pet_id}', 'PetController@getEditDrop');
    Route::get('pets/drops/delete/{pet_id}', 'PetController@getDeleteDrop');
    Route::post('pets/drops/create', 'PetController@postCreateEditDrop');
    Route::post('pets/drops/edit/{pet_id}', 'PetController@postCreateEditDrop');
    Route::post('pets/drops/delete/{pet_id}', 'PetController@postDeleteDrop');
    Route::get('pets/drops/widget/{id}', 'PetController@getDropWidget');

    // variants drops
    Route::get('pets/drops/edit/{pet_id}/variants/create', 'PetController@getCreateVariantDrop');
    Route::get('pets/drops/edit/{pet_id}/variants/edit/{variant_id}', 'PetController@getEditVariantDrop');
    Route::post('pets/drops/edit/{pet_id}/variants/create', 'PetController@postCreateEditVariantDrop');
    Route::post('pets/drops/edit/{pet_id}/variants/edit/{variant_id}', 'PetController@postCreateEditVariantDrop');
    Route::get('pets/drops/edit/{pet_id}/variants/delete/{variant_id}', 'PetController@getDeleteVariantDrop');
    Route::post('pets/drops/edit/{pet_id}/variants/delete/{variant_id}', 'PetController@postDeleteVariantDrop');

    // levels
    Route::get('pets/levels', 'PetController@getLevelIndex');
    Route::get('pets/levels/create', 'PetController@getCreateLevel');
    Route::get('pets/levels/edit/{id}', 'PetController@getEditLevel');
    Route::get('pets/levels/delete/{id}', 'PetController@getDeleteLevel');
    Route::post('pets/levels/create', 'PetController@postCreateEditLevel');
    Route::post('pets/levels/edit/{id}', 'PetController@postCreateEditLevel');
    Route::post('pets/levels/delete/{id}', 'PetController@postDeleteLevel');

    // level pets
    Route::get('pets/levels/edit/{level_id}/pets/add', 'PetController@getAddPetToLevel');
    Route::get('pets/levels/edit/{level_id}/pets/edit/{id}', 'PetController@getEditPetLevel');
    Route::post('pets/levels/edit/{level_id}/pets/add', 'PetController@postAddPetToLevel');
    Route::post('pets/levels/edit/{level_id}/pets/edit/{id}', 'PetController@postEditPetLevel');

    // RECIPES
    Route::get('recipes', 'RecipeController@getRecipeIndex');
    Route::get('recipes/create', 'RecipeController@getCreateRecipe');
    Route::get('recipes/edit/{id}', 'RecipeController@getEditRecipe');
    Route::get('recipes/delete/{id}', 'RecipeController@getDeleteRecipe');
    Route::post('recipes/create', 'RecipeController@postCreateEditRecipe');
    Route::post('recipes/edit/{id?}', 'RecipeController@postCreateEditRecipe');
    Route::post('recipes/delete/{id}', 'RecipeController@postDeleteRecipe');

    // SHOPS
    Route::get('shops', 'ShopController@getIndex');
    Route::get('shops/create', 'ShopController@getCreateShop');
    Route::get('shops/edit/{id}', 'ShopController@getEditShop');
    Route::get('shops/delete/{id}', 'ShopController@getDeleteShop');
    Route::post('shops/create', 'ShopController@postCreateEditShop');
    Route::post('shops/edit/{id?}', 'ShopController@postCreateEditShop');
    Route::post('shops/delete/{id}', 'ShopController@postDeleteShop');
    Route::post('shops/sort', 'ShopController@postSortShop');
    Route::post('shops/restrictions/{id}', 'ShopController@postRestrictShop');
    // stock
    // create
    Route::get('shops/stock/{id}', 'ShopController@getCreateShopStock');
    Route::post('shops/stock/{id}', 'ShopController@postCreateShopStock');
    // edit
    Route::get('shops/stock/edit/{id}', 'ShopController@getEditShopStock');
    Route::post('shops/stock/edit/{id}', 'ShopController@postEditShopStock');
    // delete
    Route::get('shops/stock/delete/{id}', 'ShopController@getDeleteShopStock');
    Route::post('shops/stock/delete/{id}', 'ShopController@postDeleteShopStock');
    // misc
    Route::get('shops/stock-type', 'ShopController@getShopStockType');

    // Activities
    Route::get('activities', 'ActivityController@getIndex');
    Route::get('activities/create', 'ActivityController@getCreateActivity');
    Route::get('activities/edit/{id}', 'ActivityController@getEditActivity');
    Route::get('activities/delete/{id}', 'ActivityController@getDeleteActivity');
    Route::post('activities/create', 'ActivityController@postCreateEditActivity');
    Route::post('activities/edit/{id?}', 'ActivityController@postCreateEditActivity');
    Route::post('activities/module/{id}', 'ActivityController@postEditModule');
    Route::post('activities/delete/{id}', 'ActivityController@postDeleteActivity');
    Route::post('activities/sort', 'ActivityController@postSortActivity');

    // stock
    // create
    Route::get('shops/stock/{id}', 'ShopController@getCreateShopStock');
    Route::post('shops/stock/{id}', 'ShopController@postCreateShopStock');
    // edit
    Route::get('shops/stock/edit/{id}', 'ShopController@getEditShopStock');
    Route::post('shops/stock/edit/{id}', 'ShopController@postEditShopStock');
    // delete
    Route::get('shops/stock/delete/{id}', 'ShopController@getDeleteShopStock');
    Route::post('shops/stock/delete/{id}', 'ShopController@postDeleteShopStock');
    // misc
    Route::get('shops/stock-type', 'ShopController@getShopStockType');
    Route::get('shops/stock-cost-type', 'ShopController@getShopStockCostType');

    // FEATURES (TRAITS)
    Route::get('trait-categories', 'FeatureController@getIndex');
    Route::get('trait-categories/create', 'FeatureController@getCreateFeatureCategory');
    Route::get('trait-categories/edit/{id}', 'FeatureController@getEditFeatureCategory');
    Route::get('trait-categories/delete/{id}', 'FeatureController@getDeleteFeatureCategory');
    Route::post('trait-categories/create', 'FeatureController@postCreateEditFeatureCategory');
    Route::post('trait-categories/edit/{id?}', 'FeatureController@postCreateEditFeatureCategory');
    Route::post('trait-categories/delete/{id}', 'FeatureController@postDeleteFeatureCategory');
    Route::post('trait-categories/sort', 'FeatureController@postSortFeatureCategory');

    Route::get('traits', 'FeatureController@getFeatureIndex');
    Route::get('traits/create', 'FeatureController@getCreateFeature');
    Route::get('traits/edit/{id}', 'FeatureController@getEditFeature');
    Route::get('traits/delete/{id}', 'FeatureController@getDeleteFeature');
    Route::get('traits/check-subtype', 'FeatureController@getCreateEditFeatureSubtype');
    Route::get('check-genes', 'CharacterController@getCreateCharacterMyoGenes');
    Route::post('traits/create', 'FeatureController@postCreateEditFeature');
    Route::post('traits/edit/{id?}', 'FeatureController@postCreateEditFeature');
    Route::post('traits/delete/{id}', 'FeatureController@postDeleteFeature');

    Route::get('traits/examples/{feature_id}', 'FeatureController@getFeatureExamples');
    Route::get('traits/examples/{feature_id}/create', 'FeatureController@getCreateEditFeatureExample');
    Route::get('traits/examples/{feature_id}/edit/{id}', 'FeatureController@getCreateEditFeatureExample');
    Route::post('traits/examples/{feature_id}/create', 'FeatureController@postCreateEditExample');
    Route::post('traits/examples/{feature_id}/edit/{id}', 'FeatureController@postCreateEditExample');
    Route::get('traits/examples/delete/{id}', 'FeatureController@getDeleteFeatureExample');
    Route::post('traits/examples/delete/{id}', 'FeatureController@postDeleteFeatureExample');
    Route::post('traits/examples/{feature_id}/sort', 'FeatureController@postSortFeatureExamples');

    // CHARACTER CATEGORIES
    Route::get('character-categories', 'CharacterCategoryController@getIndex');
    Route::get('character-categories/create', 'CharacterCategoryController@getCreateCharacterCategory');
    Route::get('character-categories/edit/{id}', 'CharacterCategoryController@getEditCharacterCategory');
    Route::get('character-categories/delete/{id}', 'CharacterCategoryController@getDeleteCharacterCategory');
    Route::post('character-categories/create', 'CharacterCategoryController@postCreateEditCharacterCategory');
    Route::post('character-categories/edit/{id?}', 'CharacterCategoryController@postCreateEditCharacterCategory');
    Route::post('character-categories/delete/{id}', 'CharacterCategoryController@postDeleteCharacterCategory');
    Route::post('character-categories/sort', 'CharacterCategoryController@postSortCharacterCategory');

    // CHARACTER GENERATIONS
    Route::get('character-generations', 'CharacterGenerationController@getIndex');
    Route::get('character-generations/create', 'CharacterGenerationController@getCreateCharacterGeneration');
    Route::get('character-generations/edit/{id}', 'CharacterGenerationController@getEditCharacterGeneration');
    Route::get('character-generations/delete/{id}', 'CharacterGenerationController@getDeleteCharacterGeneration');
    Route::post('character-generations/create', 'CharacterGenerationController@postCreateEditCharacterGeneration');
    Route::post('character-generations/edit/{id?}', 'CharacterGenerationController@postCreateEditCharacterGeneration');
    Route::post('character-generations/delete/{id}', 'CharacterGenerationController@postDeleteCharacterGeneration');

    // CHARACTER PEDIGREES
    Route::get('character-pedigrees', 'CharacterPedigreeController@getIndex');
    Route::get('character-pedigrees/create', 'CharacterPedigreeController@getCreateCharacterPedigree');
    Route::get('character-pedigrees/edit/{id}', 'CharacterPedigreeController@getEditCharacterPedigree');
    Route::get('character-pedigrees/delete/{id}', 'CharacterPedigreeController@getDeleteCharacterPedigree');
    Route::post('character-pedigrees/create', 'CharacterPedigreeController@postCreateEditCharacterPedigree');
    Route::post('character-pedigrees/edit/{id?}', 'CharacterPedigreeController@postCreateEditCharacterPedigree');
    Route::post('character-pedigrees/delete/{id}', 'CharacterPedigreeController@postDeleteCharacterPedigree');

    // SUB MASTERLISTS
    Route::get('sublists', 'SublistController@getIndex');
    Route::get('sublists/create', 'SublistController@getCreateSublist');
    Route::get('sublists/edit/{id}', 'SublistController@getEditSublist');
    Route::get('sublists/delete/{id}', 'SublistController@getDeleteSublist');
    Route::post('sublists/create', 'SublistController@postCreateEditSublist');
    Route::post('sublists/edit/{id?}', 'SublistController@postCreateEditSublist');
    Route::post('sublists/delete/{id}', 'SublistController@postDeleteSublist');
    Route::post('sublists/sort', 'SublistController@postSortSublist');

    // LOOT TABLES
    Route::get('loot-tables', 'LootTableController@getIndex');
    Route::get('loot-tables/create', 'LootTableController@getCreateLootTable');
    Route::get('loot-tables/edit/{id}', 'LootTableController@getEditLootTable');
    Route::get('loot-tables/delete/{id}', 'LootTableController@getDeleteLootTable');
    Route::get('loot-tables/roll/{id}', 'LootTableController@getRollLootTable');
    Route::post('loot-tables/create', 'LootTableController@postCreateEditLootTable');
    Route::post('loot-tables/edit/{id?}', 'LootTableController@postCreateEditLootTable');
    Route::post('loot-tables/delete/{id}', 'LootTableController@postDeleteLootTable');

    // PROMPTS
    Route::get('prompt-categories', 'PromptController@getIndex');
    Route::get('prompt-categories/create', 'PromptController@getCreatePromptCategory');
    Route::get('prompt-categories/edit/{id}', 'PromptController@getEditPromptCategory');
    Route::get('prompt-categories/delete/{id}', 'PromptController@getDeletePromptCategory');
    Route::post('prompt-categories/create', 'PromptController@postCreateEditPromptCategory');
    Route::post('prompt-categories/edit/{id?}', 'PromptController@postCreateEditPromptCategory');
    Route::post('prompt-categories/delete/{id}', 'PromptController@postDeletePromptCategory');
    Route::post('prompt-categories/sort', 'PromptController@postSortPromptCategory');

    Route::get('prompts', 'PromptController@getPromptIndex');
    Route::get('prompts/create', 'PromptController@getCreatePrompt');
    Route::get('prompts/edit/{id}', 'PromptController@getEditPrompt');
    Route::get('prompts/delete/{id}', 'PromptController@getDeletePrompt');
    Route::post('prompts/create', 'PromptController@postCreateEditPrompt');
    Route::post('prompts/edit/{id?}', 'PromptController@postCreateEditPrompt');
    Route::post('prompts/delete/{id}', 'PromptController@postDeletePrompt');

    // DYNAMIC LIMITS
    Route::get('limits', 'LimitController@getIndex');
    Route::get('limits/create', 'LimitController@getCreateLimit');
    Route::get('limits/edit/{id}', 'LimitController@getEditLimit');
    Route::get('limits/delete/{id}', 'LimitController@getDeleteLimit');
    Route::post('limits/create', 'LimitController@postCreateEditLimit');
    Route::post('limits/edit/{id?}', 'LimitController@postCreateEditLimit');
    Route::post('limits/delete/{id}', 'LimitController@postDeleteLimit');

    // DAILIES
    Route::get('dailies', 'DailyController@getIndex');
    Route::get('dailies/create', 'DailyController@getCreateDaily');
    Route::get('dailies/edit/{id}', 'DailyController@getEditDaily');
    Route::get('dailies/delete/{id}', 'DailyController@getDeleteDaily');
    Route::post('dailies/create', 'DailyController@postCreateEditDaily');
    Route::post('dailies/edit/{id?}', 'DailyController@postCreateEditDaily');
    Route::post('dailies/delete/{id}', 'DailyController@postDeleteDaily');
    Route::post('dailies/sort', 'DailyController@postSortDaily');

    // COLLECTIONS
    Route::get('collections', 'CollectionController@getCollectionIndex');
    Route::get('collections/create', 'CollectionController@getCreateCollection');
    Route::get('collections/edit/{id}', 'CollectionController@getEditCollection');
    Route::get('collections/delete/{id}', 'CollectionController@getDeleteCollection');
    Route::post('collections/create', 'CollectionController@postCreateEditCollection');
    Route::post('collections/edit/{id?}', 'CollectionController@postCreateEditCollection');
    Route::post('collections/delete/{id}', 'CollectionController@postDeleteCollection');

    // categories
    Route::get('collections/collection-categories', 'CollectionController@getCollectionCategoryIndex');
    Route::get('collections/collection-categories/create', 'CollectionController@getCreateCollectionCategory');
    Route::get('collections/collection-categories/edit/{id}', 'CollectionController@getEditCollectionCategory');
    Route::get('collections/collection-categories/delete/{id}', 'CollectionController@getDeleteCollectionCategory');
    Route::post('collections/collection-categories/create', 'CollectionController@postCreateEditCollectionCategory');
    Route::post('collections/collection-categories/edit/{id?}', 'CollectionController@postCreateEditCollectionCategory');
    Route::post('collections/collection-categories/delete/{id}', 'CollectionController@postDeleteCollectionCategory');
    Route::post('collections/collection-categories/sort', 'CollectionController@postSortCollectionCategory');

    // FAQ
    Route::get('faq', 'FaqController@getFaqIndex');
    Route::get('faq/create', 'FaqController@getCreateFaqQuestion');
    Route::get('faq/edit/{id}', 'FaqController@getEditFaqQuestion');
    Route::get('faq/delete/{id}', 'FaqController@getDeleteFaqQuestion');
    Route::post('faq/create', 'FaqController@postCreateEditFaqQuestion');
    Route::post('faq/edit/{id?}', 'FaqController@postCreateEditFaqQuestion');
    Route::post('faq/delete/{id}', 'FaqController@postDeleteFaqQuestion');

    // SCAVENGER HUNTS
    Route::get('hunts', 'HuntController@getHuntIndex');
    Route::get('hunts/create', 'HuntController@getCreateHunt');
    Route::get('hunts/edit/{id}', 'HuntController@getEditHunt');
    Route::get('hunts/delete/{id}', 'HuntController@getDeleteHunt');
    Route::post('hunts/create', 'HuntController@postCreateEditHunt');
    Route::post('hunts/edit/{id?}', 'HuntController@postCreateEditHunt');
    Route::post('hunts/delete/{id}', 'HuntController@postDeleteHunt');

    Route::get('hunts/targets/create/{id}', 'HuntController@getCreateHuntTarget');
    Route::post('hunts/targets/create', 'HuntController@postCreateEditHuntTarget');
    Route::get('hunts/targets/edit/{id}', 'HuntController@getEditHuntTarget');
    Route::post('hunts/targets/edit/{id}', 'HuntController@postCreateEditHuntTarget');
    Route::get('hunts/targets/delete/{id}', 'HuntController@getDeleteHuntTarget');
    Route::post('hunts/targets/delete/{id}', 'HuntController@postDeleteHuntTarget');

    // Criteria
    Route::get('criteria', 'CriterionController@getIndex');
    Route::get('criteria/create', 'CriterionController@getCreateEditCriterion');
    Route::post('criteria/create', 'CriterionController@postCreateEditCriterion');
    Route::get('criteria/edit/{id}', 'CriterionController@getCreateEditCriterion');
    Route::post('criteria/edit/{id}', 'CriterionController@postCreateEditCriterion');
    Route::get('criteria/{id}/step', 'CriterionController@getCreateEditCriterionStep');
    Route::get('criteria/{id}/step/{step_id}', 'CriterionController@getCreateEditCriterionStep');
    Route::post('criteria/{id}/step', 'CriterionController@postCreateEditCriterionStep');
    Route::post('criteria/{id}/step/{step_id}', 'CriterionController@postCreateEditCriterionStep');
    Route::get('criteria/delete/{id}', 'CriterionController@getDeleteCriterion');
    Route::post('criteria/delete/{id}', 'CriterionController@postDeleteCriterion');
    Route::get('criteria/step/delete/{step_id}', 'CriterionController@getDeleteCriterionStep');
    Route::post('criteria/step/delete/{id}', 'CriterionController@postDeleteCriterionStep');
    Route::get('criteria/step/{step_id}/option/{id}', 'CriterionController@getCreateEditCriterionOption');
    Route::get('criteria/step/{step_id}/option', 'CriterionController@getCreateEditCriterionOption');
    Route::post('criteria/step/{step_id}/option', 'CriterionController@postCreateEditCriterionOption');
    Route::post('criteria/step/{step_id}/option/{id}', 'CriterionController@postCreateEditCriterionOption');
    Route::get('criteria/option/delete/{id}', 'CriterionController@getDeleteCriterionOption');
    Route::post('criteria/option/delete/{id}', 'CriterionController@postDeleteCriterionOption');

    Route::get('criteria-defaults', 'CriterionController@getDefaultIndex');
    Route::get('criteria-defaults/create', 'CriterionController@getCreateEditCriterionDefault');
    Route::post('criteria-defaults/create', 'CriterionController@postCreateEditCriterionDefault');
    Route::get('criteria-defaults/edit/{id}', 'CriterionController@getCreateEditCriterionDefault');
    Route::post('criteria-defaults/edit/{id}', 'CriterionController@postCreateEditCriterionDefault');
    Route::get('criteria-defaults/delete/{id}', 'CriterionController@getDeleteCriterionDefault');
    Route::post('criteria-defaults/delete/{id}', 'CriterionController@postDeleteCriterionDefault');
});

// PAGES
Route::group(['prefix' => 'pages', 'middleware' => 'power:edit_pages'], function () {
    Route::get('/', 'PageController@getIndex');
    Route::get('create', 'PageController@getCreatePage');
    Route::get('edit/{id}', 'PageController@getEditPage');
    Route::get('delete/{id}', 'PageController@getDeletePage');
    Route::get('regen/{id}', 'PageController@getRegenPage');
    Route::post('create', 'PageController@postCreateEditPage');
    Route::post('edit/{id?}', 'PageController@postCreateEditPage');
    Route::post('delete/{id}', 'PageController@postDeletePage');
    Route::post('regen/{id}', 'PageController@postRegenPage');
});

// NEWS
Route::group(['prefix' => 'news', 'middleware' => 'power:manage_news'], function () {
    Route::get('/', 'NewsController@getIndex');
    Route::get('create', 'NewsController@getCreateNews');
    Route::get('edit/{id}', 'NewsController@getEditNews');
    Route::get('delete/{id}', 'NewsController@getDeleteNews');
    Route::get('regen/{id}', 'NewsController@getRegenNews');
    Route::post('create', 'NewsController@postCreateEditNews');
    Route::post('edit/{id?}', 'NewsController@postCreateEditNews');
    Route::post('delete/{id}', 'NewsController@postDeleteNews');
    Route::post('regen/{id}', 'NewsController@postRegenNews');
});

// DEV LOGS
Route::group(['prefix' => 'devlogs', 'middleware' => 'power:edit_pages'], function () {
    Route::get('/', 'DevLogsController@getIndex');
    Route::get('create', 'DevLogsController@getCreatedevLogs');
    Route::get('edit/{id}', 'DevLogsController@getEditdevLogs');
    Route::get('delete/{id}', 'DevLogsController@getDeletedevLogs');
    Route::post('create', 'DevLogsController@postCreateEditdevLogs');
    Route::post('edit/{id?}', 'DevLogsController@postCreateEditdevLogs');
    Route::post('delete/{id}', 'DevLogsController@postDeletedevLogs');
});

// SALES
Route::group(['prefix' => 'sales', 'middleware' => 'power:manage_sales'], function () {
    Route::get('/', 'SalesController@getIndex');
    Route::get('create', 'SalesController@getCreateSales');
    Route::get('edit/{id}', 'SalesController@getEditSales');
    Route::get('delete/{id}', 'SalesController@getDeleteSales');
    Route::post('create', 'SalesController@postCreateEditSales');
    Route::post('edit/{id?}', 'SalesController@postCreateEditSales');
    Route::post('delete/{id}', 'SalesController@postDeleteSales');

    Route::get('character/{slug}', 'SalesController@getCharacterInfo');
});

// SITE SETTINGS
Route::group(['prefix' => 'settings', 'middleware' => 'power:edit_site_settings'], function () {
    Route::get('/', 'SettingsController@getIndex');
    Route::post('{key}', 'SettingsController@postEditSetting');
});

// GRANTS
Route::group(['prefix' => 'grants', 'namespace' => 'Users', 'middleware' => 'power:edit_inventories'], function () {
    Route::get('user-currency', 'GrantController@getUserCurrency');
    Route::post('user-currency', 'GrantController@postUserCurrency');

    Route::get('items', 'GrantController@getItems');
    Route::post('items', 'GrantController@postItems');

    Route::get('pets', 'GrantController@getPets');
    Route::post('pets', 'GrantController@postPets');
    Route::get('pets/variants/{id}', 'GrantController@getPetVariants');
    Route::get('pets/evolutions/{id}', 'GrantController@getPetEvolutions');

    Route::get('item-search', 'GrantController@getItemSearch');

    Route::get('recipes', 'GrantController@getRecipes');
    Route::post('recipes', 'GrantController@postRecipes');
});

// PETS
Route::group(['prefix' => 'pets', 'middleware' => 'power:edit_inventories'], function () {
    Route::post('pet/{id}', 'Data\PetController@postEditPetDrop');
});

// EVENT SETTINGS
Route::group(['prefix' => 'event-settings', 'middleware' => 'power:edit_inventories'], function () {
    Route::get('/', 'EventController@getEventSettings');
    Route::get('clear', 'EventController@getClearEventCurrency');
    Route::post('clear', 'EventController@postClearEventCurrency');
    Route::post('teams', 'EventController@postEventTeams');
});

// MASTERLIST
Route::group(['prefix' => 'masterlist', 'namespace' => 'Characters', 'middleware' => 'power:manage_characters'], function () {
    Route::get('create-character', 'CharacterController@getCreateCharacter');
    Route::post('create-character', 'CharacterController@postCreateCharacter');

    Route::get('get-number', 'CharacterController@getPullNumber');

    Route::get('transfers/{type}', 'CharacterController@getTransferQueue');
    Route::get('transfer/{id}', 'CharacterController@getTransferInfo');
    Route::get('transfer/act/{id}/{type}', 'CharacterController@getTransferModal');
    Route::post('transfer/{id}', 'CharacterController@postTransferQueue');

    Route::get('trades/{type}', 'CharacterController@getTradeQueue');
    Route::get('trade/{id}', 'CharacterController@getTradeInfo');
    Route::get('trade/act/{id}/{type}', 'CharacterController@getTradeModal');
    Route::post('trade/{id}', 'CharacterController@postTradeQueue');

    Route::get('create-myo', 'CharacterController@getCreateMyo');
    Route::post('create-myo', 'CharacterController@postCreateMyo');

    Route::get('check-subtype', 'CharacterController@getCreateCharacterMyoSubtype');
    Route::get('get-warnings', 'CharacterController@getContentWarnings');
    Route::get('check-transformation', 'CharacterController@getCreateCharacterMyoTransformation');
});

Route::group(['prefix' => 'character', 'namespace' => 'Characters', 'middleware' => 'power:edit_inventories'], function () {
    Route::post('{slug}/grant', 'GrantController@postCharacterCurrency');
    Route::post('{slug}/grant-items', 'GrantController@postCharacterItems');
});
Route::group(['prefix' => 'character', 'namespace' => 'Characters', 'middleware' => 'power:manage_characters'], function () {
    // IMAGES
    Route::get('{slug}/image', 'CharacterImageController@getNewImage');
    Route::post('{slug}/image', 'CharacterImageController@postNewImage');
    Route::get('image/transformation', 'CharacterImageController@getNewImageTransformation');

    Route::get('image/{id}/traits', 'CharacterImageController@getEditImageFeatures');
    Route::post('image/{id}/traits', 'CharacterImageController@postEditImageFeatures');
    Route::get('image/traits/subtype', 'CharacterImageController@getEditImageSubtype');
    Route::get('image/traits/transformation', 'CharacterImageController@getEditImageTransformation');

    Route::get('breeding-slot/{id}', 'CharacterImageController@getEditBreedingSlot');
    Route::post('breeding-slot/{id}', 'CharacterImageController@postEditBreedingSlot');

    Route::get('image/{id}/notes', 'CharacterImageController@getEditImageNotes');
    Route::post('image/{id}/notes', 'CharacterImageController@postEditImageNotes');

    Route::get('image/{id}/credits', 'CharacterImageController@getEditImageCredits');
    Route::post('image/{id}/credits', 'CharacterImageController@postEditImageCredits');

    Route::get('image/{id}/reupload', 'CharacterImageController@getImageReupload');
    Route::post('image/{id}/reupload', 'CharacterImageController@postImageReupload');

    Route::post('image/{id}/settings', 'CharacterImageController@postImageSettings');

    Route::get('image/{id}/active', 'CharacterImageController@getImageActive');
    Route::post('image/{id}/active', 'CharacterImageController@postImageActive');

    Route::get('image/{id}/delete', 'CharacterImageController@getImageDelete');
    Route::post('image/{id}/delete', 'CharacterImageController@postImageDelete');

    Route::post('{slug}/images/sort', 'CharacterImageController@postSortImages');

    Route::post('image/{id}/colours', 'CharacterImageController@postImageColours');

    // CHARACTER
    Route::get('{slug}/stats', 'CharacterController@getEditCharacterStats');
    Route::post('{slug}/stats', 'CharacterController@postEditCharacterStats');

    Route::get('{slug}/description', 'CharacterController@getEditCharacterDescription');
    Route::post('{slug}/description', 'CharacterController@postEditCharacterDescription');

    Route::get('{slug}/profile', 'CharacterController@getEditCharacterProfile');
    Route::post('{slug}/profile', 'CharacterController@postEditCharacterProfile');

    Route::get('{slug}/delete', 'CharacterController@getCharacterDelete');
    Route::post('{slug}/delete', 'CharacterController@postCharacterDelete');

    Route::post('{slug}/settings', 'CharacterController@postCharacterSettings');

    Route::get('{slug}/breeding-permissions/{id}/use', 'CharacterController@getUseBreedingPermission')->where(['id' => '[0-9]+']);
    Route::post('{slug}/breeding-permissions/{id}/use', 'CharacterController@postUseBreedingPermission')->where(['id' => '[0-9]+']);

    Route::post('{slug}/transfer', 'CharacterController@postTransfer');

    // GENOMES
    Route::get('{slug}/genome/create', 'CharacterController@getCreateCharacterGenome');
    Route::post('{slug}/genome/create', 'CharacterController@postCreateCharacterGenome');
    Route::get('{slug}/genome/{id}', 'CharacterController@getEditCharacterGenome');
    Route::post('{slug}/genome/{id}', 'CharacterController@postEditCharacterGenome');
    Route::get('{slug}/genome/{id}/delete', 'CharacterController@getDeleteCharacterGenome');
    Route::post('{slug}/genome/{id}/delete', 'CharacterController@postDeleteCharacterGenome');

    // LINEAGE
    Route::get('{slug}/lineage', 'CharacterLineageController@getEditCharacterLineage');
    Route::post('{slug}/lineage', 'CharacterLineageController@postEditCharacterLineage');
});
// Might rewrite these parts eventually so there's less code duplication...
Route::group(['prefix' => 'myo', 'namespace' => 'Characters', 'middleware' => 'power:manage_characters'], function () {
    // CHARACTER
    Route::get('{id}/stats', 'CharacterController@getEditMyoStats');
    Route::post('{id}/stats', 'CharacterController@postEditMyoStats');

    Route::get('{id}/description', 'CharacterController@getEditMyoDescription');
    Route::post('{id}/description', 'CharacterController@postEditMyoDescription');

    Route::get('{id}/profile', 'CharacterController@getEditMyoProfile');
    Route::post('{id}/profile', 'CharacterController@postEditMyoProfile');

    Route::get('{id}/delete', 'CharacterController@getMyoDelete');
    Route::post('{id}/delete', 'CharacterController@postMyoDelete');

    Route::post('{id}/settings', 'CharacterController@postMyoSettings');

    Route::post('{id}/transfer', 'CharacterController@postMyoTransfer');

    // GENOMES
    Route::get('{id}/genome/create', 'CharacterController@getCreateMyoGenome');
    Route::post('{id}/genome/create', 'CharacterController@postCreateMyoGenome');
    Route::get('{id}/genome/{gid}', 'CharacterController@getEditMyoGenome');
    Route::post('{id}/genome/{gid}', 'CharacterController@postEditMyoGenome');
    Route::get('{id}/genome/{gid}/delete', 'CharacterController@getDeleteMyoGenome');
    Route::post('{id}/genome/{gid}/delete', 'CharacterController@postDeleteMyoGenome');

    // LINEAGE
    Route::get('{id}/lineage', 'CharacterLineageController@getEditMyoLineage');
    Route::post('{id}/lineage', 'CharacterLineageController@postEditMyoLineage');
});

// RAFFLES
Route::group(['prefix' => 'raffles', 'middleware' => 'power:manage_raffles'], function () {
    Route::get('/', 'RaffleController@getRaffleIndex');
    Route::get('edit/group/{id?}', 'RaffleController@getCreateEditRaffleGroup');
    Route::post('edit/group/{id?}', 'RaffleController@postCreateEditRaffleGroup');
    Route::get('edit/raffle/{id?}', 'RaffleController@getCreateEditRaffle');
    Route::post('edit/raffle/{id?}', 'RaffleController@postCreateEditRaffle');

    Route::get('view/{id}', 'RaffleController@getRaffleTickets');
    Route::post('view/ticket/{id}', 'RaffleController@postCreateRaffleTickets');
    Route::post('view/ticket/delete/{id}', 'RaffleController@postDeleteRaffleTicket');

    Route::get('roll/raffle/{id}', 'RaffleController@getRollRaffle');
    Route::post('roll/raffle/{id}', 'RaffleController@postRollRaffle');
    Route::get('roll/group/{id}', 'RaffleController@getRollRaffleGroup');
    Route::post('roll/group/{id}', 'RaffleController@postRollRaffleGroup');
});

// SUBMISSIONS
Route::group(['prefix' => 'submissions', 'middleware' => 'power:manage_submissions'], function () {
    Route::get('/', 'SubmissionController@getSubmissionIndex');
    Route::get('/{status}', 'SubmissionController@getSubmissionIndex')->where('status', 'pending|approved|rejected');
    Route::get('edit/{id}', 'SubmissionController@getSubmission');
    Route::post('edit/{id}/{action}', 'SubmissionController@postSubmission')->where('action', 'approve|reject|cancel');
});

// CLAIMS
Route::group(['prefix' => 'claims', 'middleware' => 'power:manage_submissions'], function () {
    Route::get('/', 'SubmissionController@getClaimIndex');
    Route::get('/{status}', 'SubmissionController@getClaimIndex')->where('status', 'pending|approved|rejected');
    Route::get('edit/{id}', 'SubmissionController@getClaim');
    Route::post('edit/{id}/{action}', 'SubmissionController@postSubmission')->where('action', 'approve|reject|cancel');
});

// SUBMISSIONS
Route::group(['prefix' => 'gallery', 'middleware' => 'power:manage_submissions'], function () {
    Route::get('/submissions', 'GalleryController@getSubmissionIndex');
    Route::get('/submissions/{status}', 'GalleryController@getSubmissionIndex')->where('status', 'pending|accepted|rejected');
    Route::get('/currency', 'GalleryController@getCurrencyIndex');
    Route::get('/currency/{status}', 'GalleryController@getCurrencyIndex')->where('status', 'pending|valued');
    Route::post('edit/{id}/{action}', 'GalleryController@postEditSubmission')->where('action', 'accept|reject|comment|move|value');
});

// REPORTS
Route::group(['prefix' => 'reports', 'middleware' => 'power:manage_reports'], function () {
    Route::get('/', 'ReportController@getReportIndex');
    Route::get('/{status}', 'ReportController@getReportIndex')->where('status', 'pending|assigned|assigned-to-me|closed');
    Route::get('edit/{id}', 'ReportController@getReport');
    Route::post('edit/{id}/{action}', 'ReportController@postReport')->where('action', 'assign|close');
});

// DESIGN APPROVALS
Route::group(['prefix' => 'designs', 'middleware' => 'power:manage_characters'], function () {
    Route::get('edit/{id}/{action}', 'DesignController@getDesignConfirmation')->where('action', 'cancel|approve|reject');
    Route::post('edit/{id}/{action}', 'DesignController@postDesign')->where('action', 'cancel|approve|reject');
    Route::post('vote/{id}/{action}', 'DesignController@postVote')->where('action', 'approve|reject');
});
Route::get('{type}/{status}', 'DesignController@getDesignIndex')->where('type', 'myo-approvals|design-approvals')->where('status', 'pending|approved|rejected');

// PAIRINGS
Route::group(['prefix' => 'pairings', 'middleware' => 'power:manage_raffles'], function () {
    Route::get('roller', 'PairingController@getRoller');
    Route::post('roller', 'PairingController@postRoll');
});

// LIMITS
Route::group(['prefix' => 'limits', 'middleware' => 'power:manage_data'], function () {
    Route::post('/', 'LimitController@postCreateEditLimits');
});
