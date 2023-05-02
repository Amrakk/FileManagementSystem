<!-- HOME PAGE
    main functions: 
    -   share files/folders
-->
<?php
	session_start();
	if (!isset($_SESSION['user_id'])) {
		header("location: http://localhost");
		exit();
	}

   	require_once '../functions.php';

	$user_id = $_SESSION['user_id'];
	$url = 'http://localhost/api/user/get_profile?id=' . $user_id;
	$response = callApi($url, null, "GET");
	if($response['code'] != 0) {
		session_destroy();
		header("location: http://localhost");
		exit();
	}

	$role = $response['data']['role'];
	$name = $response['data']['name'];
	
	$error = '';

	$dir = $_GET['dir'] ?? '';
	$isShared = $_GET['shared'] ?? false;
	$storage_root_path = '';
	if(!$isShared) {
		$storage_root_path = $user_id;
	}
	
	$current_path = $storage_root_path . $dir;

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Cloud Storage</title>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
			<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
			<link rel="stylesheet" href="http://localhost/public/assets/css/home.css">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	
	</head>
	<body>
	<?php //require_once('../includes/header.php'); ?>

		<div class="container">
			<div class="row align-items-center py-5">
				<div class="col-6">
				<h3>File Manager</h3>
				</div>
				<div class="col-6">
				<h5 class="text-right">Welcome, <?= $name ?>, <a class="text-primary" href="http://localhost/auth/logout">Logout</a></h5>
				<input type="hidden" name="user-id" id='user-id' value='<?= $user_id ?>'>
				<input type="hidden" name="role" id='role' value='<?= $role ?>'>
				<input type="hidden" name="current-path" id='current-path' value='<?= $current_path ?>'>
				</div>
			</div>
			<ol class="breadcrumb">
				
			</ol>
			<div class="input-group mb-3">
				<div class="input-group-prepend">
				<span class="input-group-text">
				<span class="fa fa-search"></span>         
				</span>
				</div>
				<input type="text" class="form-control" placeholder="Search">
			</div>
			<div class="btn-group my-3">
				<button type="button" class="btn btn-light border" data-toggle="modal" data-target="#new-folder-dialog"> 
				<i class="fas fa-folder-plus"></i> New folder
				</button>   
				<button type="button" class="btn btn-light border" data-toggle="modal" data-target="#new-file-dialog">
					<i class="fas fa-file"></i> New file
					</button>  
			</div>
			<div id="main-content">

			</div>

			<!-- File upload -->
			<div class="border rounded mb-3 mt-5 p-3">
			<h4>File upload</h4>
			<form id="upload-form" enctype="multipart/form-data">
				<div class="form-group">
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="file-input" multiple>
					<label class="custom-file-label" for="customFile">Choose file</label>            
				</div>
				</div>
				<div class="file-list"></div>
				<p>Người dùng chỉ được upload tập tin có kích thước tối đa là 20 MB.</p>
				<p>Các tập tin thực thi (*.exe, *.msi, *.sh) không được phép upload.</p>
				<p><strong>Yêu cầu nâng cao</strong>: hiển thị progress bar trong quá trình upload.</p>
				<button class="btn btn-success px-5" id="upload-btn">Upload</button>
			</form>
			</div>


		</div>


		<!-- Delete dialog -->
		<div class="modal fade" id="confirm-delete">
		<div class="modal-dialog">
		<div class="modal-content">
			
			<div class="modal-header">
			<h4 class="modal-title">Delete</h4>
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<div class="modal-body">
			Are you sure you want to delete this item? <strong>image.jpg</strong>
			</div>
		
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal" id="delete-btn">Delete</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			</div>            
		</div>	
		</div>
		</div>

		<!-- Rename dialog -->
		<div class="modal fade" id="confirm-rename">
		<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Rename</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<div class="modal-body">
				<p>Rename for <strong>Document.txt</strong></p>
				<input type="text" placeholder="Enter new name" class="form-control" id="new-name"/>
			</div>
		
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" id="rename-btn">Save</button>
			</div>            
		</div>
		</div>
		</div>

		<!-- New folder dialog -->
		<div class="modal fade" id="new-folder-dialog">
		<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Create new folder</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<div class="modal-body">
				<div class="form-group">
					<label for="name">Folder Name</label>
					<input name="folder-name" type="text" placeholder="Folder name" class="form-control" id="folder-name"/>
				</div>
			</div>
		
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="create-folder-btn">Save</button>
			</div>    
		</div>
		</div>
		</div>

		<!-- New file dialog -->
		<div class="modal fade" id="new-file-dialog">
		<div class="modal-dialog">
		<div class="modal-content">
			<form id="new-file-form" method="post">
				<div class="modal-header">
					<h4 class="modal-title">Create new file</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>

				<div class="modal-body">
					<div class="form-group">
						<label for="name">File Name</label>
						<input name="file-name"type="text" placeholder="File name" class="form-control"/>
					</div>
					<div class="form-group">
						<label for="content">Contents</label>
						<textarea name="file-contents" rows="10" class="form-control" placeholder="Contents"></textarea>
					</div>
				</div>
			
				<div class="modal-footer">
				<input type="hidden" name="current-path" value='<?= $current_path ?>'>
					<button type="button" class="btn btn-success" id="create-file-btn">Save</button>
				</div>    
			</form>  
		</div>
		</div>
		</div>

		<!-- Message dialog -->
		<div class="modal fade" id="message-dialog">
		<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="message-title">Title</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<div class="modal-body" id="message-body">
				<p>Message</p>
			</div>
		
			<div class="modal-footer">
				<button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
			</div>            
		</div>
		</div>
		</div>


	<script src="http://localhost/public/assets/js/home.js"></script>
	</body>
</html>