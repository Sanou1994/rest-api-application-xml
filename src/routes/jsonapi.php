<?php
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Message\ResponseInterface as Response;

    require '../vendor/autoload.php';
   
    function message ($code,$status,$message,$type=null,$object=null) {
        if ($object == null) {
            return array("code" => $code, "status" => $status, "message" => $message);
        } else {
            return array("code" => $code, "status" => $status, "message" => $message, $type => $object);
        }        
    }

    $app = new \Slim\App;
    /**
     * route - CREATE - add voisin - POST method
     */
    $app->post
    (
        '/api/voisin', 
        function (Request $request, Response $old_response) {
            try {
                $params = $request->getQueryParams();                
                $nom = $params['nom'];
                $telephone = $params['telephone'];
                $addresse = $params['addresse'];
                $detail = $params['detail'];
                $favoris = $params['favoris'];
               

                $sql = "insert into T_Voisins (nom,telephone,addresse,detail,favoris) values (:nom,:telephone,:addresse,:detail,:favoris)";

                $db_access = new DBAccess ();
                $db_connection = $db_access->getConnection();

                $statement = $db_connection->prepare($sql);                
                $statement->bindParam(':nom', $nom);
                $statement->bindParam(':telephone', $telephone);
                $statement->bindParam(':addresse', $addresse);
                $statement->bindParam(':detail', $detail);
                $statement->bindParam(':favoris', $favoris);
                $statement->execute();
                
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(200, 'OK', "le voisin est bien ajoute!!!!!.")));
            } catch (Exception $exception) {
                
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(500, 'KO', $exception->getMessage())));
            }

            return $response;
        }
    );

    /**
     * route - READ - get detail voisin by id - GET method
     */
    $app->get
    (
        '/api/voisin/{id}', 
        function (Request $request, Response $old_response) {
            try {
                $id = $request->getAttribute('id');                

                $sql = "select * from T_Voisins where id = :id";

                $db_access = new DBAccess ();
                $db_connection = $db_access->getConnection();

                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();

                $statement = $db_connection->prepare($sql);
                $statement->execute(array(':id' => $id));
                if ($statement->rowCount()) {
                    $voisin = $statement->fetch(PDO::FETCH_OBJ);                    
                    $body->write(json_encode(message(200, 'OK', "Process Successed.", "voisin", $voisin)));
                }
                else
                {
                    $body->write(json_encode(message(513, 'KO', "The voisin with id = '".$id."' has not been found or has already been deleted.")));
                }

                $db_access->releaseConnection();
            } catch (Exception $exception) {
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(500, 'KO', $exception->getMessage())));
            }
            
            return $response;
        }
    );

    /**
     * route - READ - get all voisins - GET method
     */
    $app->get
    (
        '/api/voisins', 
        function (Request $request, Response $old_response) {
            try {
                $sql = "Select nom From T_Voisins";
                $db_access = new DBAccess ();
                $db_connection = $db_access->getConnection();
    
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();

                $statement = $db_connection->query($sql);
                if ($statement->rowCount()) {
                    $voisins = $statement->fetchAll(PDO::FETCH_OBJ);                    
                    $body->write(json_encode(message(200, 'OK', "Process Successed.", "voisins", $voisins)));
                } else {
                    $body->write(json_encode(message(512, 'KO', "No student has been recorded yet.")));
                }

                $db_access->releaseConnection();
            } catch (Exception $exception) {
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(500, 'KO', $exception->getMessage())));
            }
    
            return $response;
        }
    );

  

    /**
     * route - MARQUEUR - marquerF un voisin comme favoris  a travers son  id - MARQUEUR method
     */
    $app->get
    (
        '/api/voisinF/{id}', 
        function (Request $request, Response $old_response) {
            try {
                $id = $request->getAttribute('id');

                $sql = "update T_Voisins set favoris = true  where id = :id";

                $db_access = new DBAccess ();
                $db_connection = $db_access->getConnection();

                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();

                $statement = $db_connection->prepare($sql);
                $statement->execute(array(':id' => $id));

                $body->write(json_encode(message(200, 'OK', "la mise a jour comme favoris s'est bien passee!!!!!.")));
                $db_access->releaseConnection();
            } catch (Exception $exception) {
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(500, 'KO', $exception->getMessage())));
            }

            return $response;
        }
    );


    /**
     * route - MARQUEUR - marquerNF un voisin comme non favoris  a travers son  id - MARQUEUR method
     */
    $app->get
    (
        '/api/voisinNF/{id}', 
        function (Request $request, Response $old_response) {
            try {
                $id = $request->getAttribute('id');

                $sql = "update T_Voisins set favoris = false  where id = :id";

                $db_access = new DBAccess ();
                $db_connection = $db_access->getConnection();

                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();

                $statement = $db_connection->prepare($sql);
                $statement->execute(array(':id' => $id));

                $body->write(json_encode(message(200, 'OK', "la mise a jour comme non favoris s'est bien passee!!!!!.")));
                $db_access->releaseConnection();
            } catch (Exception $exception) {
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(500, 'KO', $exception->getMessage())));
            }

            return $response;
        }
    );

 /**
     * route - READ - get all voisins like favoris - GET method
     */
    $app->get
    (
        '/api/voisinsFavoris', 
        function (Request $request, Response $old_response) {
            try {
                $sql = "Select nom  From T_Voisins where favoris = 1";
                $db_access = new DBAccess ();
                $db_connection = $db_access->getConnection();
    
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();

                $statement = $db_connection->query($sql);
                if ($statement->rowCount()) {
                    $voisins = $statement->fetchAll(PDO::FETCH_OBJ);                    
                    $body->write(json_encode(message(200, 'OK', "Process Successed.", "voisins", $voisins)));
                } else {
                    $body->write(json_encode(message(512, 'KO', "No voisin has been recorded yet.")));
                }

                $db_access->releaseConnection();
            } catch (Exception $exception) {
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(500, 'KO', $exception->getMessage())));
            }
    
            return $response;
        }
    );

    $app->run();
?>