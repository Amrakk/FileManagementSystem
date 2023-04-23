<!-- HOME PAGE
    main functions: 
    -   upload files
    -   delete files/folders
    -   download files/folders
    -   share files/folders
    -   rename files/folders
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
	$storage_root_path = $user_id;
	
	$current_path = $storage_root_path . $dir;

	$url = 'http://localhost/api/storage/get_files_folders?path=' . urlencode($current_path);
	$response = callApi($url, null, "GET");

	$file_list = [];
	if(isset($response['code']) || $response['code'] >= 10) 
	{
		if($response['code'] == 0) $file_list = $response['data'];
		else $error = $response['message'];
	} else $error = "There was an error while processing your request. Please try again later";

	print_r($response);
	$file_contents = loadUserFiles($file_list);
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Cloud Storage</title>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
			<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" 
				  integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
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
				<li class="breadcrumb-item"><a href="?dir=">Home</a></li>
				<?php 
					$dirs = explode('/', $dir);
					$path = '';
					for($i = 1; $i < count($dirs); $i++) {
						$path = $path . '/' . $dirs[$i];
						if($i == count($dirs) - 1) {
							?>
								<li class="breadcrumb-item active"><?= $dirs[$i] ?></li>
							<?php
						}
						else {
							?>
								<li class="breadcrumb-item"><a href="?dir=<?= $path ?>"><?= $dirs[$i] ?></a></li>
							<?php
						}
					}
				?>
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
			<table class="table table-hover border">
				<thead>
				<tr>
					<th>Name</th>
					<th>Type</th>
					<th>Size</th>
					<th>Last modified</th>
					<th>Actions</th>
				</tr>
				</thead>
				<tbody>
					<?php 
						if(gettype($file_contents) == 'string') echo '<tr><td>' . $file_contents . '</td></tr>'; // show dialog for file contents
						else 
						{
							foreach($file_contents as $file) 
							{
								$path = $dir . '/' . $file['name'];
								?>
									<tr>
									<td>
										<i class="<?= $file['icon'] ?>"></i>
										<a href="?dir=<?= $path ?>"><?= $file['name'] ?></a>
									</td>
									<td><?= $file['type'] ?></td>
									<td><?= $file['size'] ?></td>
									<td><?= $file['modified_date'] ?></td>
									<td>
										<i class="fa fa-download action"></i>
										<i class="fa fa-edit action" ></i>
										<i class="fa fa-trash action"></i>
									</td>
									</tr>
								<?php
							}
						}
					?>
				</tbody>
			</table>

			<div class="border rounded mb-3 mt-5 p-3">
				<h4>File upload</h4>
				<form id="upload-form"enctype="multipart/form-data">
				<div class="form-group">
					<div class="custom-file">
						<input type="file" class="custom-file-input" id="customFile" multiple>
						<label class="custom-file-label" for="customFile">Choose file</label>            
					</div>
				</div>
				<p>Người dùng chỉ được upload tập tin có kích thước tối đa là 20 MB.</p>
				<p>Các tập tin thực thi (*.exe, *.msi, *.sh) không được phép upload.</p>
				<p><strong>Yêu cầu nâng cao</strong>: hiển thị progress bar trong quá trình upload.</p>
				<button class="btn btn-success px-5" id="upload-btn">Upload</button>
				</form>
			</div>

			<div class="modal-example my-5">
				<h4>Một số dialog mẫu</h4>
				<p>Nhấn vào để xem kết quả</p>
				<ul>
					<li><a href="#" data-toggle="modal" data-target="#confirm-delete">Confirm delete</a></li>
					<li><a href="#" data-toggle="modal" data-target="#confirm-rename">Confirm rename</a></li>
					<li><a href="#" data-toggle="modal" data-target="#new-file-dialog">New file dialog</a></li>
					<li><a href="#" data-toggle="modal" data-target="#message-dialog">Message Dialog</a></li>
				</ul>
			</div>

		</div>


		<!-- Delete dialog -->
		<div class="modal fade" id="confirm-delete">
		<div class="modal-dialog">
		<div class="modal-content">
			
			<div class="modal-header">
			<h4 class="modal-title">Xóa tập tin</h4>
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<div class="modal-body">
			Bạn có chắc rằng muốn xóa tập tin <strong>image.jpg</strong>
			</div>
		
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Xóa</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Không</button>
			</div>            
		</div>	
		</div>
		</div>


		<!-- Rename dialog -->
		<div class="modal fade" id="confirm-rename">
		<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Đổi tên</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<div class="modal-body">
				<p>Nhập tên mới cho tập tin <strong>Document.txt</strong></p>
				<input type="text" placeholder="Nhập tên mới" value="Document.txt" class="form-control"/>
			</div>
		
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Lưu</button>
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
						<input name="file-name"type="text" placeholder="File name" class="form-control" id="file-name"/>
					</div>
					<div class="form-group">
						<label for="content">Contents</label>
						<textarea name="contents" rows="10" id="file-contents" class="form-control" placeholder="Contents"></textarea>

					</div>
				</div>
			
				<div class="modal-footer">
					
					<button type="submit" class="btn btn-success" id="create-file-btn">Save</button>
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