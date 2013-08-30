<?php
//This is the small db class for make reads and writes for the dynamodb

class DB {

	var $dynamodb;

	function __construct() {
		$this->dynamodb = new AmazonDynamoDB();
	}

	function comparisonOperator($co){
		if($co=="GE"){ $co=AmazonDynamoDB::CONDITION_GREATER_THAN_OR_EQUAL;	}
		elseif($co=="LE"){ $co=AmazonDynamoDB::CONDITION_LESS_THAN_OR_EQUAL;}
		else{ $co=AmazonDynamoDB::CONDITION_EQUAL;}
		return $co;
	}

	function query($table, $hash, $id=0, $co=""){
		$co=$this->comparisonOperator($co);
		$response = $this->dynamodb->query(array(
			'TableName' => $table,
				'HashKeyValue' => array(
					AmazonDynamoDB::TYPE_STRING => $hash
				),
				'RangeKeyCondition' => array(
					'ComparisonOperator' => $co,
					'AttributeValueList' => array(
						array(AmazonDynamoDB::TYPE_NUMBER =>"$id")
					),
				)
			)
		);
		if ($response->isOK()){
			$resultData = json_decode($response->body->to_json(),true);
			return $resultData;
		}else{
			return "";
		}
	}

	function scan($table, $hash="", $id="", $co=""){
		$co=$this->comparisonOperator($co);
		if($hash!="" && $id==""){
			$response = $this->dynamodb->scan(array(
				'TableName' => $table,
				'AttributesToGet' => array('hash', 'id'),
				'ScanFilter' => array(
					'hash' => array(
						'ComparisonOperator' => $co,
						'AttributeValueList' => array(
							array( AmazonDynamoDB::TYPE_STRING => $hash )
						)
					),
				)
			));
		}elseif($hash=="" && $id!=""){
			$response = $this->dynamodb->scan(array(
				'TableName' => $table,
				'AttributesToGet' => array('hash', 'id'),
				'ScanFilter' => array(
					'id' => array(
						'ComparisonOperator' => $co,
						'AttributeValueList' => array(
							array( AmazonDynamoDB::TYPE_NUMBER => "$id" )
						)
					),
				)
			));
		}
		if ($response->isOK()){
			$resultData = json_decode($response->body->to_json(),true);
			return $resultData;
		}else{
			return $response;
		}
	}

	function write($table, $data){
		$response = $this->dynamodb->putItem(array(
			"TableName" => $table,
			"Item" => $data
		));
		if ($response->isOK()){
			return true;
		}else{
			return false;
		}
	}

	function update($table, $hash, $id, $data){
		$response = $this->dynamodb->update_item(array(
			'TableName' => $table,
			'Key' => array(
				'HashKeyElement'  => array('S' =>$hash),
				'RangeKeyElement' => array('N' => "$id"),
			),
			'AttributeUpdates' => $data
		));
		if ($response->isOK()){
			return true;
		}else{
			return false;
		}
	}

}
?>