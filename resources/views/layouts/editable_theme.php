<?php
    header("Content-type: text/css; charset: UTF-8"); //interpret as css file

    if ($theme) {
        $titleColor = $theme->themeEditor->title_color;
        $navBarColor = $theme->themeEditor->nav_color; 
        $navTextColor  = $theme->themeEditor->nav_text_color; 
        $headerImageDisplay = $theme->themeEditor->header_image_display;
        $backgroundColor = $theme->themeEditor->background_color; 
        $backgroundImage = $theme->themeEditor->background_image_url; 
        $backgroundSize = $theme->themeEditor->background_size; 
        $mainMarginTop = ($headerImageDisplay == 'none') ? 50 : 0;
        $mainColor = $theme->themeEditor->main_color;  
        $mainTextColor = $theme->themeEditor->main_text_color;  
        $cardColor = $theme->themeEditor->card_color;  
        $cardHeaderColor = $theme->themeEditor->card_header_color;  
        $cardTextColor = $theme->themeEditor->card_text_color;  
        $linkColor = $theme->themeEditor->link_color;  
        $primaryButtonColor = $theme->themeEditor->primary_button_color;  
        $secondaryButtonColor = $theme->themeEditor->secondary_button_color;  

    } else {
        # mimic default lorekeeper behavior
        #navbar colors
        $titleColor = "#ffffff";
        $navBarColor = "#343a40";
        $navTextColor  = "hsla(0,0%,100%,.5)";
        $headerImageDisplay = 'inline';  // none or inline depending on toggle
        $headerImage = '/images/header.png'; // url to uploaded header image
        $backgroundColor = '#ddd';  
        $backgroundImage = ''; 
        $backgroundSize = 'cover';   // cover=smoll repeat, auto=takes whole page width 
        $mainMarginTop = 0;
        $mainColor = '#fff';  
        $mainTextColor = '#000';
        $cardColor = '#fff';
        $cardHeaderColor = "#f1f1f1";
        $cardTextColor = "#000";
        $linkColor = "#000";
        $primaryButtonColor = "#007bff";
        $secondaryButtonColor = "#6c757d";
    }
?>

<style>

/** Style the site header and nav */
.site-header-image{
    display: <?php echo $headerImageDisplay; ?>;
}

#headerNav, .sidebar-header, .sidebar a.active, .sidebar a.active:hover {
    background-color: <?php echo $navBarColor; ?>!important;
    color: <?php echo $navTextColor; ?>!important;
}

.dropdown-item:hover, .sidebar a:hover, .sidebar a:active, .selectize-dropdown .active {
  filter: brightness(115%);
}

.navbar-dark .navbar-nav .nav-link {
    color: <?php echo $navTextColor; ?>!important;
}

.navbar-brand {
    color: <?php echo $titleColor; ?>!important;
}

/** Style the main content + sidebars and make buttons/forms/cards fit */

#main {
    background-image: url('<?php echo $backgroundImage; ?>');
    background-color: <?php echo $backgroundColor; ?>!important;
    background-size: 100% <?php echo $backgroundSize; ?>;
    background-repeat: repeat;
}

.main-content, .sidebar {
    margin-top: <?php echo $mainMarginTop; ?>px;
}

.main-content, .modal-content, 
.sidebar-section, .sidebar-item, .sidebar a:hover, .sidebar a:active, 
option:hover, .form-control, .selectize-input, .selectize-dropdown .active, 
::placeholder, .breadcrumb-item, 
.dropdown-item:hover, .dropdown-item, .dropdown-menu {
    background-color: <?php echo $mainColor; ?>!important;
    color: <?php echo $mainTextColor; ?>!important;
}

/** Style cards */

.card-header {
    background-color: <?php echo $cardHeaderColor; ?>!important;
}

.card, .list-group-item, .nav-tabs .active {
    background-color: <?php echo $cardColor; ?>!important;
    color: <?php echo $cardTextColor; ?>!important;
}

/** Style links&buttons */

a:not(.btn, .navbar-brand, .card-link, .dropdown-item):not(.sidebar-item > a), a strong, .text-muted {
    color: <?php echo $linkColor; ?>!important;
}

.btn-primary, .page-item.active .page-link {
    background-color: <?php echo $primaryButtonColor; ?>!important;
    border-color: <?php echo $primaryButtonColor; ?>!important;
}

.btn-secondary {
    background-color: <?php echo $secondaryButtonColor; ?>!important;
    border-color: <?php echo $secondaryButtonColor; ?>!important;
}

</style>

