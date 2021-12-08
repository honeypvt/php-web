<?php include 'functions.php'; ?>

<?php

	if (isset($_POST['submit'])) {

        $title   = $_POST['title'];
        $message = $_POST['message'];
		$link	 = $_POST['link'];

        $image = $_FILES['image']['name'];
        $image_error = $_FILES['image']['error'];
        $image_type = $_FILES['image']['type'];
				
		// create array variable to handle error
		$error = array();

        if (empty($title)) {
            $error['title'] = " <span class='label label-danger'>Must Insert!</span>";
        }
			
		if (empty($message)) {
			$error['message'] = " <span class='label label-danger'>Must Insert!</span>";
		}

        // common image file extensions
        $allowedExts = array("gif", "jpeg", "jpg", "png");

        // get image file extension
        error_reporting(E_ERROR | E_PARSE);
        $extension = end(explode(".", $_FILES["image"]["name"]));

        if($image_error > 0) {
            $error['image'] = " <span class='font-12 col-red'>This field is required.</span>";
        } else if(!(($image_type == "image/gif") ||
                ($image_type == "image/jpeg") ||
                ($image_type == "image/jpg") ||
                ($image_type == "image/x-png") ||
                ($image_type == "image/png") ||
                ($image_type == "image/pjpeg")) &&
            !(in_array($extension, $allowedExts))) {

            $error['image'] = " <span class='font-12'>Image type must jpg, jpeg, gif, or png!</span>";
        }
			
		if (!empty($title) && !empty($message) && empty($error['image'])) {

            $string = '0123456789';
            $file = preg_replace("/\s+/", "_", $_FILES['image']['name']);
            $function = new functions;
            $image = $function->get_random_string($string, 4)."-".date("Y-m-d").".".$extension;
            $upload = move_uploaded_file($_FILES['image']['tmp_name'], 'upload/notification/'.$image);        	
            $upload_image = $image;

			// insert new data to menu table
			$sql_query = "INSERT INTO tbl_fcm_template (title, message, image, link) VALUES (?, ?, ?, ?)";
					
			$stmt = $connect->stmt_init();
			if ($stmt->prepare($sql_query)) {	
				// Bind your variables to replace the ?s
				$stmt->bind_param('ssss', $title, $message, $upload_image, $link);
				// Execute query
				$stmt->execute();
				// store result 
				$result = $stmt->store_result();
				$stmt->close();
			}

			if($result) {
                $_SESSION['msg'] = "Notification template added successfully...";
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
		    } else {
		        $_SESSION['msg'] = "Failed to add notification template...";
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
		    }
		}
	}

?>

    <section class="content">

        <ol class="breadcrumb breadcrumb-offset">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="push-notification.php">Notification</a></li>
            <li class="active">Add New Notification Template</a></li>
        </ol>

       <div class="container-fluid" id="fade-in">

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                	<form id="form_validation" method="post" enctype="multipart/form-data">
                    <div class="card corner-radius">
                        <div class="header">
                            <h2>ADD NOTIFICATION TEMPLATE</h2>

                            <div class="header-dropdown m-r--5">
                                <button type="submit" name="submit" class="button button-rounded btn-offset bg-blue waves-effect pull-right">PUBLISH</button>
                            </div>
                        </div>
                        <div class="body">

                            <?php if(isset($_SESSION['msg'])) { ?>
                                <div class='alert alert-info corner-radius'>
                                    <?php echo $_SESSION['msg']; ?>
                                </div>
                            <?php unset($_SESSION['msg']); }?>

                        	<div class="row clearfix">
                                
                                <div>
                                    <div class="form-group form-float col-sm-12">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="title" id="title" required>
                                            <label class="form-label">Title</label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group form-float col-sm-12">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="message" id="message" required>
                                            <label class="form-label">Message</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="file" name="image" id="image" class="dropify-image" data-max-file-size="2M" data-allowed-file-extensions="jpg jpeg png gif" required/>
                                        </div>
                                    </div>

                                    <div class="form-group form-float col-sm-12">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="link" id="link" >
                                            <label class="form-label">Url (Optional)</label>
                                        </div>
                                    </div>                                    
                                </div>

                            </div>
                        </div>
                    </div>
                    </form>

                </div>
            </div>
            
        </div>

    </section>