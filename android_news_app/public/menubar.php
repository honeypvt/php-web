<?php include 'includes/config.php'; ?>
<?php include 'includes/languages.php'; ?>
<?php include 'includes/colors.php'; ?>
<?php include 'roles.php'; ?>
<?php include 'variables.php'; ?>

<?php

    $verify_qry    = "SELECT * FROM tbl_license ORDER BY id DESC LIMIT 1";
    $verify_result = mysqli_query($connect, $verify_qry);
    $verify_row   = mysqli_fetch_assoc($verify_result);
    $item_id    = $verify_row['item_id'];

    if ($item_id != $var_item_id) {
        $error =<<<EOF
        <script>
        alert('Please Verify your Purchase Code to Continue Using Admin Panel');
        window.location = 'verify-purchase-code.php';
        </script>
EOF;
        echo $error;
    }

?>

<?php
    $setting_qry    = "SELECT * FROM tbl_settings WHERE id = '1'";
    $setting_result = mysqli_query($connect, $setting_qry);
    $settings_row   = mysqli_fetch_assoc($setting_result);
    $comment_approval    = $settings_row['comment_approval'];

    $sql_count = "SELECT COUNT(*) as num FROM tbl_comments WHERE comment_status = '0' ";
    $total_pending_comment = mysqli_query($connect, $sql_count);
    $total_pending_comment = mysqli_fetch_array($total_pending_comment);
    $total_pending_comment = $total_pending_comment['num'];
?>

<?php

    $username = $_SESSION['user'];
    $sql_query = "SELECT id, username, email FROM tbl_admin WHERE username = ?";
            
    // create array variable to store previous data
    $data = array();
            
    $stmt = $connect->stmt_init();
    if($stmt->prepare($sql_query)) {
        // Bind your variables to replace the ?s
        $stmt->bind_param('s', $username);          
        // Execute query
        $stmt->execute();
        // store result 
        $stmt->store_result();
        $stmt->bind_result(
            $data['id'],
            $data['username'],
            $data['email']
            );
        $stmt->fetch();
        $stmt->close();
    }
            
?>

<!DOCTYPE html>
<html>
 
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title><?php echo $app_name; ?></title>
    <!-- Favicon-->
    <link rel="icon" href="assets/images/favicon.png" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <!-- Bootstrap Core Css -->
    <link href="assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="assets/plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="assets/plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Morris Chart Css-->
    <link href="assets/plugins/morrisjs/morris.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="assets/css/style.css" rel="stylesheet">
    
    <link href="assets/css/custom.css" rel="stylesheet">

    <!-- Wait Me Css -->
    <link href="assets/plugins/sweetalert/sweetalert.css" rel="stylesheet" />

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="assets/css/theme.css" rel="stylesheet" />

    <!-- Bootstrap Material Datetime Picker Css -->
    <link href="assets/css/time-picker.css" rel="stylesheet" />

     <!-- JQuery DataTable Css -->
    <link href="assets/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">
    

    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
    <!-- Latest compiled and minified CSS -->
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css"> -->

    <!-- Sweetalert Css -->
    <link href="assets/plugins/sweetalert/sweetalert.css" rel="stylesheet" />
       <!-- Light Gallery Plugin Css -->
    <link href="assets/plugins/light-gallery/css/lightgallery.css" rel="stylesheet">

    <link href="assets/css/sticky-footer.css" rel="stylesheet">

    <link href="assets/css/dropify.css" type="text/css" rel="stylesheet">

    <?php if ($ENABLE_RTL_MODE == 'true') { ?>
    <link href="assets/css/rtl.css" rel="stylesheet">
    <?php } ?>

    <style type="text/css">
        :root {
            --main-color:<?php echo $colorPrimary; ?>;
            --second-color:<?php echo $colorSecondary; ?>;
        }
    </style>
        
</head>

<body class="theme-blue poppins">

    <!-- Page Loader -->
    <!-- <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader pl-size-xl">
                <div class="spinner-layer pl-blue">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div> -->

    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <!-- <div class="overlay"></div> -->
    <!-- #END# Overlay For Sidebars -->
    <!-- Search Bar -->
<!--     <div class="search-bar">
        <div class="search-icon">
            <i class="material-icons">search</i>
        </div>
        <input type="text" placeholder="START TYPING...">
        <div class="close-search">
            <i class="material-icons">close</i>
        </div>
    </div> -->
    <!-- #END# Search Bar -->
    <!-- Top Bar -->
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                <a href="javascript:void(0);" class="bars"></a>
                <a class="navbar-brand uppercase" href="dashboard.php"><?php echo $app_name; ?></a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="manage-comment.php">
                            <i class="material-icons">comment</i>
                                <?php if ($comment_approval == 'yes') { ?>        
                                    <span class="label-count"><?php echo $total_pending_comment; ?></span>
                                <?php } else if ($comment_approval == 'no') { }                                 
                                ?>
                        </a>
                    </li>
                    <!-- Call Search -->
                    <li><a href="push-notification.php"><i class="material-icons">notifications</i></a></li>
                    <!-- #END# Call Search -->
                    <!-- Notifications -->
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="edit-member.php?id=<?php echo $data['id']; ?>"><i class="material-icons">person</i>Profile</a></li>
                            <li><a href="settings.php"><i class="material-icons">settings</i>Settings</a></li>
                            <li><a href="logout.php"><i class="material-icons">power_settings_new</i>Logout</a></li>
                        </ul>
                    </li>
                    <!-- #END# Notifications -->

                </ul>
            </div>
        </div>
    </nav>
    <!-- #Top Bar -->
    <section>
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <!-- User Info -->
            <div class="user-info">
                <div class="info-container">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                        <img src="assets/images/ic_launcher.png" width="40px" height="40px" />
                    </div>
                    <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo $data['username'] ?>
                    </div>
                    <div class="email"><?php echo $data['email'] ?></div>
                    <div class="btn-group user-helper-dropdown">
                        <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="edit-member.php?id=<?php echo $data['id']; ?>"><i class="material-icons">person</i>Profile</a></li>
                            <li><a href="logout.php"><i class="material-icons">power_settings_new</i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- #User Info -->
            <!-- Menu -->
            <div class="menu">
                <ul class="list">
                    <?php $page = $_SERVER['REQUEST_URI']; ?>
                    <li class="header">MENU</li>
                    <li class="<?php if (strpos($page, 'dashboard.php') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                        <a href="dashboard.php">
                            <i class="material-icons">dashboard</i>
                            <span><?php echo $sb_dashboard; ?></span>
                        </a>
                    </li>

                    <li class="<?php if (strpos($page, 'category.php') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                        <a href="manage-category.php">
                            <i class="material-icons">view_list</i>
                            <span><?php echo $sb_category; ?></span>
                        </a>
                    </li>

                    <li class="<?php if (strpos($page, 'news.php') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                        <a href="manage-news.php">
                            <i class="material-icons">library_books</i>
                            <span><?php echo $sb_news; ?></span>
                        </a>
                    </li>

                    <li class="<?php if (strpos($page, 'notification.php') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                        <a href="push-notification.php">
                            <i class="material-icons">notifications</i>
                            <span><?php echo $sb_notification; ?></span>
                        </a>
                    </li>

                    <li class="<?php if (strpos($page, 'ads.php') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                        <a href="manage-ads.php">
                            <i class="material-icons">monetization_on</i>
                            <span><?php echo $sb_ads; ?></span>
                        </a>
                    </li>

                    <li class="<?php if (strpos($page, 'comment.php') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                        <a href="manage-comment.php">
                            <i class="material-icons">comment</i>
                            <span><?php echo $sb_comment; ?></span>
                        </a>
                    </li>

                    <li class="<?php if (strpos($page, 'user.php') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                        <a href="registered-user.php">
                            <i class="material-icons">verified_user</i>
                            <span><?php echo $sb_user; ?></span>
                        </a>
                    </li>

                    <li class="<?php if (strpos($page, 'member') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                        <a href="members.php">
                            <i class="material-icons">people</i>
                            <span><?php echo $sb_admin; ?></span>
                        </a>
                    </li>

                    <li class="<?php if (strpos($page, 'settings.php') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                        <a href="settings.php">
                            <i class="material-icons">settings</i>
                            <span><?php echo $sb_settings; ?></span>
                        </a>
                    </li>

                    <li class="<?php if (strpos($page, 'license.php') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                        <a href="license.php">
                            <i class="material-icons">vpn_key</i>
                            <span><?php echo $sb_license; ?></span>
                        </a>
                    </li>

                    <li>
                        <a href="logout.php">
                            <i class="material-icons">power_settings_new</i>
                            <span><?php echo $sb_logout; ?></span>
                        </a>
                    </li>

                </ul>
            </div>
            <!-- #Menu -->
            <!-- Footer -->

            <!-- #Footer -->
        </aside>
        <!-- #END# Left Sidebar -->
        
    </section>

    