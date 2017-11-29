<?php
namespace Model {
	use Utils\Database;

	/**
	 * Class Model
	 * @property int id
	 * @package Model
	 */
	class Model {
		/**
		 * Database connection
		 * @var null|Database
		 */
		protected $db;
		/**
		 * Order by (eg: date DESC, name ASC)
		 * @var string
		 */
		protected $order_by = '';
		/**
		 * Table name (eg: users)
		 * @var string
		 */
		protected $tableName = '';
		/**
		 * Limit rows (eg: 0, 10)
		 * @var string
		 */
		protected $limit = '';
		/**
		 * Define an array of conditions to met (eg:
		 * Select all the users that have one purchase at least with a value higher than 1000
		 *		array(
		 *			'EXISTS(SELECT * FROM `purchases` WHERE `user` = `users`.`id`  AND `value` > 1000 LIMIT 1)' => 1
		 *		)
		 * )
		 * @var array
		 */
		protected $where = array();
		/**
		 * Group by (eg: date)
		 * @var string
		 */
		protected $group_by = '';
		/**
		 * Define an array of custom fields to select (eg:
		 * 	As an array of parameters
		 * 		array(
		 * 			'DATE_FORMAT(created, ?) AS date', 's', '%d.%m.%Y'
		 * 		)
		 *	Or as a string:
		 * 		'SUM(IF(status = 1, 1, 0)) AS confirmed'
		 * )
		 * @var array
		 */
		protected $customFields = array();
		/**
		 * Table schema
		 * @var array
		 */
		public $schema = array();

		/**
		 * Model constructor.
		 * @param string $tableName
		 * @param bool|array $schema
		 */
		public function __construct($tableName, $schema = false) {
			//Connect to database
			$this->db = Database::getInstance();
			if(!$this->db) die('Can not connect to database!');
			//Set charset to UTF-8
			if ($this->db->server_version < 50500) @$this->db->set_charset('utf8');
			//Use the first parameter as the table name
			$this->tableName = $tableName;
			//If the second parameter isn't set then get the table schema
			if($schema === false) $this->getSchema();
			else $this->schema = $schema;
		}

		/**
		 * @param string $string String to be escaped
		 * @return string
		 */
		public function escape($string) {
			return $this->db->real_escape_string($string);
		}

		/**
		 * @param string $name
		 * @return string|int|double|null
		 */
		public function __get($name) {
			return (property_exists($this, $name)) ? $this->$name : null;
		}

		/**
		 * @param string $name
		 * @param $value
		 *
		 * @return $this
		 */
		public function __set($name, $value) {
			$this->$name = $value;
			return $this;
		}

		/**
		 * @return $this
		 */
		private function getSchema() {
			$cache = (extension_loaded('Memcached'))?\Utils\Memcached::getInstance():false;
			if($cache && $buffer = $cache->get(_APP_NAME_ . 'schema' . $this->tableName) && !empty($buffer)) {
				$this->schema = json_decode($buffer, true);
				return $this;
			}
			$schema = array();
			$sql = sprintf(/** @lang text */ "SELECT `COLUMNS`.`COLUMN_NAME` AS `column`, `COLUMN_DEFAULT` AS `default`, `IS_NULLABLE` AS `null`, `DATA_TYPE`, `EXTRA` AS `extra`, `REFERENCED_TABLE_NAME` AS `table_reference`, `REFERENCED_COLUMN_NAME` AS `column_reference`, (SELECT GROUP_CONCAT(`COLUMN_NAME` SEPARATOR ',') FROM `INFORMATION_SCHEMA`.`COLUMNS` `c` WHERE `c`.`TABLE_SCHEMA` LIKE '%s' AND `TABLE_NAME` = `REFERENCED_TABLE_NAME`) AS `trc` FROM `INFORMATION_SCHEMA`.`COLUMNS` LEFT OUTER JOIN `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE` ON `KEY_COLUMN_USAGE`.`TABLE_SCHEMA` = `COLUMNS`.`TABLE_SCHEMA` AND `KEY_COLUMN_USAGE`.`TABLE_NAME` = `COLUMNS`.`TABLE_NAME` AND `KEY_COLUMN_USAGE`.`COLUMN_NAME` = `COLUMNS`.`COLUMN_NAME` WHERE `COLUMNS`.`TABLE_SCHEMA` LIKE '%s' AND `COLUMNS`.`TABLE_NAME` LIKE '%s'", _DB_NAME_, _DB_NAME_, $this->tableName);
			$stmt = $this->db->query($sql);
			if($stmt) {
				while($sel_row = $stmt->fetch_assoc()) {
					$colArr = $sel_row;
					$colArr['trc'] = explode(',', $colArr['trc']);
					unset($colArr['column']);
					$data_type = 's';
					switch($colArr['DATA_TYPE']) {
						case 'int':
						case 'tinyint':
						case 'smallint':
						case 'mediumint':
						case 'bigint':
						case 'boolean':
							$data_type = 'i';
							break;
						case 'decimal':
						case 'float':
						case 'double':
						case 'real':
							$data_type = 'd';
							break;
						case 'tinyblob':
						case 'mediumblob':
						case 'blob':
						case 'longblob':
							$data_type = 'b';
							break;
						default:
							break;
					}
					$colArr['param_type'] = $data_type;
					$schema[$sel_row['column']] = $colArr;
				}
				$stmt->free_result();
			}
			$this->schema = $schema;
			if($cache && $cache->getResultCode() == \Memcached::RES_NOTFOUND) $cache->set(_APP_NAME_ . 'schema' . $this->tableName, json_encode($schema));
			return $this;
		}

		/**
		 * @param string $order_by
		 * @return $this
		 */
		public function order($order_by) {
			$this->order_by = ' ORDER BY ' . $order_by;
			return $this;
		}

		/**
		 * @param string $limit
		 * @return $this
		 */
		public function limit($limit) {
			$this->limit = ' LIMIT ' . $limit;
			return $this;
		}

		/**
		 * @param array $where
		 * @return $this
		 */
		public function where($where) {
			$this->where = $where;
			return $this;
		}

		/**
		 * @param string $group_by
		 * @return $this
		 */
		public function groupBy($group_by) {
			$this->group_by = (!empty($group_by))?' GROUP BY ' . $group_by:'';
			return $this;
		}

		/**
		 * @param array|string $customField
		 * @return $this
		 */
		public function addCustomField($customField) {
			$this->customFields[] = $customField;
			return $this;
		}

		/**
		 * @return $this
		 */
		public function clear() {
			foreach(get_object_vars($this) AS $key => $val) if(!in_array($key, self::getIgnoredKeys())) unset($this->$key);
			$this->db = Database::getInstance();
			return $this;
		}

		/**
		 * Insert a new record
		 * @return array|Model
		 */
		public function create() {
			//If the last entity was not cleared by ID field we'll clear it
			if(property_exists($this, 'id')) unset($this->id);
			//Array of mysql bind parameters; first element is the column types, the next ones are the values for the columns
			$data = array();
			$param_type = '';
			//Store the column types into $param_type (eg: `ids`, meaning integer double string)
			foreach(get_object_vars($this) AS $key => $val) if(!in_array($key, self::getIgnoredKeys()) && !is_object($val) && property_exists($this, 'schema') && array_key_exists($key, $this->schema)) $param_type .= (property_exists($this, 'schema') && array_key_exists($key, $this->schema)) ? $this->schema[$key]['param_type'] : (is_numeric($val) ? 'i' : 's');
			$data[] = &$param_type;
			$cols = array();
			//Store the values for the columns
			foreach(get_object_vars($this) AS $key => $val) if(!in_array($key, self::getIgnoredKeys()) && !is_object($val) && property_exists($this, 'schema') && array_key_exists($key, $this->schema)) {
				$cols[] = $key;
				if($val === '0xNULL') $this->$key = null;
				$data[] = &$this->$key;
			}
			//Build sql query
			$values = str_repeat("?,", count($cols) - 1) . "?";
			$sql = "INSERT INTO " . $this->tableName . " (" . implode(",", $cols) . ") VALUES (" . $values . ")";
			//Execute the query
			try {
				$stmt = $this->db->prepare($sql);
				call_user_func_array(array($stmt, 'bind_param'), $data);
				$return = $stmt->execute();
				if($return) {
					$id = $stmt->insert_id;
					$stmt->free_result();
					if($id) $this->id = $id;
					return $this;
				}
				return array('error' => $stmt->error);
			}
			catch(\mysqli_sql_exception $e) {
				trigger_error($sql . "****" . $e->getMessage() . "\r\n" . print_r(debug_backtrace(), true), E_USER_WARNING);
				return array('error' => $e->getMessage());
			}
		}

		/**
		 * @param string $whereOp
		 * @param bool $ignore_password
		 * @return array|mixed
		 */
		public function get($whereOp = 'AND', $ignore_password = false) {
			$where = '';
			//Array of mysql bind parameters; first element is the column types, the next ones are the values for the columns
			$data = array();
			$param_type = '';
			$columnsToSelect = '';
			$join = array();
			$joins = '';
			//Build the join tables array
			if(property_exists($this, 'schema')) foreach($this->schema AS $column => $colDetails) {
				if(!empty($colDetails['column_reference'])) $join[$colDetails['table_reference']] = array('column' => $column, 'reference' => $colDetails['column_reference'], 'columns' => $colDetails['trc'], 'joinType' => (strtolower($colDetails['null']) == 'yes')?'LEFT':'INNER');
			}
			//Build the join columns names
			if(count($join)) {
				foreach($join AS $table_reference => $arrJoin) {
					foreach($arrJoin['columns'] AS $column) $columnsToSelect .= ", `j_{$table_reference}`.{$column} AS `{$table_reference}-{$column}`";
					$refColumn = (strpos($arrJoin['column'], '.') === false)?$this->tableName . '.' . $arrJoin['column']:$arrJoin['column'];
					if(!is_array($arrJoin['reference'])) $joins .= " {$arrJoin['joinType']} JOIN {$table_reference} `j_{$table_reference}` ON `j_{$table_reference}`.{$arrJoin['reference']} = {$refColumn}";
					else $joins .= " {$arrJoin['reference'][1]} JOIN {$table_reference} `j_{$table_reference}` ON `j_{$table_reference}`.{$arrJoin['reference'][0]} = {$refColumn}";
				}
			}
			//Store the column types into $param_type (eg: `ids`, meaning integer double string)
			//Build the sql for custom fields
			$customFields = '';
			foreach($this->customFields AS $customField) {
				if(is_array($customField)) {
					$param_type .= $customField[1];
					$customFields .= ', ' . $customField[0];
				}
				else $customFields .= ', ' . $customField;
			}
			//Build the sql where for table columns and their parameter types
			foreach(get_object_vars($this) AS $key => $val) {
				if(!in_array($key, self::getIgnoredKeys())) {
					$key = (count($join) > 0 && preg_match('/^([a-zA-Z0-9\_\-]+)$/', $key)) ? $this->tableName . '.' . $key : $key;
					$w = (is_array($val)) ? (($val[1] != 'BETWEEN') ? $key . ' ' . $val[1] . ' ?' : $key . ' BETWEEN ? AND ?') : ($val !== null ? $key . ' = ?' : $key . ' IS NULL');
					$where .= ($where == '') ? ' WHERE ' . $w : ' ' . $whereOp . ' ' . $w;
					if($val !== null) {
						$tipData = (property_exists($this, 'schema') && array_key_exists($key, $this->schema)) ? $this->schema[$key]['param_type'] : (is_numeric($val) ? 'i' : 's');
						$param_type .= ($key == 'id') ? 'i' : ((!is_array($val) || $val[1] != 'BETWEEN') ? $tipData : $tipData . $tipData);
					}
				}
			}
			//Append custom where parameter types if is set
			if(count($this->where) > 0) {
				foreach($this->where AS $key => $val) {
					$w = ($val == 'complexW')?$key:((is_array($val)) ? (($val[1] != 'BETWEEN') ? $key . ' ' . $val[1] . ' ?' : $key . ' BETWEEN ? AND ?') : $key . ' = ?');
					$where .= ($where == '') ? ' WHERE ' . $w : ' ' . $whereOp . ' ' . $w;
					if($val != 'complexW') {
						$tipData = (is_numeric($val) ? 'i' : ((is_array($val) && array_key_exists(2, $val))?$val[2]:'s'));
						$param_type .= ((!is_array($val) || $val[1] != 'BETWEEN') ? $tipData : $tipData . $tipData);
					}
				}
			}
			//Build the data parameters and types
			if($param_type) $data[] = &$param_type;
			foreach($this->customFields AS $key => $val) if(is_array($val)) $data[] = &$this->customFields[$key][2];
			foreach(get_object_vars($this) AS $key => $val) {
				if(!in_array($key, self::getIgnoredKeys())) {
					if(is_array($val)) {
						if($val[1] != 'BETWEEN') {
							$v = &$this->$key;
							$data[] = &$v[0];
						}
						else {
							$v = &$this->$key;
							$data[] = &$v[0];
							$data[] = &$v[2];
						}
					}
					else if($val !== null) $data[] = &$this->$key;
				}
			}
			//Append custom where parameter values if is set
			if(count($this->where) > 0) {
				foreach($this->where AS $key => $val) {
					if($val != 'complexW') {
						if(is_array($val)) {
							if($val[1] != 'BETWEEN') $data[] = &$this->where[$key][0];
							else {
								$data[] = &$this->where[$key][0];
								$data[] = &$this->where[$key][2];
							}
						}
						else $data[] = &$this->where[$key];
					}
				}
			}
			//End of build data
			$sql = "SELECT {$this->tableName}.*" . $columnsToSelect . $customFields . " FROM {$this->tableName}" . $joins . $where . $this->group_by . $this->order_by . $this->limit;
			$ret = array();
			try {
				$stmt = $this->db->prepare($sql);
				if($stmt) {
					if(count($data) > 0) call_user_func_array(array($stmt, 'bind_param'), $data);
					$stmt->execute();
					$meta = $stmt->result_metadata();
					$params = array();
					while($field = $meta->fetch_field()) $params[] = &$row[$field->name];
					call_user_func_array(array($stmt, 'bind_result'), $params);

					while($stmt->fetch()) {
						$c = new Model($this->tableName, $this->schema);
						/** @var array $row */
						foreach($row as $key => $val) {
							if($ignore_password && $key == 'password') continue;
							preg_match('/(.+?)(?=\-)\-(.*)/', $key, $matches);
							if(count($matches)) {
								$table = $matches[1];
								if(!property_exists($c, $table)) {
									$nc = new Model($table);
									$c->$table = $nc;
								}
								$c->$table->$matches[2] = $val;
							}
							else $c->$key = $val;
						}
						$ret[] = $c;
					}
					$stmt->free_result();
				}
				else die('Error preparing sql -> ' . $sql . ' | ' . print_r($data, true));
			}
			catch(\Exception $e) {
				error_log($e->getMessage());
			}
			return $ret;
		}

		/**
		 * @param $key
		 * @param $val
		 * @return Model|bool
		 */
		public function getOneResult($key, $val) {
			//Parameter type
			$varType = is_numeric($val)?'i':'s';
			//If the last entity was not cleared we'll clear it
			$this->clear();
			$join = array();
			$columnsToSelect = '';
			$joins = '';
			if(property_exists($this, 'schema')) foreach($this->schema AS $column => $colDetails) {
				if(!empty($colDetails['column_reference'])) $join[$colDetails['table_reference']] = array('column' => $column, 'reference' => $colDetails['column_reference'], 'columns' => $colDetails['trc'], 'joinType' => (strtolower($colDetails['null']) == 'yes')?'LEFT':'INNER');
			}
			if(count($join)) {
				foreach($join AS $table_reference => $arrJoin) {
					foreach($arrJoin['columns'] AS $column) $columnsToSelect .= ", `j_{$table_reference}`.{$column} AS `{$table_reference}-{$column}`";
					$refColumn = (strpos($arrJoin['column'], '.') === false)?$this->tableName . '.' . $arrJoin['column']:$arrJoin['column'];
					if(!is_array($arrJoin['reference'])) $joins .= " {$arrJoin['joinType']} JOIN {$table_reference} `j_{$table_reference}` ON `j_{$table_reference}`.{$arrJoin['reference']} = {$refColumn}";
					else $joins .= " {$arrJoin['reference'][1]} JOIN {$table_reference} `j_{$table_reference}` ON `j_{$table_reference}`.{$arrJoin['reference'][0]} = {$refColumn}";
				}
			}
			$sql = "SELECT " . $this->tableName . ".*" . $columnsToSelect . " FROM " . $this->tableName . "{$joins} WHERE {$this->tableName}.{$key} = ? LIMIT 0, 1";
			try {
				$stmt = $this->db->prepare($sql);
				call_user_func_array(array($stmt, 'bind_param'), array($varType, &$val));
				$stmt->execute();
				$stmt->store_result();
				if($stmt->num_rows > 0) {
					$meta = $stmt->result_metadata();
					$params = array();
					while($field = $meta->fetch_field()) $params[] = &$row[$field->name];
					call_user_func_array(array($stmt, 'bind_result'), $params);
					while($stmt->fetch()) {
						/** @var array $row */
						foreach($row as $key => $val) {
							if(array_key_exists($key, $this->schema)) $this->$key = $val;
							else {
								preg_match('/(.+?)(?=\-)\-(.*)/', $key, $matches);
								if(count($matches)) {
									$table = $matches[1];
									if(!property_exists($this, $table)) {
										$nc = new Model($table);
										$this->$table = $nc;
									}
									$this->$table->$matches[2] = $val;
								}
							}
						}
					}
					$stmt->free_result();
					return $this;
				}
			}
			catch(\Exception $e) {
				trigger_error($sql . "****" . $val . "********\r\n" . $e->getMessage() . "\r\n" . print_r(debug_backtrace(), true), E_USER_WARNING);
				return false;
			}
			return false;
		}

		/**
		 * @param string $whereOp
		 * @return int
		 */
		public function countItems($whereOp = 'AND') {
			$totalItems = 0;
			$where = '';
			$data = array();
			$param_type = '';
			$join = array();
			if(property_exists($this, 'schema')) foreach($this->schema AS $column => $colDetails) {
				if(!empty($colDetails['column_reference'])) $join[$colDetails['table_reference']] = array('column' => $column, 'reference' => $colDetails['column_reference'], 'joinType' => (strtolower($colDetails['null']) == 'yes')?'LEFT':'INNER');
			}
			$count = (empty($this->group_by)) ? '*' : ('DISTINCT ' . str_replace(' GROUP BY ', '', $this->group_by));
			foreach($this->customFields AS $customField) if(is_array($customField)) $param_type .= $customField[1];
			foreach(get_object_vars($this) AS $key => $val) {
				if(!in_array($key, self::getIgnoredKeys())) {
					$key = (count($join) > 0 && preg_match('/^([a-zA-Z0-9\_\-]+)$/', $key)) ? $this->tableName . '.' . $key : $key;
					$w = (is_array($val)) ? (($val[1] != 'BETWEEN') ? $key . ' ' . $val[1] . ' ?' : $key . ' BETWEEN ? AND ?') : ($val !== null ? $key . ' = ?' : $key . ' IS NULL');
					$where .= ($where == '') ? ' WHERE ' . $w : ' ' . $whereOp . ' ' . $w;
					if($val !== null) {
						$tipData = (property_exists($this, 'schema') && array_key_exists($key, $this->schema)) ? $this->schema[$key]['param_type'] : (is_numeric($val) ? 'i' : 's');
						$param_type .= ($key == 'id') ? 'i' : ((!is_array($val) || $val[1] != 'BETWEEN') ? $tipData : $tipData . $tipData);
					}
				}
			}
			if(count($this->where) > 0) {
				foreach($this->where AS $key => $val) {
					$w = (is_array($val)) ? (($val[1] != 'BETWEEN') ? $key . ' ' . $val[1] . ' ?' : $key . ' BETWEEN ? AND ?') : $key . ' = ?';
					$where .= ($where == '') ? ' WHERE ' . $w : ' ' . $whereOp . ' ' . $w;
					$tipData = (is_numeric($val) ? 'i' : ((is_array($val) && array_key_exists(2, $val))?$val[2]:'s'));
					$param_type .= ((!is_array($val) || $val[1] != 'BETWEEN') ? $tipData : $tipData . $tipData);
				}
			}
			if($param_type) $data[] = &$param_type;
			foreach($this->customFields AS $key => $val) if(is_array($val)) $data[] = &$this->customFields[$key][2];
			foreach(get_object_vars($this) AS $key => $val) {
				if(!in_array($key, self::getIgnoredKeys())) {
					if(is_array($val)) {
						if($val[1] != 'BETWEEN') {
							$v = &$this->$key;
							$data[] = &$v[0];
						}
						else {
							$v = &$this->$key;
							$data[] = &$v[0];
							$data[] = &$v[2];
						}
					}
					else if($val !== null) $data[] = &$this->$key;
				}
			}
			if(count($this->where) > 0) {
				foreach($this->where AS $key => $val) {
					if(is_array($val)) {
						if($val[1] != 'BETWEEN') $data[] = &$this->where[$key][0];
						else {
							$data[] = &$this->where[$key][0];
							$data[] = &$this->where[$key][2];
						}
					}
					else $data[] = &$this->where[$key];
				}
			}
			$customFields = '';
			if(!empty($this->customFields)) {
				foreach($this->customFields AS $customField) {
					if(!is_array($customField)) $customFields .= (empty($customFields)) ? $customField : ', ' . $customField;
					else {
						$customFields .= (empty($customFields)) ? $customField[0] : ', ' . $customField[0];
					}
				}
			}

			$sql = sprintf("SELECT COUNT(" . $count . ") AS totalItems FROM %s", $this->tableName);
			$joins = '';
			foreach($join AS $table_reference => $column_reference) {
				$refColumn = (strpos($column_reference['column'], '.') === false)?$this->tableName . '.' . $column_reference['column']:$column_reference['column'];
				if(!is_array($column_reference['reference'])) $joins .= " {$column_reference['joinType']} JOIN {$table_reference} `j_{$table_reference}` ON `j_{$table_reference}`.{$column_reference['reference']} = {$refColumn}";
				else $joins .= " {$column_reference['reference'][1]} JOIN {$table_reference} `j_{$table_reference}` ON `j_{$table_reference}`.{$column_reference['reference'][0]} = {$refColumn}";
			}
			$sql .= $joins . $where;
			$stmt = $this->db->prepare($sql);
			if(count($data) > 0) call_user_func_array(array($stmt, 'bind_param'), $data);
			$stmt->execute();
			$stmt->bind_result($totalItems);
			$stmt->fetch();
			$stmt->free_result();

			return $totalItems;
		}

		/**
		 * @param bool $where
		 * @return Model|array
		 */
		public function update($where = false) {
			if(property_exists($this, 'id')) {
				$id = $this->id;
				unset($this->id);
				$data = array();
				$param_type = '';
				$set = '';
				foreach(get_object_vars($this) AS $key => $val) if(!is_a($val, 'Model\Model') && !in_array($key, self::getIgnoredKeys())) $param_type .= (property_exists($this, 'schema') && array_key_exists($key, $this->schema)) ? $this->schema[$key]['param_type'] : (is_numeric($val) ? 'i' : 's');
				$param_type .= 'i';
				$data[] = &$param_type;
				foreach(get_object_vars($this) AS $key => $val) if(!is_a($val, 'Model\Model') && !in_array($key, self::getIgnoredKeys())) {
					if($this->$key === '0xNULL') $this->$key = null;
					$data[] = &$this->$key;
					$set .= ($set == '') ? $key . ' = ?' : ', ' . $key . ' = ?';
				}
				$data[] = &$id;
				$sql = "UPDATE {$this->tableName} SET " . $set . " WHERE id = ?";
				$stmt = $this->db->prepare($sql);
				call_user_func_array(array($stmt, 'bind_param'), $data);
				try {
					$return = $stmt->execute();
					$stmt->free_result();
					$this->id = $id;
					if($return) return $this;
				}
				catch(\Exception $e) {
					error_log($e->getMessage());
				}
				return $this;
			}
			elseif($where) {
				$data = array();
				$param_type = '';
				$set = '';
				foreach(get_object_vars($this) AS $key => $val) if(!is_a($val, 'Model\Model') && !in_array($key, self::getIgnoredKeys())) $param_type .= (property_exists($this, 'schema') && array_key_exists($key, $this->schema)) ? $this->schema[$key]['param_type'] : (is_numeric($val) ? 'i' : 's');
				$data[] = &$param_type;
				foreach(get_object_vars($this) AS $key => $val) if(!is_a($val, 'Model\Model') && !in_array($key, self::getIgnoredKeys())) {
					if($this->$key === '0xNULL') $this->$key = null;
					$data[] = &$this->$key;
					$set .= ($set == '') ? $key . ' = ?' : ', ' . $key . ' = ?';
				}
				$stmt = $this->db->prepare("UPDATE {$this->tableName} SET " . $set . " WHERE {$where}");
				call_user_func_array(array($stmt, 'bind_param'), $data);
				$return = $stmt->execute();
				$stmt->free_result();
				if($return) return $this;
				return array('error' => $stmt->error);
			}
			return array('error' => __('No where statement'));
		}

		/**
		 * @param string $whereOp
		 * @return bool|array
		 */
		public function delete($whereOp = 'AND') {
			$where = '';
			$data = array();
			$param_type = '';
			//Build the sql where for table columns and their parameter types
			foreach(get_object_vars($this) AS $key => $val) {
				if(!is_a($val, 'Model\Model') && !in_array($key, self::getIgnoredKeys())) {
					$w = (is_array($val)) ? (($val[1] != 'BETWEEN') ? $key . ' ' . $val[1] . ' ?' : $key . ' BETWEEN ? AND ?') : ($val !== null ? $key . ' = ?' : $key . ' IS NULL');
					$where .= ($where == '') ? ' WHERE ' . $w : ' ' . $whereOp . ' ' . $w;
					if($val !== null) {
						$tipData = (property_exists($this, 'schema') && array_key_exists($key, $this->schema)) ? $this->schema[$key]['param_type'] : (is_numeric($val) ? 'i' : 's');
						$param_type .= ($key == 'id') ? 'i' : ((!is_array($val) || $val[1] != 'BETWEEN') ? $tipData : $tipData . $tipData);
					}
				}
			}
			//Append custom where parameter types if is set
			if(count($this->where) > 0) {
				foreach($this->where AS $key => $val) {
					$w = (is_array($val)) ? (($val[1] != 'BETWEEN') ? $key . ' ' . $val[1] . ' ?' : $key . ' BETWEEN ? AND ?') : $key . ' = ?';
					$where .= ($where == '') ? ' WHERE ' . $w : ' ' . $whereOp . ' ' . $w;
					$tipData = (is_numeric($val) ? 'i' : ((is_array($val) && array_key_exists(2, $val))?$val[2]:'s'));
					$param_type .= ((!is_array($val) || $val[1] != 'BETWEEN') ? $tipData : $tipData . $tipData);
				}
			}
			//Build the data parameters and types
			if($param_type) $data[] = &$param_type;
			foreach($this->customFields AS $key => $val) if(is_array($val)) $data[] = &$this->customFields[$key][2];
			foreach(get_object_vars($this) AS $key => $val) {
				if(!is_a($val, 'Model\Model') && !in_array($key, self::getIgnoredKeys())) {
					if(is_array($val)) {
						if($val[1] != 'BETWEEN') {
							$v = &$this->$key;
							$data[] = &$v[0];
						}
						else {
							$v = &$this->$key;
							$data[] = &$v[0];
							$data[] = &$v[2];
						}
					}
					else if($val !== null) $data[] = &$this->$key;
				}
			}
			//Append custom where parameter values if is set
			if(count($this->where) > 0) {
				foreach($this->where AS $key => $val) {
					if(is_array($val)) {
						if($val[1] != 'BETWEEN') $data[] = &$this->where[$key][0];
						else {
							$data[] = &$this->where[$key][0];
							$data[] = &$this->where[$key][2];
						}
					}
					else $data[] = &$this->where[$key];
				}
			}
			$sql = "DELETE FROM " . $this->tableName . $where;
			if(!empty($where)) {
				try {
					$stmt = $this->db->prepare($sql);
					if(count($data) > 0) call_user_func_array(array($stmt, 'bind_param'), $data);
					$return = $stmt->execute();
					$stmt->free_result();
					if($return) return true;
					return array('error' => $stmt->error);
				}
				catch(\Exception $e) {
					die($e->getMessage());
				}
			}
			return array('error' => __('No where statement'));
		}

		/**
		 * @return int
		 */
		public function lastId() {
			$stmt = $this->db->query(/** @lang text */
				"SELECT MAX(`id`) AS `id` FROM {$this->tableName}");
			$sel_row = $stmt->fetch_object();
			$id = $sel_row->id ? $sel_row->id : 0;
			$stmt->free_result();
			return $id;
		}

		/**
		 * @param string $sql
		 * @param array $data
		 * @param bool $useResult
		 * @return array|bool
		 */
		public function runQuery($sql, &$data = array(), $useResult = true) {
			$ret = array();
			if(count($data) > 0) {
				$stmt = $this->db->prepare($sql);
				call_user_func_array(array($stmt, 'bind_param'), $data);
				$stmt->execute();
				$meta = $stmt->result_metadata();
				if($meta) {
					$params = array();
					while($field = $meta->fetch_field()) $params[] = &$row[$field->name];
					if(count($params)) call_user_func_array(array($stmt, 'bind_result'), $params);
				}
				/** @var array $row */
				while($stmt->fetch()) $ret[] = $row;
				$stmt->free_result();

				return $ret;
			}
			else {
				if($useResult) {
					$result = $this->db->query($sql, MYSQLI_USE_RESULT);
					while($obj = $result->fetch_assoc()) $ret[] = $obj;

					return $ret;
				}
				else $this->db->query($sql);
			}

			return true;
		}

		/**
		 * @param $sql
		 * @return bool
		 */
		public function runMultiQuery($sql) {
			$db = $this->db;
			$db->begin_transaction();
			$db->multi_query($sql);
			$db->commit();
			while(mysqli_more_results($db)) mysqli_next_result($db);
			return true;
		}

		/**
		 * @return array
		 */
		private static function getIgnoredKeys() {
			return array('db', 'order_by', 'limit', 'schema', 'where', 'customFields', 'tableName', 'group_by');
		}
	}
}