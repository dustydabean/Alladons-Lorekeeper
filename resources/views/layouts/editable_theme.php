<?php
    header("Content-type: text/css; charset: UTF-8"); //interpret as css file

    use App\Models\ThemeEditor;


    $defaultThemeId = Settings::get('default_theme');
    if(isset($defaultThemeId) && ThemeEditor::find($defaultThemeId) != null){
        $theme = ThemeEditor::find($defaultThemeId);
        $navBarColor = $theme->nav_color; 
        $navTextColor  = $theme->nav_text_color; 
        $headerImageDisplay = $theme->header_image_display; 
        $headerImage = $theme->header_image_url; 
        $backgroundColor = $theme->background_color; 
        $backgroundImage = $theme->background_image_url; 
        $backgroundSize = $theme->background_size; 
        $mainMarginTop = ($headerImageDisplay == 'none') ? 50 : 0;

    } else {
        # mimic default lorekeeper behavior
        #navbar colors
        $navBarColor = "#343a40"; 
        $navTextColor  = "#ffffff";
        $headerImageDisplay = 'inline';  // none or inline depending on toggle
        $headerImage = '/images/header.png'; // url to uploaded header image
        $backgroundColor = '#ddd';  
        $backgroundImage = ''; 
        $backgroundSize = 'cover';   // cover=smoll repeat, auto=takes whole page width 
        $mainMarginTop = 0;
    }
?>

<style>
.site-header-image{
    display: <?php echo $headerImageDisplay; ?>;
    background-image: url('<?php echo $headerImage; ?>') !important;
}

#headerNav {
    background-color: <?php echo $navBarColor; ?>!important;
}

.navbar-dark .navbar-nav .nav-link, .navbar-brand{
    color: <?php echo $navTextColor; ?>!important;
}

#main {
    background-image: url('<?php echo $backgroundImage; ?>');
    background-color: <?php echo $backgroundColor; ?>!important;
    background-size: 100% <?php echo $backgroundSize; ?>;
    background-repeat: repeat;
}

.main-content, .sidebar {
    margin-top: <?php echo $mainMarginTop; ?>px;
}
</style>

