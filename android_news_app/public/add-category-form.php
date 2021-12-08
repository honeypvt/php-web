<?php include_once('functions.php'); ?>

<?php

if (isset($_POST['submit'])) {

    $category_name = $_POST['category_name'];

    // get image info
    $menu_image = $_FILES['category_image']['name'];
    $image_error = $_FILES['category_image']['error'];
    $image_type = $_FILES['category_image']['type'];

    // create array variable to handle error
    $error = array();

    if(empty($category_name)){
        $error['category_name'] = " <span class='label label-danger'>Must Insert!</span>";
    }

    // common image file extensions
    $allowedExts = array("gif", "jpeg", "jpg", "png");

    // get image file extension
    error_reporting(E_ERROR | E_PARSE);
    $extension = end(explode(".", $_FILES["category_image"]["name"]));

    if($image_error > 0) {
        $error['category_image'] = " <span class='font-12 col-red'>You're not insert images!!</span>";
    } else if(!(($image_type == "image/gif") ||
            ($image_type == "image/jpeg") ||
            ($image_type == "image/jpg") ||
            ($image_type == "image/x-png") ||
            ($image_type == "image/png") ||
            ($image_type == "image/pjpeg")) &&
        !(in_array($extension, $allowedExts))){

        $error['category_image'] = " <span class='font-12'>Image type must jpg, jpeg, gif, or png!</span>";
    }

    if(!empty($category_name) && empty($error['category_image'])){

        // create random image file name
        $string = '0123456789';
        $file = preg_replace("/\s+/", "_", $_FILES['category_image']['name']);
        $function = new functions;
        $menu_image = $function->get_random_string($string, 4)."-".date("Y-m-d").".".$extension;

        // upload new image
        $upload = move_uploaded_file($_FILES['category_image']['tmp_name'], 'upload/category/'.$menu_image);

        // insert new data to menu table
        $sql_query = "INSERT INTO tbl_category (category_name, category_image)
                        VALUES(?, ?)";

        $upload_image = $menu_image;
        $stmt = $connect->stmt_init();
        if($stmt->prepare($sql_query)) {
            // Bind your variables to replace the ?s
            $stmt->bind_param('ss',
                $category_name,
                $upload_image
            );
            // Execute query
            $stmt->execute();
            // store result
            $result = $stmt->store_result();
            $stmt->close();
        }

        if($result) {
            $error['add_category'] = "<div class='alert alert-info corner-radius'>Category successfully added...</div>";
        } else {
            $error['add_category'] = "<div class='alert alert-danger corner-radius'>Added Failed</div>";
        }
    }

}

?> 

    <section class="content">

        <ol class="breadcrumb breadcrumb-offset">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="manage-category.php">Manage Category</a></li>
            <li class="active">Add Category</a></li>
        </ol>

       <div class="container-fluid" id="fade-in">

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                	<form id="form_validation" method="post" enctype="multipart/form-data">
                    <div class="card corner-radius">
                        <div class="header">
                            <h2>ADD CATEGORY</h2>

                            <div class="header-dropdown m-r--5">
                                <button type="submit" name="submit" class="button button-rounded btn-offset bg-blue waves-effect pull-right">PUBLISH</button>
                            </div>

                        </div>
                        <div class="body">

                            <?php echo isset($error['add_category']) ? $error['add_category'] : '';?>

                        	<div class="row clearfix">
                                
                                <div>
                                    <div class="form-group form-float col-sm-12">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="category_name" id="category_name" required>
                                            <label class="form-label">Category Name</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="file" name="category_image" id="category_image" class="dropify-image" data-max-file-size="2M" data-allowed-file-extensions="jpg jpeg png gif" required />
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