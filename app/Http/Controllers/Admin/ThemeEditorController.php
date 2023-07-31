<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Auth;
use Config;

use App\Models\ThemeEditor;
use App\Services\ThemeEditorManager;

use App\Http\Controllers\Controller;

class ThemeEditorController extends Controller
{
    /**
     * Shows the theme editor index.
     *
     * @param  string  $folder
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getThemeEditor($folder = null)
    {
        return view('admin.theme_editor.themes', [
            'themes' => ThemeEditor::orderBy('name')->paginate(20)
        ]);
    }


    /**
     * Shows the create theme page. 
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateTheme()
    {
        return view('admin.theme_editor.create_edit_theme', [
            'theme' => new ThemeEditor
        ]);
    }
    
    /**
     * Shows the edit theme page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditTheme($id)
    {
        $theme = ThemeEditor::find($id);
        if(!$theme) abort(404);
        return view('admin.theme_editor.create_edit_theme', [
            'theme' => $theme
        ]);
    }

    /**
     * Creates a new theme.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\ThemeEditorManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditTheme(Request $request, ThemeEditorManager $service, $id = null)
    {
        $data = $request->only([
            'name', 'title_color', 'nav_color', 'nav_text_color', 'header_image_display', 'header_image_url', 'background_color', 'background_image_url', 'background_size', 
            'main_color', 'main_text_color', 'card_color', 'card_header_color', 'card_text_color', 'link_color', 'primary_button_color', 'secondary_button_color', 'is_released'
        ]);
        if($id && $service->updateTheme(ThemeEditor::find($id), $data, Auth::user())) {
            flash('Theme updated successfully.')->success();
        }
        else if (!$id && $theme = $service->createTheme($data, Auth::user())) {
            flash('Theme created successfully.')->success();
            return redirect()->to('admin/theme-editor/edit/'.$theme->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }


    /**
     * Deletes a theme.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\PageService  $service
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteTheme(Request $request, ThemeEditorManager $service, $id)
    {
        if($id && $service->deleteTheme(ThemeEditor::find($id))) {
            flash('Theme deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/theme-editor');
    }

}
