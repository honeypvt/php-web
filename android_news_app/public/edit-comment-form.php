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
			
	if (isset($_POST['btnSave'])) {
		$process = $_POST['status'];
		$sql_query = "UPDATE tbl_comments SET comment_status = ? WHERE comment_id = ?";
			
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
			//$error['update_data'] = "<br><div class='alert alert-info'>User status successfully changed...</div>";
			$succes =<<<EOF
				<script>
				alert('Comment Status Successfully Changed ...');
				window.location = 'manage-comment.php';
				</script>
EOF;

			echo $succes;
		} else {
			$error['update_data'] = "<br><div class='alert alert-danger'>Update Failed</div>";
		}
	}
		
	// get data from reservation table
	$sql_query = "SELECT * FROM tbl_comments WHERE comment_id = ?";
		
	$stmt = $connect->stmt_init();
	if ($stmt->prepare($sql_query)) {	
		// Bind your variables to replace the ?s
		$stmt->bind_param('s', $id);
		// Execute query
		$stmt->execute();
		// store result 
		$stmt->store_result();
		$stmt->bind_result($data['comment_id'], 
				$data['nid'],
				$data['user_id'],
				$data['date_time'],
				$data['content'],
				$data['comment_status']
				);
		$stmt->fetch();
		$stmt->close();
	}
		
?>

    <section class="content">

        <ol class="breadcrumb breadcrumb-offset">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="manage-comment.php">Manage Comment</a></li>
            <li class="active">Edit Comment</a></li>
        </ol>

       <div class="container-fluid" id="fade-in">

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                    <form id="form_validation" method="post" enctype="multipart/form-data">
                    <div class="card">
                        <div class="header">
                            <h2>EDIT COMMENT</h2>
                                <?php echo isset($error['update_data']) ? $error['update_data'] : ''; ?>
                        </div>
                        <div class="body">

                            <div class="row clearfix">
                                
                                <div class="col-sm-12">

                    <div class="form-group">
                        <div class="font-12">status</div>

                    <select class="form-control show-tick" name="status" id="status">	
						<?php if ($data['comment_status'] == 1) { ?>
							<option value="1" selected="selected">APPROVE</option>
							<option value="0" >PENDING</option>
						<?php } else { ?>
							<option value="1" >APPROVE</option>
							<option value="0" selected="selected">PENDING</option>
						<?php } ?>
					</select>
					</div>

                                    <div class="col-sm-12">
                                         <button class="btn bg-blue waves-effect pull-right" type="submit" name="btnSave">UPDATE</button>
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