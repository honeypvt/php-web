<?php 
		if(isset($_GET['id'])) {
			$ID = $_GET['id'];
		}else{
			$ID = "";
		}
			
		// create array variable to handle error
		$error = array();
			
		// create array variable to store data from database
		$data = array();
		
		// get data from reservation table
		$sql_query = "SELECT * FROM tbl_news WHERE nid = ?";
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			$stmt->bind_param('s', $ID);
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result(
					$data['nid'], 
					$data['cat_id'],
					$data['news_title'],
					$data['news_date'],
					$data['news_description'],
					$data['news_image'],
					$data['video_url'],
					$data['video_id'],
					$data['content_type'],
					$data['size'],
					$data['view_count']
					);
			$stmt->fetch();
			$stmt->close();
		}


		$sql_query2 = "SELECT * FROM tbl_news n, tbl_comments c, tbl_users u WHERE n.nid = c.nid AND c.user_id = u.id AND n.nid = '".$_GET['id']."'";
		$hasil = mysqli_query($connect, $sql_query2);

		$sql_comments = "SELECT COUNT(*) as num FROM tbl_news n, tbl_comments c WHERE n.nid = c.nid AND n.nid = '".$_GET['id']."'";
  		$total_comments = mysqli_query($connect, $sql_comments);
  		$total_comments = mysqli_fetch_array($total_comments);
  		$total_comments = $total_comments['num'];
			
	?>

<?php

	$setting_qry    = "SELECT * FROM tbl_settings where id = '1'";
    $setting_result = mysqli_query($connect, $setting_qry);
    $settings_row   = mysqli_fetch_assoc($setting_result);
    $comment_approval    = $settings_row['comment_approval'];

?>	

	<section class="content">

        <ol class="breadcrumb breadcrumb-offset">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="manage-news.php">Manage News</a></li>
            <li class="active">Manage Comments</a></li>
        </ol>

        <div class="container-fluid" id="fade-in">

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                	<form method="post">
                	<div class="card corner-radius">
                        <div class="header">
                            <h2>COMMENT DETAIL</h2>
                        </div>
                        <div class="body">

                        	<div class="row clearfix">
                        	<div class="form-group form-float col-sm-12">
                        		<p>
									<h4>
										<?php echo $data['news_title']; ?>
									</h4>
								</p>
								<p>
									<?php echo $data['news_date']; ?> 
								</p>
								
                	</form>

							<p><b>Comments ( <?php echo $total_comments;?> )</b></p>
							<?php
								$total = 0;
								while ($data2 = mysqli_fetch_array($hasil)) {
							?>
							<div>
							<table>
								<tr>
									<td>
										<b><?php echo $data2['name']; ?></b> 
									</td>
									<td><a href="delete-comment.php?id=<?php echo $data2['comment_id'];?>" onclick="return confirm('Are you sure want to delete this comment?')" ><i class="material-icons">delete</i></a></td>
								</tr>

								<tr>
									<td><?php echo $data2['date_time']; ?></td>
								</tr>

								<tr>
									<td><?php echo $data2['content']; ?></td>
								</tr>

								<tr>
			                        <?php if ($comment_approval == 'yes') { ?>        
				                        <td>
		                                <?php if ($data2['comment_status'] == '1') { ?>
		                                    <span class="label bg-green">APPROVED</span>
		                                <?php } else { ?>
		                                    <span class="label bg-red">PENDING</span>
		                                <?php } ?>														
										</td>
				                        <?php } else if ($comment_approval == 'no') { }
			                        ?>
								</tr>				

							</table>
					            	
				           	</div>
				           	<br>

				            <?php } ?>

							</div>
                        	</div>
                        </div>
                    </div>

                </div>

            </div>
            
        </div>

    </section>