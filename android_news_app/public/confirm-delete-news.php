<?php 

	if (isset($_GET['id'])) {
		$ID = $_GET['id'];
	} else {
		$ID = "";
	}
		
	// get image file from menu table
	$sql_query = "SELECT news_image FROM tbl_news WHERE nid = ?";
			
	$stmt = $connect->stmt_init();
	if ($stmt->prepare($sql_query)) {	
		// Bind your variables to replace the ?s
		$stmt->bind_param('s', $ID);
		// Execute query
		$stmt->execute();
		// store result 
		$stmt->store_result();
		$stmt->bind_result($news_image);
		$stmt->fetch();
		$stmt->close();
	}
			
	// delete image file from directory
	$delete = unlink('upload/'."$news_image");

	// get image file from menu table
	$sql_query = "SELECT video_url FROM tbl_news WHERE nid = ?";
			
	$stmt = $connect->stmt_init();
	if ($stmt->prepare($sql_query)) {	
		// Bind your variables to replace the ?s
		$stmt->bind_param('s', $ID);
		// Execute query
		$stmt->execute();
		// store result 
		$stmt->store_result();
		$stmt->bind_result($video_url);
		$stmt->fetch();
		$stmt->close();
	}
			
	// delete image file from directory
	$delete = unlink('upload/video/'."$video_url");
			
	// delete data from menu table
	$sql_query = "DELETE FROM tbl_news WHERE nid = ?";
			
	$stmt = $connect->stmt_init();
	if ($stmt->prepare($sql_query)) {	
		// Bind your variables to replace the ?s
		$stmt->bind_param('s', $ID);
		// Execute query
		$stmt->execute();
		// store result 
		$delete_result = $stmt->store_result();
		$stmt->close();
	}


	// get image file from table
	$sql_query = "SELECT image_name FROM tbl_news_gallery WHERE nid = ?";

	// create array variable to store menu image
	$image_data = array();

	$stmt_menu = $connect->stmt_init();
	if ($stmt_menu->prepare($sql_query)) {
		// Bind your variables to replace the ?s
		$stmt_menu->bind_param('s', $ID);
		// Execute query
		$stmt_menu->execute();
		// store result
		$stmt_menu->store_result();
		$stmt_menu->bind_result($image_data['image_name']);
	}

	// delete all menu image files from directory
	while ($stmt_menu->fetch()) {
		$image_name = $image_data['image_name'];
		$delete_image = unlink('upload/'."$image_name");
	}

	$stmt_menu->close();

	// delete data from menu table
	$sql_query = "DELETE FROM tbl_news_gallery WHERE nid = ?";

	$stmt = $connect->stmt_init();
	if ($stmt->prepare($sql_query)) {
		// Bind your variables to replace the ?s
		$stmt->bind_param('s', $ID);
		// Execute query
		$stmt->execute();
		// store result
		$delete_news_image_result = $stmt->store_result();
		$stmt->close();
	}


				
	// if delete data success back to reservation page
	if ($delete_result && $delete_news_image_result) {
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		$_SESSION['msg'] = "News deleted successfully...";
		exit;
	}

?>