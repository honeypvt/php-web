<?php
	include 'functions.php';
?>

	<?php 
		// create object of functions class
		$function = new functions;
		
		// create array variable to store data from database
		$data = array();
		
		if(isset($_GET['keyword'])) {	
			// check value of keyword variable
			$keyword = $function->sanitize($_GET['keyword']);
			$bind_keyword = "%".$keyword."%";
		} else {
			$keyword = "";
			$bind_keyword = $keyword;
		}
			
		if (empty($keyword)) {
			$sql_query = "SELECT cid, category_name, category_image FROM tbl_category ORDER BY cid DESC";
		} else {
			$sql_query = "SELECT cid, category_name, category_image FROM tbl_category WHERE category_name LIKE ? ORDER BY cid DESC";
		}
		
		
		$stmt = $connect->stmt_init();
		if ($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			if (!empty($keyword)) {
				$stmt->bind_param('s', $bind_keyword);
			}
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result( 
					$data['cid'],
					$data['category_name'],
					$data['category_image']
					);
			// get total records
			$total_records = $stmt->num_rows;
		}
			
		// check page parameter
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
						
		// number of data that will be display per page		
		$offset = 10;
						
		//lets calculate the LIMIT for SQL, and save it $from
		if ($page) {
			$from 	= ($page * $offset) - $offset;
		} else {
			//if nothing was given in page request, lets load the first page
			$from = 0;	
		}	
		
		if (empty($keyword)) {
			$sql_query = "SELECT cid, category_name, category_image FROM tbl_category ORDER BY cid DESC LIMIT ?, ?";
		} else {
			$sql_query = "SELECT cid, category_name, category_image FROM tbl_category WHERE category_name LIKE ? ORDER BY cid DESC LIMIT ?, ?";
		}
		
		$stmt_paging = $connect->stmt_init();
		if ($stmt_paging ->prepare($sql_query)) {
			// Bind your variables to replace the ?s
			if (empty($keyword)) {
				$stmt_paging ->bind_param('ss', $from, $offset);
			} else {
				$stmt_paging ->bind_param('sss', $bind_keyword, $from, $offset);
			}
			// Execute query
			$stmt_paging ->execute();
			// store result 
			$stmt_paging ->store_result();
			$stmt_paging->bind_result(
				$data['cid'],
				$data['category_name'],
				$data['category_image']
			);
			// for paging purpose
			$total_records_paging = $total_records; 
		}

		// if no data on database show "No Reservation is Available"
		if ($total_records_paging == 0) {
	
	?>

    <section class="content">

        <ol class="breadcrumb breadcrumb-offset">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li class="active">Manage Category</a></li>
        </ol>

       <div class="container-fluid" id="fade-in">

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card corner-radius">
                        <div class="header">
                            <h2>MANAGE CATEGORY</h2>
                            <div class="header-dropdown m-r--5">
                                <a href="add-category.php"><button type="button" class="button button-rounded btn-offset bg-blue waves-effect">ADD CATEGORY</button></a>
                            </div>
                        </div>

                        <div style="margin-top: -10px;" class="body table-responsive">

                        	<?php if(isset($_SESSION['msg'])) { ?>
	                        	<div class='alert alert-info corner-radius'>
	                        		<?php echo $_SESSION['msg']; ?>
	                        	</div>
                        	<?php unset($_SESSION['msg']); }?>
	                        
	                        <form method="get" id="form_validation">
	                        	<table class='table'>
	                        		<tr>
	                        			<td>
	                        				<div class="form-group form-float">
												<div class="form-line">
													<input type="text" class="form-control" name="keyword" placeholder="Search category...">
												</div>
											</div>
										</td>
	                        			<td width="1%"><button type="submit" class="btn bg-blue btn-circle waves-effect waves-circle waves-float"><i class="material-icons">search</i></button></td>
	                        		</tr>
	                        	</table>
							</form>

							<div class="col-sm-10">Whoops, No data found!</div>

						</div>

                    </div>
                </div>
            </div>
        </div>
    </section>

	<?php 
		// otherwise, show data
		} else {
			$row_number = $from + 1;
	?>

    <section class="content">

        <ol class="breadcrumb breadcrumb-offset">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li class="active">Manage Category</a></li>
        </ol>

       <div class="container-fluid">

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card corner-radius">
                        <div class="header">
                            <h2>MANAGE CATEGORY</h2>
                            <div class="header-dropdown m-r--5">
                                <a href="add-category.php"><button type="button" class="button button-rounded btn-offset bg-blue waves-effect">ADD CATEGORY</button></a>
                            </div>
                        </div>

                        <div style="margin-top: -10px;" class="body table-responsive">

                        	<?php if(isset($_SESSION['msg'])) { ?>
	                        	<div class='alert alert-info corner-radius'>
	                        		<?php echo $_SESSION['msg']; ?>
	                        	</div>
                        	<?php unset($_SESSION['msg']); }?>
	                        
	                        <form method="get" id="form_validation">
	                        	<table class='table'>
	                        		<tr>
	                        			<td>
	                        				<div class="form-group form-float">
												<div class="form-line">
													<input type="text" class="form-control" name="keyword" placeholder="Search category...">
												</div>
											</div>
										</td>
	                        			<td width="1%"><button type="submit" class="btn bg-blue btn-circle waves-effect waves-circle waves-float"><i class="material-icons">search</i></button></td>
	                        		</tr>
	                        	</table>
							</form>
										
							<table class='table table-hover table-striped table-offset'>
								<thead>
									<tr>
										<th>Category Name</th>
										<th>Category Image</th>
										<th width="15%">Action</th>
									</tr>
								</thead>

								<?php 
									while ($stmt_paging->fetch()) { ?>
										<tr>
											<td><?php echo $data['category_name'];?></td>
											<td><img src="upload/category/<?php echo $data['category_image']; ?>" width="48" height="48"/></td>
											<td>
									            <a href="edit-category.php?id=<?php echo $data['cid'];?>">
									                <i class="material-icons">mode_edit</i>
									            </a>
									                        
									            <a href="delete-category.php?id=<?php echo $data['cid'];?>" onclick="return confirm('Are you sure want to delete this category?')" >
									                <i class="material-icons">delete</i>
									            </a>
									        </td>
										</tr>
								<?php 
									}
								?>
							</table>

							<h4><?php $function->doPages($offset, 'manage-category.php', '', $total_records, $keyword); ?></h4>
							<?php 
								}
							?>
						</div>
                    </div>
                </div>
            </div>
        </div>
    </section>