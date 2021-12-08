<?php
	include 'functions.php';
	include 'fcm.php';
?>

<?php
    $setting_query = "SELECT * FROM tbl_settings WHERE id = '1'";
    $setting_result = mysqli_query($connect, $setting_query);
    $setting_row = mysqli_fetch_assoc($setting_result);

    // delete selected records
    if(isset($_POST['submit'])) {

        $arr = $_POST['chk_id'];
        $count = count($arr);
        if ($count > 0) {
            foreach ($arr as $nid) {

                $sql_image = "SELECT news_image FROM tbl_news WHERE nid = $nid";
                $img_results = mysqli_query($connect, $sql_image);

                $sql_delete = "DELETE FROM tbl_news WHERE nid = $nid";

                if (mysqli_query($connect, $sql_delete)) {
                    while ($row = mysqli_fetch_assoc($img_results)) {
                        unlink('upload/' . $row['news_image']);
                    }
                    $_SESSION['msg'] = "$count Selected news deleted";
                } else {
                    $_SESSION['msg'] = "Error deleting record";
                }

            }
        } else {
            $_SESSION['msg'] = "Whoops! no news selected to delete";
        }
        header("Location:manage-news.php");
        exit;
    }  

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
			$sql_query = "SELECT n.nid, n.news_title, n.news_image, n.news_date, c.category_name, n.video_id, n.content_type, COUNT(comment_id) as comments_count, n.view_count
					FROM tbl_news n 
						LEFT JOIN tbl_comments r ON n.nid = r.nid 
						LEFT JOIN tbl_category c ON n.cat_id = c.cid
					GROUP BY n.nid  
					ORDER BY n.nid DESC";
		} else {
			$sql_query = "SELECT n.nid, n.news_title, n.news_image, n.news_date, c.category_name, n.video_id, n.content_type, COUNT(comment_id) as comments_count, n.view_count
					FROM tbl_news n 
						LEFT JOIN tbl_comments r ON n.nid = r.nid 
						LEFT JOIN tbl_category c ON n.cat_id = c.cid
					WHERE n.news_title LIKE ? 
					GROUP BY n.nid  
					ORDER BY n.nid DESC";
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
					$data['nid'],
					$data['news_title'],
					$data['news_image'],
					$data['news_date'],
					$data['category_name'],
					$data['video_id'],
					$data['content_type'],
					$data['comments_count'],
					$data['view_count']
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
			$sql_query = "SELECT n.nid, n.news_title, n.news_image, n.news_date, c.category_name, n.video_id, n.content_type, COUNT(comment_id) as comments_count, n.view_count
					FROM tbl_news n 
						LEFT JOIN tbl_comments r ON n.nid = r.nid 
						LEFT JOIN tbl_category c ON n.cat_id = c.cid
					GROUP BY n.nid  
					ORDER BY n.nid DESC LIMIT ?, ?";
		} else {
			$sql_query = "SELECT n.nid, n.news_title, n.news_image, n.news_date, c.category_name, n.video_id, n.content_type, COUNT(comment_id) as comments_count, n.view_count
					FROM tbl_news n 
						LEFT JOIN tbl_comments r ON n.nid = r.nid 
						LEFT JOIN tbl_category c ON n.cat_id = c.cid
					WHERE n.news_title LIKE ? 
					GROUP BY n.nid  
					ORDER BY n.nid DESC LIMIT ?, ?";
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
				$data['nid'],
				$data['news_title'],
				$data['news_image'],
				$data['news_date'],
				$data['category_name'],
				$data['video_id'],
				$data['content_type'],
				$data['comments_count'],
				$data['view_count']
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
            <li class="active">Manage News</a></li>
        </ol>

       <div class="container-fluid" id="fade-in">

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card corner-radius">
                        <div class="header">
                            <h2>MANAGE NEWS</h2>
                            <div class="header-dropdown m-r--5">
                                <a href="add-news.php"><button type="button" class="button button-rounded btn-offset bg-blue waves-effect">ADD NEWS</button></a>
                            </div>
                        </div>

                        <div class="body body-offset table-responsive">

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
													<input type="text" class="form-control" name="keyword" placeholder="Search news...">
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
            <li class="active">Manage News</a></li>
        </ol>

       <div class="container-fluid">

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card corner-radius">
                        <div class="header">
                            <h2>MANAGE NEWS</h2>

                            <div class="header-dropdown m-r--5">
                                <a href="add-news.php"><button type="button" class="button button-rounded btn-offset bg-blue waves-effect">ADD NEWS</button></a>
                            </div>
                        </div>

                        <div class="body body-offset table-responsive">

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
													<input type="text" class="form-control" name="keyword" placeholder="Search news...">
												</div>
											</div>
										</td>
	                        			<td width="1%"><button type="submit" class="btn bg-blue btn-circle waves-effect waves-circle waves-float"><i class="material-icons">search</i></button></td>
	                        		</tr>
	                        	</table>
							</form>

							<form method="post" action="">

								<div style="margin-left: 8px; margin-top: -36px; margin-bottom: 10px;">
									<button type="submit" name="submit" id="submit" class="button button-rounded waves-effect waves-float" onclick="return confirm('Are you sure want to delete all selected news?')">Delete selected items(s)</button>
								</div>
										
								<table class='table table-hover table-striped'>
									<thead>
										<tr>
											<th width="1%">
				                                <div class="demo-checkbox" style="margin-bottom: -15px;">
				                                    <input id="chk_all" name="chk_all" type="checkbox" class="filled-in chk-col-blue" />
				                                    <label for="chk_all"></label>
				                                </div>
				                            </th>
											<th width="35%">News Title</th>
											<th width="1%">Image</th>
											<th width="10%">Date</th>
											<th width="10%">Category</th>
											<th width="5%"><center>Comment</center></th>
											<th width="5%"><center>View</center></th>
											<th width="10%"><center>Type</center></th>
											<th width="15%"><center>Action</center></th>
										</tr>
									</thead>

									<?php 
										while ($stmt_paging->fetch()) { ?>
											<tr>
												<td width="1%">
					                                <div class="demo-checkbox" style="margin-top: 10px;">
					                                <input type="checkbox" name="chk_id[]" id="<?php echo $data['nid'];?>" class="chkbox filled-in chk-col-blue" value="<?php echo $data['nid'];?>"/>
					                                <label for="<?php echo $data['nid'];?>"></label>
					                                </div>
					                            </td>
												<td><?php echo $data['news_title'];?></td>

								            	<td>
								            		<?php
														if ($data['content_type'] == 'youtube') {			
											      	?>
											      		<img src="https://img.youtube.com/vi/<?php echo $data['video_id'];?>/mqdefault.jpg" height="48px" width="60px"/>
											      	<?php } else { ?>
								            			<img src="upload/<?php echo $data['news_image'];?>" height="48px" width="60px"/>
								            		<?php } ?>
								            	</td>

												<td><?php echo $data['news_date'];?></td>
												<td><?php echo $data['category_name'];?></td>
												<td>
													<?php
														if ($data['comments_count'] > 0) {
													?>
													<a href="comment-detail.php?id=<?php echo $data['nid'];?>">
														<center>
															<?php echo $data['comments_count'];?>
														</center>
													</a>
													<?php
														} else {
													?>
														<center>
															<?php echo $data['comments_count'];?>
														</center>
													<?php
														}
													?>
												</td>
												<td><center><?php echo $data['view_count'];?></center></td>
												<td><center>
	                                                <?php if ($data['content_type'] == 'Post') { ?>
	                                                    <span class="label label-rounded bg-blue">NEWS</span>
	                                                 <?php } else { ?>
	                                                    <span class="label label-rounded bg-red">VIDEO</span>
	                                                <?php } ?>	
												</center></td>
												<td><center>

							                        <?php if ($setting_row['providers'] == 'onesignal') { ?>
							                        <a href="send-onesignal-news-notification.php?id=<?php echo $data['nid'];?>">
							                            <i class="material-icons">notifications_active</i>
							                        </a>
							                        <?php } else { ?>
							                        <a href="send-fcm-news-notification.php?id=<?php echo $data['nid'];?>">
							                            <i class="material-icons">notifications_active</i>
							                        </a>
							                        <?php } ?>	

										            <a href="news-detail.php?id=<?php echo $data['nid'];?>">
										                <i class="material-icons">launch</i>
										            </a>

										            <a href="edit-news.php?id=<?php echo $data['nid'];?>">
										                <i class="material-icons">mode_edit</i>
										            </a>
										                        
										            <a href="delete-news.php?id=<?php echo $data['nid'];?>" onclick="return confirm('Are you sure want to delete this News?')" >
										                <i class="material-icons">delete</i>
										            </a></center>
										        </td>
											</tr>
									<?php 
										}
									?>
								</table>

							</form>

							<h4><?php $function->doPages($offset, 'manage-news.php', '', $total_records, $keyword); ?></h4>
							<?php 
								}
							?>
						</div>
                    </div>
                </div>
            </div>
        </div>
    </section>