<?php include('session.php'); ?>
<?php include('public/menubar.php'); ?>

<?php

	if (isset($_GET['id'])) {
		$ID = $_GET['id'];
	} else {
		$ID = "";
	}

    // create array variable to handle error
    $error = array();
      
    $sql    = "SELECT n.*, c.category_name FROM tbl_news n, tbl_category c WHERE n.cat_id = c.cid AND nid = $ID";
    $result = mysqli_query($connect, $sql);
    $data   = mysqli_fetch_assoc($result);

    $protocol = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://';
    $server_name = $_SERVER['SERVER_NAME'];

    $localhost_link = $protocol."10.0.2.2".dirname($_SERVER['REQUEST_URI']);
    $actual_link =  $protocol.$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI']);

  	$onesignal_app_id = $settings_row['onesignal_app_id']; 
  	$onesignal_rest_api_key = $settings_row['onesignal_rest_api_key'];

  	define("ONESIGNAL_APP_ID", $onesignal_app_id);
  	define("ONESIGNAL_REST_KEY", $onesignal_rest_api_key);
 

  	if (isset($_POST['submit'])) {

	    $title = $_POST["title"];
	    $message = $_POST["message"];
        $post_id = $_POST['post_id'];
	    $link = $_POST['link'];
	    $image = $_POST["image"];
	    $unique_id = rand(1000, 9999);
		
        if ($data['content_type'] == 'youtube') {
            $big_image = 'https://img.youtube.com/vi/'.$data['video_id'].'/mqdefault.jpg';
        } else {
            if ($server_name == 'localhost') {
                $big_image = $localhost_link.'/upload/'.$image;  
            } else {
                $big_image = $actual_link.'/upload/'.$image;  
            }
        }

        $content = array("en" =>  $message);

        $fields = array(
        	'app_id' => ONESIGNAL_APP_ID,
        	'included_segments' => array('All'),                                            
        	'data' => array(
        		"foo" => "bar",
        		"link" => $link,
        		"post_id" => $post_id,
        		"unique_id" => $unique_id
        	),
        	'headings'=> array("en" => $title),
        	'contents' => $content,
        	'big_picture' => $big_image         
        );

        $fields = json_encode($fields);
        print("\nJSON sent:\n");
        print($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                   'Authorization: Basic '.ONESIGNAL_REST_KEY));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
        
        $_SESSION['msg'] = "Push notification sent...";
        header("Location:manage-news.php");
        exit; 

  }
  
?>

	<section class="content">

        <ol class="breadcrumb breadcrumb-offset">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="push-notification.php">Manage Notification</a></li>
            <li class="active">Send Notification</a></li>
        </ol>

        <div class="container-fluid" id="fade-in">

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                	<form method="post" enctype="multipart/form-data">
	                	<div class="card corner-radius">
	                        <div class="header">
	                            <h2>SEND NOTIFICATION</h2>
	                            <div class="header-dropdown m-r--5">
	                                <button type="submit" name="submit" class="button button-rounded btn-offset bg-blue waves-effect">SEND NOW</button>
	                            </div>
	                        </div>
	                        <div class="body">

	                        	<div class="row clearfix">

			                        <div class="form-group col-sm-12">
			                            <div class="font-12">Title *</div>
			                            <div class="form-line">
			                                <input type="text" class="form-control" name="title" id="title" placeholder="Title" value="<?php echo $data['category_name']; ?>" required>
			                            </div>
			                       	</div>

			                       	<div class="form-group col-sm-12">
			                            <div class="font-12">Message *</div>
			                            <div class="form-line">
			                                <input type="text" class="form-control" name="message" id="message" placeholder="Message" value="<?php echo $data['news_title']; ?>" required>
			                            </div>
			                       	</div>

			                       	<div class="col-sm-6">
                                        <div class="font-12 ex1">Image *</div>
                                        <div class="form-group">
                                            <?php if ($data['content_type'] == 'youtube') { ?>
                                                <input type="file" class="dropify-image" data-max-file-size="3M" data-allowed-file-extensions="jpg jpeg png gif" data-default-file="https://img.youtube.com/vi/<?php echo $data['video_id'];?>/mqdefault.jpg" data-show-remove="false" disabled/>
                                                <input type="hidden" name="image" value="https://img.youtube.com/vi/<?php echo $data['video_id'];?>/mqdefault.jpg">
                                            <?php } else { ?>
                                                <input type="file" class="dropify-image" data-max-file-size="1M" data-allowed-file-extensions="jpg jpeg png gif" data-default-file="upload/<?php echo $data['news_image']; ?>" data-show-remove="false" disabled/>
                                              <input type="hidden" name="image" value="<?php echo $data['news_image']; ?>">
                                            <?php } ?>
                                        </div>
                                    </div>
                                    
                                    <input type="hidden" name="post_id" id="post_id" value="<?php echo $data['nid']; ?>" />
			                       	<input type="hidden" name="link" id="link" value="" />
										
		                       	</div>
		                    </div>
		                </div>
                	</form>
                </div>
            </div>
        </div>

    </section>

<?php include('public/footer.php'); ?>