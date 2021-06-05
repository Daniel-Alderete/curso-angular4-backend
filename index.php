<?php

require_once 'vendor/autoload.php';

$app = new \Slim\Slim();

$db = mysqli_connect("localhost", "root", "Pecesenelagu@1968", "curso_angular4");

//HTTP Headers 
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}


$app->get("/pruebas", function() use($app, $db){
	echo "Hello World from Slim PHP";
	var_dump($db);
});

$app->get("/probando", function() use($app){
	echo "JUST ANOTHER TEXT";
});

// Retrieve all products
$app->get("/products", function() use($app, $db){
	$sql = 'SELECT * FROM products ORDER BY id DESC;';
	$query = $db->query($sql);
	$products = array();

	while ($product = $query->fetch_assoc()) {
		$products[] = $product;
	}	

	$result = array(
		'status' => 'success',
		'code' => 200,
		'data' => $products
	);

	echo json_encode($result);
});

// Retrieve one product
$app->get("/products/:id", function($id) use($app, $db){
	$sql = 'SELECT * FROM products WHERE id = '.$id;
	$query = $db->query($sql);

	$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => 'Product not found'
	);
	
	if($query->num_rows == 1) {
		$product = $query->fetch_assoc();
		$result = array(
			'status' => 'success',
			'code' => 200,
			'data' => $product
		);	
	} 	

	echo json_encode($result);
});

// Delete one product
$app->delete("/products/:id", function($id) use($app, $db){
	$sql = 'DELETE FROM products WHERE id = '.$id;
	$query = $db->query($sql);

	$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => 'Product not found'
	);
	
	if($query) {
		$result = array(
			'status' => 'success',
			'code' => 204,
			'message' => 'Product deleted successfully'
		);	
	} else {
		$result = array(
			'status' => 'error',
			'code' => 404,
			'message' => 'An error happened when deleting product'
		);
	}	

	echo json_encode($result);
});


// Update one product
$app->put("/products/:id", function($id) use($app, $db){
	$json = $app->request->post('json');
	$data = json_decode($json, true);

	$sql = "UPDATE products SET ";
	$before = false;

	if(isset($data['name'])) {
		$sql .= "name = '{$data["name"]}'";
		$before = true;
	}

	if(isset($data['description'])) {
		if($before) {
			$sql .= ", ";
		}

		$sql .= "description = '{$data["description"]}'";

		$before = true;
	}

	if(isset($data['prize'])) {
		if($before) {
			$sql .= ", ";
		}
		$sql .= "prize = '{$data["prize"]}'";

		$before = true;
	}

	if(isset($data['image'])) {
		if($before) {
			$sql .= ", ";
		}

		$sql .= "image = '{$data["image"]}'";
	}

	$sql .= " WHERE id = {$id}";

	$query = $db->query($sql);

	if($query) {
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => 'Product updated successfully'
		);	
	} else {
		$result = array(
			'status' => 'error',
			'code' => 404,
			'message' => 'An error happened when updating product'
		);
	}	

	echo json_encode($result);
});

// Load image
$app->post("/upload-image", function() use($app, $db){
	$result = array(
		'status' => 'error',
		'code' => 400,
		'message' => 'An error happened when updating product'
	);

	if(isset($_FILES['uploads'])) {
		$piramideUploader = new PiramideUploader();
		$upload = $piramideUploader->upload('image', "uploads", "uploads", array('image/jpeg', 'image/png', 'image/gif'));
		$file = $piramideUploader->getInfoFile();
		$file_name = $file['complete_name'];

		if(isset($upload) && $upload["uploaded"] == true) {
			$result = array(
				'status' => 'success',
				'code' => 200,
				'message' => 'Image uploaded sucessfully',
				'filename' => $file_name
			);
		}	
	} 

	echo json_encode($result);
});

// Save products
$app->post("/products", function() use($app, $db){
	$json = $app->request->post('json');
	$data = json_decode($json, true);	


	if(!isset($data['name'])){
		$data['name']=null;
	}

	if(!isset($data['description'])){
		$data['description']=null;
	}

	if(!isset($data['prize'])){
		$data['prize']=null;
	}
	
	if(!isset($data['image'])){
		$data['image']=null;
	}

	$query = "INSERT INTO products VALUES(NULL,".
		"'{$data['name']}',".
		"'{$data['description']}',".
		"'{$data['prize']}',".
		"'{$data['image']}'".
		");";

	$insert = $db->query($query);

	$result = array(
		'status' => 'error',
		'code' => 400,
		'message' => 'An error happened when creating product'
	);	

	if($insert){
		$result = array(
			'status' => 'success',
			'code' => 201,
			'message' => 'Product created successfully'
		);	
	}

	echo json_encode($result);
});

	

$app->run();

?>
