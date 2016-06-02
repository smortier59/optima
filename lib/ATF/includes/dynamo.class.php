<?php
/*
 * Classe de gestion de la base de données DynamoDB
 * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
 */
require_once __DIR__.'/../libs/vendor/autoload.php';
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Aws\DynamoDb\Exception\ResourceNotFoundException;
use Aws\DynamoDb\Exception\ResourceInUseException;

/*
define('__DynamoDB_TablePrefix__','optima-dev-');
define('__DynamoDB_Key__','AKIAJPDJ3ICRDBO5YDHA');
define('__DynamoDB_Secret__','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');
define('__DynamoDB_Region__','eu-west-1');
*/

class dynamo {
    const NO_REST = true; // Sécurité

    private static $db;

    // Retourne l'instance du client DynamoDB
    public static function db() {
        if (!self::$db) {
            self::$db = DynamoDbClient::factory(array(
                'credentials' => array(
                    "key"=>__DynamoDB_Key__,
                    "secret"=>__DynamoDB_Secret__
                ), 
                'region' => __DynamoDB_Region__ 
            ));
        }
        if (!self::$db) {
            throw new Exception('NoSQL database unreachable !');
        }
        return self::$db;
    }

    // Insertion d'un nouvel item $data dans la $table
    public static function insert($table,array $data) {
        $marshaler = new Marshaler();
        return self::db()->putItem(array(
            'TableName' => __DynamoDB_TablePrefix__.$table,
            'Item' => $marshaler->marshalJson(json_encode((object) array_filter((array) $data)))
        ));
    }

    // Retourne l'objet ayant la $key dans la $table
    public static function select($table,$key) {
//log::logger(__FUNCTION__."($table, $key)",dynamo);
//log::logger(print_r(array(
            'ConsistentRead' => false,
            //'ReturnConsumedCapacity' => 'INDEXES',
            'TableName' => __DynamoDB_TablePrefix__.$table,
            'Key' => array(
                'id' => array(
                    'S' => $key
                )
            )
        ),true),dynamo);
        $items = self::db()->getItem(array(
            'ConsistentRead' => false,
            //'ReturnConsumedCapacity' => 'INDEXES',
            'TableName' => __DynamoDB_TablePrefix__.$table,
            'Key' => array(
                'id' => array(
                    'S' => $key
                )
            )
        ));
//log::logger($items,dynamo);
        if ($items["Item"]) {
            $marshaler = new Marshaler();
            return json_decode($marshaler->unmarshalJson($items["Item"]),true);
        }
    }

    // Créer une nouvelle table prévue pour des token (id,time)
    public static function createTokenTable($table) {
		return self::db()->createTable(array(
			'TableName' => __DynamoDB_TablePrefix__.$table,
			'AttributeDefinitions' => array(
				array(
					'AttributeName' => 'id',
					'AttributeType' => 'S'
				)/*,
				array(
					'AttributeName' => 'time',
					'AttributeType' => 'N'
				)*/
			),
			'KeySchema' => array(
				array(
					'AttributeName' => 'id',
					'KeyType'       => 'HASH'
				)/*,
				array(
					'AttributeName' => 'time',
					'KeyType'       => 'RANGE'
				)*/
			),
			'ProvisionedThroughput' => array(
				'ReadCapacityUnits'  => 5,
				'WriteCapacityUnits' => 5
			)
		));
    }

	// Supprimer une table
	public static function dropTable($table) {
		return self::db()->deleteTable(array('TableName' => $table));
	}

	// Meta-méthode pour gérer le get/set d'un token
	public static function cached($key=NULL, $value=NULL, $ttl=300) {
		$table = "token".'-'.date('Y-m-d');
		if ($value !== NULL) { 
//log::logger(__FUNCTION__." insert($table, ".print_r(array('id'=>$key, 'time'=> time() + $ttl, 'value'=>$value),true),dynamo);
			return self::insert($table, array('id'=>$key, 'time'=> time() + $ttl, 'value'=>$value));
		} elseif ($key !== NULL) {
//log::logger(__FUNCTION__." select($table, $key)",dynamo);
			$data = self::select($table, $key);
//log::logger($data,dynamo);
			if ($data['time'] > time()) {
				return $data['time']; // On retourne 
			}
		}
	}

	// Maintenance (créer table suivante / purger tables expirées)
	public static function tableSchemeMaintenance($table="token") {
		// Créer la table de demain si elle n'existe pas
		$tableDated = $table.'-'.date('Y-m-d', time() + 86400);
		try {
			self::db()->describeTable(array( 'TableName' => __DynamoDB_TablePrefix__.$tableDated ));
		} catch (ResourceNotFoundException $e) {
			// N'existe pas, alors on la créee
			try {
				self::createTokenTable($tableDated);
			} catch (ResourceInUseException $e) {
				echo "Table '$tableDated' en cours de creation...\n";
			}
		}

		// On supprime toutes les tables de ce scheme, sauf J-1 à J+1
		if ($result = self::db()->listTables()) {
			foreach ($result['TableNames'] as $t) {
				if (substr($t,0,strlen(__DynamoDB_TablePrefix__.$table))!=__DynamoDB_TablePrefix__.$table) continue;
				if ($t==__DynamoDB_TablePrefix__.$table.'-'.date('Y-m-d', time() - 86400)) continue; 		// hier
				if ($t==__DynamoDB_TablePrefix__.$table.'-'.date('Y-m-d')) continue; 						// aujourd'hui
				if ($t==__DynamoDB_TablePrefix__.$table.'-'.date('Y-m-d', time() + 86400)) continue; 		// demain
				//echo "dropping ".$t."\n";
				self::dropTable($t);
			}
		}
    }
}