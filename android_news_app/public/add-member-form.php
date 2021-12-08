<?php

	include_once('functions.php'); 

	$error = false;

	if (isset($_POST['submit'])) {

        $username   = $_POST['username'];
		$full_name  = $_POST['full_name'];
		$password   = $_POST['password'];
		$repassword = $_POST['repassword'];
		$email      = $_POST['email'];
		//$role  = $_POST['role'] ? : '102';
        $role   = $_POST['role'];

		if (strlen($username) < 3) {
			$error[] = 'Username is too short!';
		}

		if (empty($password)) {
			$error[] = 'Password can not be empty!';
		}

        if (empty($full_name)) {
            $error[] = 'Full name can not be empty!';
        }

		if ($password != $repassword) {
			$error[] = 'Password does not match!';
		}

		$password = hash('sha256',$username.$password);

		if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
			$error[] = 'Email is not valid!'; 
		}

		// $query = mysqli_query($connect, "SELECT email FROM tbl_admin where email = '$email' ");
		// if(mysqli_num_rows($query) > 0) {
		//     $error[] = 'Email already exists!'; 
		// }

		if (!$error) {

			$sql = "SELECT * FROM tbl_admin WHERE (username = '$username' OR email = '$email');";
            $result = mysqli_query($connect, $sql);
            if (mysqli_num_rows($result) > 0) {

            	$row = mysqli_fetch_assoc($result);

            	if ($username == $row['username']) {
                	$error[] = 'Username already exists!';
            	} 

            	if ($email == $row['email']) {
                	$error[] = 'Email already exists!';
            	}

	        } else {

				$sql = "INSERT INTO tbl_admin (username, password, email, full_name, user_role) VALUES (?, ?, ?, ?, ?)";

				$insert = $connect->prepare($sql);
				$insert->bind_param('sssss', $username, $password, $email, $full_name, $role);
				$insert->execute();

                $_SESSION['msg'] = "User added successfully...";
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
			}
		}
	}

?>

    <section class="content">

        <ol class="breadcrumb breadcrumb-offset">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="members.php">Manage Administrator</a></li>
            <li class="active">Add New Administrator</a></li>
        </ol>

       <div class="container-fluid" id="fade-in">

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                	<form id="form_validation" method="post">
                    <div class="card corner-radius">
                        <div class="header">
                            <h2>ADD NEW ADMINISTRATOR</h2>
                            <div class="header-dropdown m-r--5">
                                <button type="submit" name="submit" class="button button-rounded btn-offset bg-blue waves-effect pull-right">PUBLISH</button>
                            </div>
                        </div>
                        <div class="body">

                            <?php echo $error ? '<div class="alert alert-warning corner-radius">'. implode('<br>', $error) . '</div>' : '';?>

                            <?php if(isset($_SESSION['msg'])) { ?>
                                <div class='alert alert-info corner-radius'>
                                    <?php echo $_SESSION['msg']; ?>
                                </div>
                            <?php unset($_SESSION['msg']); }?>

                        	<div class="row clearfix">
                                
                                <div>
                                    <div class="form-group form-float col-sm-12">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="username" id="username" required>
                                            <label class="form-label">Username</label>
                                        </div>
                                    </div>

                                    <div class="form-group form-float col-sm-12">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="full_name" id="full_name" required>
                                            <label class="form-label">Full Name</label>
                                        </div>
                                    </div>

                                    <div class="form-group form-float col-sm-12">
                                        <div class="form-line">
                                            <input type="email" class="form-control" name="email" id="email" required>
                                            <label class="form-label">Email</label>
                                        </div>
                                    </div>

                                    <div class="form-group form-float col-sm-12">
                                        <div class="form-line">
                                            <input type="password" class="form-control" name="password" id="password" required>
                                            <label class="form-label">Password</label>
                                        </div>
                                    </div>

                                    <div class="form-group form-float col-sm-12">
                                        <div class="form-line">
                                            <input type="password" class="form-control" name="repassword" id="repassword" required>
                                            <label class="form-label">Re Password</label>
                                        </div>
                                    </div>

                                    <input type="hidden" name="role" id="role" value="100" />      
                                    
                                </div>

                            </div>
                        </div>
                    </div>
                    </form>

                </div>
            </div>
            
        </div>

    </section>