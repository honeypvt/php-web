<?php include 'functions.php' ?>

<?php

	if (isset($_GET['id'])) {
		$id = $_GET['id'];
	} else {
		$id = "";
	}
			
	// create array variable to handle error
	$error = array();
	
	// create array variable to store data from database
	$data = array();
			
	if (isset($_POST['submit'])) {
		$process = $_POST['status'];
		$sql_query = "UPDATE tbl_users SET status = ? WHERE id = ?";
			
		$stmt = $connect->stmt_init();
		if ($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			$stmt->bind_param('ss', $process, $id);
			// Execute query
			$stmt->execute();
			// store result 
			$update_result = $stmt->store_result();
			$stmt->close();
		}
			
        // check update result
        if ($update_result) {
            $error['update_data'] = "<div class='alert alert-info corner-radius'>Changes saved...</div>";
        } else {
            $error['update_data'] = "<div class='alert alert-danger corner-radius'>Update Failed</div>";
        }

	}
		
	// get data from reservation table
	$sql_query = "SELECT * FROM tbl_users WHERE id = ?";
		
	$stmt = $connect->stmt_init();
	if ($stmt->prepare($sql_query)) {	
		// Bind your variables to replace the ?s
		$stmt->bind_param('s', $id);
		// Execute query
		$stmt->execute();
		// store result 
		$stmt->store_result();
		$stmt->bind_result($data['id'], 
				$data['user_type'],
				$data['name'],
				$data['email'],
				$data['password'],
				$data['confirm_code'], 
				$data['status'], 
				$data['imageName']
				);
		$stmt->fetch();
		$stmt->close();
	}
		
?>

    <section class="content">

        <ol class="breadcrumb breadcrumb-offset">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="registered-user.php">Registered User</a></li>
            <li class="active">Edit User</a></li>
        </ol>

       <div class="container-fluid" id="fade-in">

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                    <form id="form_validation" method="post" enctype="multipart/form-data">
                    <div class="card corner-radius">
                        <div class="header">
                            <h2>EDIT USER</h2>
                            <div class="header-dropdown m-r--5">
                                <button type="submit" name="submit" class="button button-rounded btn-offset bg-blue waves-effect pull-right">UPDATE</button>
                            </div>
                        </div>
                        <div class="body">
                            
                            <?php echo isset($error['update_data']) ? $error['update_data'] : ''; ?>

                            <div class="row clearfix">

                            <div class="col-sm-12">
                            	<center>
	                            <?php if ($data['imageName'] == NULL) { ?>
	                                <img src="assets/images/ic_user.png" class="rounded-image" height="100px" width="100px"/>
	                            <?php } else { ?>
	                                <img src="upload/avatar/<?php echo $data['imageName'];?>" class="rounded-image" height="100px" width="100px"/>
	                            <?php } ?>
	                            </center>                        	
	                        </div>

                                <div class="form-group col-sm-12">
                                    <div class="form-line">
                                        <div class="font-12">Name</div>
                                        <input type="text" class="form-control" value="<?php echo $data['name']; ?>" disabled />
                                    </div>
                                </div>

                                <div class="form-group col-sm-12">
                                    <div class="form-line">
                                        <div class="font-12">Email</div>
                                        <input type="text" class="form-control" value="<?php echo $data['email']; ?>" disabled />
                                    </div>
                                </div>

				                <div class="form-group col-sm-12">
				                    <div class="font-12">Status</div>
					                    <select class="form-control show-tick" name="status" id="status">	
											<?php if ($data['status'] == 1) { ?>
												<option value="1" selected="selected">Enabled</option>
												<option value="0" >Disabled</option>
											<?php } else { ?>
												<option value="1" >Enabled</option>
												<option value="0" selected="selected">Disabled</option>
											<?php } ?>
										</select>
								</div> 
                                    
                            </div>
                        </div>
                    </div>
                    </form>

                </div>
            </div>
            
        </div>

    </section>