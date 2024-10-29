<?php

/*  mysqli_ext extension of the PHP mysqli framework is the intellectual property
    of Ryan Gross, and may not be used without written permission from the
    author.
*/

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class mysqli_ext extends mysqli {
	// Query functions
	public function showTables() {
		$query_result = $this->query("SHOW TABLES from `".$GLOBALS["db_name"]."` like '".$GLOBALS['app_name']."%'");
		$db_table_list = array_column($query_result->fetch_all(),0);
		$query_result->free_result();
		return $db_table_list;
	}
	public function showColumnsFrom($table_name) {
		$query_result = $this->query("SHOW COLUMNS from `".$GLOBALS["db_name"]."`.`$table_name`");
		$columns = array($query_result->fetch_all(),0)[0];
		$query_result->free_result();
		return $columns;
	}
	public function countColumnsFrom($table_name) {
		$query_result = $this->query("SELECT COUNT(*)
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_schema = '".$GLOBALS["db_name"]."'
				AND table_name = '$table_name'");
		$count = $query_result->fetch_row()[0];
		$query_result->free_result();
		return $count;
	}
	public function selectRowsFrom($table_name,$limit_rows=0,$order_by_column='',$order_by_method='ASC') {
		$order_by = ($order_by_column == '') ? '' : " ORDER BY `$order_by_column` $order_by_method";
		$limit = ($limit_rows > 0) ? " LIMIT $limit_rows" : '';
		$query_result = $this->query("SELECT * FROM `".$GLOBALS["db_name"]."`.`$table_name`$order_by$limit");
		$rows = array($query_result->fetch_all(),0)[0];
		$query_result->free_result();
		return $rows;
	}
	public function selectRowsCustom($table_name,$columns='*',$conditions=[]) {
		// Verify parameters
		$str_columns = '';
		if ($columns == '*') {
			$str_columns = '*';
		} else if (is_string($columns)) {
			$str_columns = '`'.substr(str_replace(['`','"',"'",' '],'',$columns),0,strpos($columns,' ')).'`';
		} else if (is_array($columns)) {
			for ($i = 0; $i < sizeof($columns); $i++) {
				$str_columns .= '`'.substr(str_replace(['`','"',"'",' '],'',$columns[$i]),0).'`';
				if ($i < sizeof($columns)-1) {$str_columns .= ', ';}
			}
		} else {
			throw new Exception("Error: (columns) incorrect parameter data type passed!");
		}
		$str_conditions = '';
		if (is_array($conditions)) {
			if (count($conditions) > 0) {
				$str_conditions = ' WHERE ';
				for ($i = 0; $i < count($conditions); $i++) {
					$quotes = (is_nan($conditions[$i][1])) ? '"' : '';
					$str_conditions .= '`'.str_replace(['`','"',"'",' '],'',$conditions[$i][0]).'` = '.$quotes.str_replace(['`','"',"'"],'',$conditions[$i][1]).$quotes;
					if ($i < count($conditions)-1) {$str_conditions .= ', ';}
				}
			}
		} else {
			throw new Exception("Error: (conditions) incorrect parameter data type passed!");
		}
		// Run query
		//echo "<br>SELECT $str_columns FROM `app_db`.`$table_name`$str_conditions";
		$query_result = $this->query("SELECT $str_columns FROM `".$GLOBALS["db_name"]."`.`$table_name`$str_conditions");
		$rows = array($query_result->fetch_all(),0)[0];
		$query_result->free_result();
		return $rows;
	}
	public function countRowsFrom($table_name) {
		$query_result = $this->query("SELECT COUNT(*) FROM `".$GLOBALS["db_name"]."`.`$table_name`");
		$count = $query_result->fetch_row()[0];
		$query_result->free_result();
		return $count;
	}
	public function queryCustom($query) {
		$query_result = $this->query($query);
		$rows = array($query_result->fetch_all(),0)[0];
		$query_result->free_result();
		return $rows;
	}

	public function insertOne($table_name,$row_data,$echo_alerts=false,$return_err=false) {
		try {
			// Get columns (so to know what fields to look for)
			$columns = $this->showColumnsFrom($table_name);

			// Look for fields
			$str_columns = ""; $str_blanks = ""; $str_params = ""; $params = array(); $i = 0;
			foreach ($columns as $column) {
				// Disregard autofilled columns
				if ($column[5] != '') {continue;}
				// Only necessary columns at this point. Throw error if column data not found AND column is required (else default to null)
				if ((!isset($row_data[$column[0]]) || $row_data[$column[0]]=='') && $column[4]!=null) {continue;}
				if (!isset($row_data[$column[0]]) && $column[2]=='NO') {
					throw new Exception("Missing field for column '".$column[0]."'");
				}
				// All columns have data. Process data
				if ($row_data[$column[0]] == '') {
					$row_data[$column[0]] = null;
				}
				if ($i > 0) {
					$str_columns .= ', ';
					$str_blanks .= ',';
				}
				if (stripos($column[1],'int') !== false) {
					$params[$i] = (int)($row_data[$column[0]]);
					$str_params .= 'i';
				} else {
					$params[$i] = $row_data[$column[0]];
					$str_params .= 's';
				}

				if (stripos($column[0],'password') !== false) {
					$str_blanks .= 'MD5(?)';
				} else {
					$str_blanks .= '?';
				}
				$str_columns .= $column[0];
				$i++;
			}
			$stmt_insert = $this->prepare("INSERT INTO `".$GLOBALS["db_name"]."`.`$table_name` ($str_columns) VALUES ($str_blanks)");
			$stmt_insert->bind_param($str_params, ...$params);
			$stmt_insert->execute();

			if ($stmt_insert->affected_rows > 0) {
				// Success
				if ($echo_alerts) {echo_success("Inserted ".$stmt_insert->affected_rows." row (id: ".$stmt_insert->insert_id.")");}
				return $stmt_insert->insert_id;
			} else {
				// Failure
				throw new Exception("(".$stmt_insert->errno.") ".$stmt_insert->error);
			}
		} catch (Throwable $th) {
			// Failure
			if ($echo_alerts) {echo_error("Error: ".$th->getMessage());}
			return ($return_err) ? $th->getMessage() : false;
		}
	}

	public function updateOne($table_name, $row_id, $row_data, $echo_alerts = false) {
		try {
			if (is_nan($row_id)) {throw new Exception("Invalid row_id (".$row_id.")");}

			// Get columns (so to know what fields to look for)
			$columns = $this->showColumnsFrom($table_name);
			$row_auto_inc = $columns[0][0];

			// Get existing row data
			$old_row = $this->selectRowsCustom($table_name,'*',[[$row_auto_inc,$row_id]])[0]; $old_row_data = [];
			for ($i = 0; $i < count($columns); $i++) {
				$old_row_data[$columns[$i][0]] = $old_row[$i];
			}

			// Look for fields
			$str_updates = ""; $str_fields = ""; $str_params = ""; $params = array(); $i = 0;
			foreach ($columns as $column) {
				// Disregard non-editable columns
				if ($column[5] != '') {continue;}
				if (!isset($row_data[$column[0]]) || ($row_data[$column[0]]=='') || ($column[4]!=null && $column[4]!=0)) {continue;}
				// Only editable columns at this point. Continue if data has not been changed
				if ($old_row_data[$column[0]] == $row_data[$column[0]]) {continue;}
				// Columns have new data. Process data
				if ($row_data[$column[0]] == '') {
					$row_data[$column[0]] = null;
				}
				if ($i > 0) {
					$str_updates .= ',';
					$str_fields .= ', ';
				}
				if (stripos($column[1],'int') !== false) {
					$params[$i] = (int)($row_data[$column[0]]);
					$str_params .= 'i';
				} else {
					$params[$i] = $row_data[$column[0]];
					$str_params .= 's';
				}
				if (stripos($column[0],'password') !== false) {
					$str_updates .= "`".$column[0]."`=MD5(?)";
				} else {
					$str_updates .= "`".$column[0]."`=?";
				}
				$str_fields .= $column[0];
				$i++;
			}

			if ($str_updates == '') {
				echo_warning('Query did not execute. No data has been changed.');
			} else {
				//echo "UPDATE `$table_name` SET $str_updates WHERE $row_auto_inc = $row_id";
				$stmt_update = $this->prepare("UPDATE `".$GLOBALS["db_name"]."`.`$table_name` SET $str_updates WHERE $row_auto_inc = $row_id");
				$stmt_update->bind_param($str_params, ...$params);
				$stmt_update->execute();

				if ($stmt_update->affected_rows > 0) {
					// Success
					if ($echo_alerts) {echo_success("Updated ".$stmt_update->affected_rows." row (id: ".$row_id."). Fields changed: ".$str_fields);}
					return true;
				} else {
					// Failure
					throw new Exception("(".$stmt_update->errno.") ".$stmt_update->error);
				}
			}
		} catch (Throwable $th) {
			// Failure
			if ($echo_alerts) {echo_error("Error: ".$th->getMessage());}
			return false;
		}
	}

	public function deleteWhere($table_name, $row_id, $echo_alerts = false) {
		try {
			if (is_nan($row_id)) {throw new Exception("Invalid row_id (".$row_id.")");}

			// Get columns (so to know what fields to look for)
			$columns = $this->showColumnsFrom($table_name);
			$row_auto_inc = $columns[0][0];

			$stmt_delete = $this->prepare("DELETE FROM `".$GLOBALS["db_name"]."`.`$table_name` WHERE `$row_auto_inc` = $row_id");
			$stmt_delete->execute();

			if ($stmt_delete->affected_rows > 0) {
				// Success
				if ($echo_alerts) {echo_success("Deleted ".$stmt_update->affected_rows." row (id: ".$row_id.")");}
				return true;
			} else {
				// Failure
				throw new Exception("(".$stmt_update->errno.") ".$stmt_update->error);
			}
		} catch (Throwable $th) {
			// Failure
			if ($echo_alerts) {echo_error("Error: ".$th->getMessage());}
			return false;
		}
	}
}