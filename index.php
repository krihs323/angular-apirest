<?php

require_once 'vendor/autoload.php';

$app = new \Slim\Slim();

//Conexion a la base de datos
$db = new mysqli('localhost', 'root', '', 'curso_angular4');

//Permitir acceso CORS
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
	die();
}

//Hola mundo dese Slim
$app->get("/pruebas", function() use($app, $db){
    echo "Hola mundo desde Slim";
});

$app->get("/probando", function() use($app){
    echo "Hola desde la ruta Probando";
});

//Listar Productos
$app->get("/productos", function() use($app, $db){
    $sql = "select * from productos order by id desc;";  
    $query = $db->query($sql);

    $productos = array();
    while ($producto = $query->fetch_assoc()) {
        $productos[] = $producto;
    }

    $result = array(
        'estatus'=>'success',
        'code'=>200,
        'data'=>$productos,
    );

    echo json_encode($result);
    
});

//Devolver Producto
$app->get("/producto/:id", function($id) use($app, $db){
    $sql = "select * from productos where id = " .$id;  
    $query = $db->query($sql);

    $result = array(
        'estatus'=>'error',
        'code'=>400,
        'message'=>'producto no disponible',
    );

    if ($query->num_rows==1) {
        $producto = $query->fetch_assoc();
        $result = array(
            'estatus'=>'success',
            'code'=>200,
            'data'=>$producto,
        );
    }

    echo json_encode($result);
    
});

//Borrar Produccto
$app->post("/borrar-producto/:id", function($id) use($app, $db){
    $sql = "delete from productos where id = " .$id;  
    $query = $db->query($sql);

    if ($query) {
        $result = array(
            'estatus'=>'success',
            'code'=>200,
            'message'=>'producto eliminado de la base de datos',
        );
    }else{
        $result = array(
            'estatus'=>'error',
            'code'=>400,
            'message'=>'el producto no se pudo eliminar de la base de datos',
        );
    }

    echo json_encode($result);
    
});


//Actualizar Producto
$app->post("/actualizar-producto/:id", function($id) use($app, $db){


    $json = $app->request->post('json');
    $data = json_decode($json,true);

    $sql = " update productos set nombre =  ".
    "'{$data['nombre']}', descripcion = ".
    "'{$data['descripcion']}', precio = ".
    "'{$data['precio']}', imagen =".
    "'{$data['imagen']}'".
    " where id = " .$id;

    $query = $db->query($sql);

    if ($query) {
        $result = array(
            'estatus'=>'success',
            'code'=>200,
            'message'=>'producto actualizado correctamente',
        );
    }else{
        $result = array(
            'estatus'=>'error',
            'code'=>400,
            'message'=>'el producto no se pudo actualizar',
        );
    }

    echo json_encode($result);
    
});

//Subir fichearo
$app->post("/upload-file", function() use($app, $db){

    
    if (isset($_FILES['uploads'])) {
        echo 'el fichero existe';

        $piramideUploader = new PiramideUploader();
        $upload = $piramideUploader->upload('image',"uploads", "uploads", array('image/jpeg','image/jpg','image/png','image/gif'));
        $file = $piramideUploader->getInfoFile();
        $file_name = $file['complete_name'];


        if (isset($upload) && $upload['uploaded'] == false ) {
            $result = array(
                'estatus'=>'error',
                'code'=>400,
                'message'=>'el archivo no se ha podido subir',
            );
        }else{
            $result = array(
                'estatus'=>'success',
                'code'=>200,
                'message'=>'archivo cargado exitosamente',
                'file_name' => $file_name,
            );
        }
    }
    echo json_encode($result);
});

//Guardar Productos
$app->post("/productos", function() use($app, $db){
    $json = $app->request->post('json');
    $data = json_decode($json,true);

    if(!isset($data['nombre'])){
        $data['nombre']=null;
    }
    if(!isset($data['descripcion'])){
        $data['descripcion']=null;
    }
    if(!isset($data['precio'])){
        $data['precio']=null;
    }
    if(!isset($data['imagen'])){
        $data['imagen']=null;
    }

    $query = " INSERT INTO productos VALUES (NULL, ".
        "'{$data['nombre']}',".
        "'{$data['descripcion']}',".
        "'{$data['precio']}',".
        "'{$data['imagen']}'".
        ")";
    
    // var_dump($query);
    $insert = $db->query($query);

    if($insert){
        $result = array(
            'estatus'=>'success',
            'code'=>200,
            'message'=>'registro insertado correctamente',
        );
    }else{
        $result = array(
            'estatus'=>'error',
            'code'=>400,
            'message'=>'se genero un error al insertar el registro',
        );
    }

    echo json_encode($result);
    
});

$app->run();