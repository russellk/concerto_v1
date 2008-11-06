<?php

class Database {
	static private $instance;

	public function __construct( $dsn, $username=false, $password=false, $driver_options=array() ) {
		if( !self::$instance ) {
			try {
				self::$instance = new PDO( $dsn, $username, $password, $driver_options );
				self::$instance->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			} catch( PDOException $e ) {
				die( "Database Connection Error: " . $e->getMessage() . "<br/>" );
			}
		}
		return self::$instance;
	}
	
	public function instance() {
	    return self::$instance;
	}

    /**
     * QUERIES
     */
	public function query( $sql ) {
		return self::$instance->query( $sql );
	}

	public function queryFetchAllAssoc( $sql ) {
		return self::$instance->query( $sql )->fetchAll( PDO::FETCH_ASSOC );
	}

	public function queryFetchRowAssoc( $sql ) {
		return self::$instance->query( $sql )->fetch( PDO::FETCH_ASSOC );
	}

	public function queryFetchValue( $sql, $column = 0 ) {
		return self::$instance->query( $sql )->fetchColumn( $column );
	}

    /**
     * TRANSACTIONS
     */
	public function beginTransaction() {
		return self::$instance->beginTransaction();
	}

	public function commit() {
		return self::$instance->commit();
	}

	public function rollBack() {
		return self::$PDOInstance->rollBack();
	}

    /**
     * PREPARED STATEMENTS
     */
	public function prepare( $sql, $driver_options = array() ) {
		return self::$instance->prepare( $sql, $driver_options );
	}

	public function exec( $sql ) {
		return self::$instance->exec( $sql );
	}

    /**
     * ERROR FUNCTIONS
     */
	public function errorCode() {
		return self::$instance->errorCode();
	}

	public function errorInfo() {
		return self::$instance->errorInfo();
	}

    /**
     * UTILITY FUNCTIONS
     */
	public function lastInsertId( $name = NULL ) {
		return self::$instance->lastInsertId( $name );
	}

	public function quote( $input, $parameter_type=0 ) {
		return self::$instance->quote( $input, $parameter_type );
	}

	public function getAttribute( $attribute ) {
		return self::$instance->getAttribute( $attribute );
	}

	public function setAttribute( $attribute, $value  ) {
		return self::$instance->setAttribute( $attribute, $value );
	}
}
?>
