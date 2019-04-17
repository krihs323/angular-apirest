<?php

require_once 'vendor/autoload.php';

$app = new \Slim\Slim();

//Conexion a la base de datos

$db = new mysqli('localhost', 'root', '', 'curso_angular4');

$app->get("/pruebas", function() use($app, $db){
    echo "Hola mundo desde Slim";
});

$app->get("/probando", function() use($app){
    echo "Hola desde la ruta Probando";
});

$app->get("/productos", function() use($app, $db){
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