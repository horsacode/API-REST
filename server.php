<?php

/*if ( !array_key_exists( 'HTTP_X_TOKEN', $_SERVER ) ) {

	die;
}

$url = 'http://localhost:8001';

$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_HTTPHEADER, [
	"X-Token: {$_SERVER['HTTP_X_TOKEN']}",
]);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
$ret = curl_exec( $ch );

if ( curl_errno($ch) != 0 ) {
	die ( curl_error($ch) );
}

if ( $ret !== 'true' ) {
	http_response_code( 403 );

	die;
}*/

// Definimos recursos disponibles
$allowedResourceTypes = [
    'books',
    'authors',
    'genres'
];

// Validamos el recurso
$resourceType = $_GET['resource_type'];

if (!in_array($resourceType, $allowedResourceTypes)){
    http_response_code( 400 );
	echo json_encode(
		[
			'error' => "$resourceType is un unkown",
		]
	);
    die;
}

// Defino los recursos
$books = [
    1 => [
        'titulo' => 'Lo que el viento se llevo',
        'id_autor' => 2,
        'id_genero' => 2
    ],
    2 => [
        'titulo' => 'La Iliada',
        'id_autor' => 1,
        'id_genero' => 1
    ],
    3 => [
        'titulo' => 'La Odisea',
        'id_autor' => 3,
        'id_genero' => 3
    ]
];

header('Content-Type: application/json');

$resourceId = array_key_exists('resource_id', $_GET) ? $_GET['resource_id'] : '';
// Generamos la respuesta
switch($_SERVER['REQUEST_METHOD']){

    case 'GET':
        if(empty($resourceId)){
            echo json_encode($books);
        }else{
            if(array_key_exists($resourceId, $books)){
                echo json_encode($books[$resourceId]);
            }else{
                http_response_code( 404 );

				echo json_encode(
					[
						'error' => 'Book '.$resourceId.' not found :(',
					]
				);
            }
        }
        
        break;
    case 'POST':
        $json = file_get_contents('php://input');

        $books[] = json_decode($json, true);

        //echo array_keys($books)[count($books - 1)];
        //curl.exe --% -X POST -d "{\"titulo\": \"Nuevo Libro\", \"id_autor\":1, \"id_genero\": 2}" http://localhost:8000/books
        echo json_encode($books);
        break;
    case 'PUT':
        // Validamos que el recurso buscado exista
        if(!empty($resourceId) && array_key_exists($resourceId, $books)){
            // Tomamos la entrada cruda
            $json = file_get_contents('php://input');

            $books[$resourceId] = json_decode($json, true);

            // retornamos la coleccion modificada
            //curl.exe --% -X PUT -d "{\"titulo\": \"Nuevo Libro\", \"id_autor\":1, \"id_genero\": 2}" http://localhost:8000/books/1
            echo json_encode($books);
        }
        break;
    case 'DELETE':
        // Validamos que el recurso exista
        if(!empty($resourceId) && array_key_exists($resourceId, $books)){

            unset($books[$resourceId]);
        }
        
        //curl.exe --% -X DELETE http://localhost:8000/books/1                                        {"2":{"titulo":"La Iliada","id_autor":1,"id_genero":1},"3":{"titulo":"La Odisea","id_autor":3,"id_genero":3}}
        echo json_encode($books);
        break;
    default:
        http_response_code( 404 );

        echo json_encode(
            [
                'error' => $method.' not yet implemented :(',
            ]
        );
        break;
}

//curl.exe http://hora:1234@localhost:8000/books/
//curl.exe -H "X-HASH:672594d97467331af282e0ee671110762058091d" -H "X-UID: 1" -H "X-TIMESTAMP: 1641855346" http://localhost:8000/books
//curl.exe -X POST -H "X-Client-Id: 1" -H "X-Secret:SuperSecreto!" http://localhost:8001
//curl.exe -H "X-Token: 0e414a94af9a0c77518c35a9fad072f6de5e8db0" http://localhost:8000/books