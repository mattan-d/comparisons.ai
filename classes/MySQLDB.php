<?php

namespace MySQLDB;

class MySQLDB {

    use ConditionTrait;

    function __construct() {
        global $CONFIG;

        mysqli_report(MYSQLI_REPORT_ERROR);

        if ($CONFIG->dbhost) {
            $this->mysqli = new \mysqli($CONFIG->dbhost, $CONFIG->dbuser, $CONFIG->dbpass, $CONFIG->dbname);
            $this->mysqli->query('set names utf8mb4');

            if ($this->mysqli->connect_errno) {
                echo 'Error DB!';
                exit();
            }
        }
    }

    private function read() {
        /*        if (isset($_SESSION['read'])) {
                    $_SESSION['read'] = $_SESSION['read'] + 1;
                }*/
    }

    private function write() {
        /*        if (isset($_SESSION['write'])) {
                    $_SESSION['write'] = $_SESSION['write'] + 1;
                }*/
    }

    public function store($storeName, $db = false) {
        global $CONFIG;

        $instance = new self();

        if ($db) {
            $this->mysqli = new \mysqli($CONFIG->dbhost, $CONFIG->dbuser, $CONFIG->dbpass, $db);
            $this->mysqli->query('set names utf8mb4');

            if ($this->mysqli->connect_errno) {
                echo 'Error DB!';
                exit();
            }
        }

        $instance->store = $storeName;
        $instance->init = true;
        $instance->count = false;
        $instance->presql = false;
        $instance->where = [];
        $instance->like = [];
        $instance->orderBy = [];
        $instance->tmp = [];
        $instance->default = [];
        $instance->one = false;
        $instance->debug = false;
        $instance->calc = false;
        $instance->limit = false;
        $instance->groupBy = false;
        $instance->fullsql = false;

        return $instance;
    }

}

trait ConditionTrait {

    private function escape($string) {

        return '"' . addslashes($string) . '"';

        /*        if (is_numeric($string) && intval($string) > 1400000000)
                    return '"' . addslashes($string) . '"';

                if (!is_numeric($string))
                    return '"' . addslashes($string) . '"';
                else
                    return $string;*/
    }

    private function diff() {
        if (empty($this->structure)) {
            $this->structure = [];
        }

        $columns = $this->getColumns($this->copyData);
        $currColumns = array_keys($this->structure);
        $diff = array_diff($columns, $currColumns);

        if ($diff) {
            $this->alterTable(array_values($diff), $this->copyData);
        }

        return true;
    }

    private function reBuild($data) {
        $tmp = [];
        foreach ($data as $key => $value) {
            if (strpos($key, '__') !== false) {
                $keys = explode('__', $key);
                if (!is_array($tmp[$keys[0]])) {
                    $tmp[$keys[0]] = [];
                }
                $tmp[$keys[0]][$keys[1]] = $value;
            } else {
                $tmp[$key] = $value;
            }
        }
        return $tmp;
    }

    private function getColumns($data, $prefix = '', $recursiveData = []) {
        foreach ($data as $key => $value) {
            array_push($recursiveData, $prefix . $key);
            if (is_array($value)) {
                $recursiveData = $this->getColumns($value, $key . '__', $recursiveData);
            }
        }
        return $recursiveData;
    }

    private function getValues($data, $recursiveData = []) {
        foreach ($data as $key => $value) {
            if (!strlen($value)) {
                array_push($recursiveData, 'null');
            } else {
                array_push($recursiveData, $this->escape($value));
            }

            if (is_array($value)) {
                $recursiveData = $this->getValues($value, $recursiveData);
            }
        }
        return $recursiveData;
    }

    private function buildColumns($structure, $prefix = '') {
        if (!$this->columns) {
            $this->columns = [];
        }

        foreach ($structure as $key => $value) {
            array_push($this->columns, $prefix . $key . ' ' . $this->typeByValue($value));
            if (is_array($value)) {
                $this->buildColumns($value, $key . '__');
            }
        }

        return $this;
    }

    private function addWhere($sql = '') {
        $this->sql = $sql;

        if (count($this->where) || count($this->like)) {
            $this->sql .= ' WHERE ';
        }

        if (count($this->where)) {
            $this->tmp = [];
            foreach ($this->where as $where) {
                // is null
                if ($where->value === null) {
                    array_push($this->tmp, $where->field . ' ' . $where->condition . ' null');
                } else {

                    if (is_array($where->value)) {
                        $where->value = implode(',', $where->value);
                        array_push($this->tmp, $where->field . ' ' . $where->condition . '(' . $where->value . ')');
                    } else {
                        array_push($this->tmp, $where->field . ' ' . $where->condition . ' ' . $this->escape($where->value));
                    }
                }
            }
            $this->sql .= implode(' ' . $this->whereAssign . ' ', $this->tmp) . ' ';
        }

        if (count($this->like)) {
            $this->tmp = [];
            foreach ($this->like as $like) {
                array_push($this->tmp, $like->field . ' LIKE ' . $this->escape($like->value));
            }
            $this->sql .= implode(' ' . $this->whereAssign . ' ', $this->tmp) . ' ';
        }

        return $this->sql;
    }

    private function typeByValue($value) {
        if (is_string($value)) {
            // Check if the string contains multibyte characters
            $length = mb_strlen($value, 'UTF-8');
            if ($length <= 255) {
                return "VARCHAR($length) CHARACTER SET 'utf8mb4' NULL DEFAULT NULL";
            } else if ($length <= 65535) {
                return "TEXT CHARACTER SET 'utf8mb4'";
            } else if ($length <= 16777215) {
                return "MEDIUMTEXT CHARACTER SET 'utf8mb4'";
            } else {
                return "LONGTEXT CHARACTER SET 'utf8mb4'";
            }
        } else if (is_int($value)) {
            if ($value >= -128 && $value <= 127) {
                return "TINYINT";
            } else if ($value >= -32768 && $value <= 32767) {
                return "SMALLINT";
            } else if ($value >= -8388608 && $value <= 8388607) {
                return "MEDIUMINT";
            } else if ($value >= -2147483648 && $value <= 2147483647) {
                return "INT";
            } else {
                return "BIGINT";
            }
        } else if (is_float($value)) {
            return "DOUBLE";
        } else if (is_bool($value)) {
            return "BOOLEAN";
        } else if (is_null($value)) {
            return "NULL";
        } else if (is_array($value)) {
            // For simplicity, assume serialized array storage
            return "TEXT CHARACTER SET 'utf8mb4'";
        } else {
            return "TEXT CHARACTER SET 'utf8mb4'";
        }
    }

    private function alterTable($columns, $value) {
        $table = $this->sql('SHOW TABLES LIKE \'' . $this->store . '\'')->fetch();
        if (sizeof($table) == 0) {
            $this->createTable([$this->copyData]);
            return $this;
        }

        $tmp = [];
        foreach ($columns as $key => $column) {
            array_push($tmp, 'ADD COLUMN ' . $column . ' ' . $this->typeByValue($value[$column]));
        }

        $sql = 'ALTER TABLE ' . $this->store . ' ' . implode(', ', $tmp);

        if ($this->mysqli->query($sql)) {
            return $this;
        }
    }

    private function alterColumn($error, $value = false, $columns = []) {

        preg_match("/column '(.*)' (?:at row|in 'field list')/i", $error, $column);
        $column = $column[1];

        if (strpos($error, 'at row') !== false) {
            $index = array_search($column, $columns);
            $sql = 'ALTER TABLE ' . $this->store . ' MODIFY COLUMN ' . $column . ' ' . $this->typeByValue($value[$index]);
        } else if (strpos($error, 'in') !== false) {
            if ($column) {
                $index = array_search($column, $columns);
                $sql = 'ALTER TABLE ' . $this->store . ' ADD COLUMN ' . $column . ' ' . $this->typeByValue($value[$index]);
            }
        } else {
            if (strpos($error, "doesn't exist") !== false) {
                preg_match("/Table '(.+?)\.(.+?)' doesn't exist/", $error, $matches);
                if (count($matches) == 3) {
                    $table = $matches[2];
                    $sql = 'CREATE TABLE ' . $table . ' (id INT AUTO_INCREMENT PRIMARY KEY);';
                }
            }
        }

        if ($sql && $this->mysqli->query($sql)) {
            return $this;
        }
    }

    private function tableReady() {
        $table = $this->sql('SHOW TABLES LIKE \'' . $this->store . '\'')->fetch();
        if (sizeof($table) == 0) {
            $this->structure = $this->copyData[0];
            return $this;
        }

        $data = $this->sql('DESCRIBE ' . $this->store)->fetch();
        if ($data) {
            $this->structure = [];
            foreach ($data as $item) {
                $this->structure[$item['Field']] = 0;
                $this->skipCreate = true;
            }
        } else {
            $this->structure = $this->copyData[0];
        }

        return $this;
    }

    public function copy() {
        foreach ($this->copyData as $raw) {
            if ($diff = array_diff(array_keys($raw), array_keys($this->structure))) {
                $this->alterTable(array_values($diff), $raw);
            }

            $newid = $this->store($this->store)->insert($raw);
            echo 'DATA COPY: ' . $raw['_id'] . PHP_EOL;
        }

        return true;
    }

    public function createTable($createData) {
        $this->copyData = $createData;
        $this->tableReady();
        $this->buildColumns($this->structure);

        if ($this->skipCreate) {
            return $this;
        }

        $sql = 'CREATE TABLE IF NOT EXISTS ' . $this->store . ' (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ' . implode(',', $this->columns) . '
            )';

        if ($this->mysqli->query($sql)) {
            return $this;
        }
    }

    public function def($def) {
        $this->default = $def;

        return $this;
    }

    public function update($updateData) {
        $this->write();
        $this->copyData = $updateData;
        $this->tableReady();
        $this->diff();

        $columns = $this->getColumns($updateData);
        $values = $this->getValues($updateData);

        $i = 0;
        $this->tmp = [];
        foreach ($columns as $column) {
            array_push($this->tmp, $column . ' = ' . $values[$i]);
            $i++;
        }

        $this->sql = 'UPDATE ' . $this->store . ' SET ' . implode(',', $this->tmp);
        $this->addWhere($this->sql);

        if ($this->debug) {
            print_r($this);
        }

        if ($this->mysqli->query($this->sql)) {
            $this->mysqli->insert_id;
        } else {
            error_log('MySQL Error: ' . $this->mysqli->error);

            $this->alterColumn($this->mysqli->error, $values, $columns);
            $this->update($updateData);
        }
    }

    public function insert($storeData) {
        $this->write();
        $this->copyData = $storeData;
        $this->tableReady();
        $this->diff();

        $columns = $this->getColumns($storeData);
        $values = $this->getValues($storeData);

        $sql = 'INSERT INTO ' . $this->store . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) .
                ')  ON DUPLICATE KEY UPDATE ' . $columns[0] . '=' . $columns[0];

        if ($this->debug) {
            print_r($this);
        }

        if ($this->mysqli->query($sql)) {
            return $this->mysqli->insert_id;
        } else {
            error_log('MySQL Error: ' . $this->mysqli->error);

            $this->alterColumn($this->mysqli->error, $values, $columns);
            $this->insert($storeData);
        }
    }

    public function delete() {
        $this->write();
        $this->addWhere('DELETE FROM ' . $this->store);

        if ($this->mysqli->query($this->sql)) {
            $this->mysqli->insert_id;
        } else {
            return false;
        }
    }

    public function where($fieldName, $condition, $value, $assign = 'AND') {
        $tmp = new \stdClass();
        $tmp->field = $fieldName;
        $tmp->condition = $condition;
        $tmp->value = $value;

        array_push($this->where, $tmp);
        $this->whereAssign = $assign;

        return $this;
    }

    public function one() {
        $this->one = true;
        return $this;
    }

    public function like($fieldName, $value, $assign = 'AND') {
        $tmp = new \stdClass();
        $tmp->field = $fieldName;
        $tmp->value = '%' . $value . '%';

        array_push($this->like, $tmp);
        $this->whereAssign = $assign;
        return $this;
    }

    public function groupBy($fieldName) {
        $this->groupBy = $fieldName;
        // $this->presql = 'SELECT ' . $fieldName . ' FROM ' . $this->store . ' ';

        return $this;
    }

    public function orderBy($order, $orderBy = 'id') {
        $tmp = new \stdClass();
        $tmp->order = $order;
        $tmp->field = $orderBy;

        array_push($this->orderBy, $tmp);

        return $this;
    }

    public function limit($limit = 0) {
        $this->limit = $limit;
        if ($limit && !$this->skip) {
            $this->skip(0);
        }

        return $this;
    }

    public function skip($skip) {
        $this->skip = $skip;
        return $this;
    }

    public function count() {
        $this->read();
        $this->calc = true;
        $this->presql = 'SELECT count(1) as calc FROM ' . $this->store . ' ';

        return $this;
    }

    public function sum($field) {
        $this->read();
        $this->calc = true;
        $this->presql = 'SELECT sum(' . $field . ') as calc FROM ' . $this->store . ' ';
        return $this;
    }

    public function sql($sql) {
        $this->fullsql = $sql;
        return $this;
    }

    public function debug() {
        $this->debug = true;
        return $this;
    }

    public function stats() {
        echo 'Read: ' . $_SESSION['read'] . ', Write: ' . $_SESSION['write'];
    }

    public function fetch() {

        $this->read();

        if (!$this->presql) {
            $this->sql = 'SELECT * FROM ' . $this->store . ' ';
        } else {
            $this->sql = $this->presql;
        }

        $this->addWhere($this->sql);

        // GROUP BY
        if ($this->groupBy) {
            $this->sql .= 'GROUP BY ' . $this->groupBy;
            $this->sql .= ' ';
        }

        // ORDER BY
        if (count($this->orderBy)) {
            $this->sql .= 'ORDER BY ';
            $this->tmp = [];
            foreach ($this->orderBy as $order) {
                array_push($this->tmp, $order->field . ' ' . $order->order);
            }
            $this->sql .= implode(', ', $this->tmp);
            $this->sql .= ' ';
        }

        // LIMIT
        if ($this->limit) {
            $this->sql .= 'LIMIT ' . $this->skip . ', ' . $this->limit;
        }

        if ($this->fullsql) {
            $this->sql = $this->fullsql;
        }

        if ($this->debug) {
            print_r($this);
            die();
        }

        if (!$raw = $this->mysqli->query($this->sql)) {
            $this->alterColumn($this->mysqli->error);
        }

        $tmp = [];

        if (!$raw) {
            return;
            //die('ERROR: ' . mysqli_error($this->mysqli));
        }

        while ($item = mysqli_fetch_assoc($raw)) {
            array_push($tmp, $this->reBuild($item));
        }

        $this->records = $tmp;

        if ($this->one) {
            return $this->records[0];
        } else {
            return ($this->calc ? number_format($this->records[0]['calc'], 0, ',', ',') : $this->records);
        }
    }
}

$DB = new \MySQLDB\MySQLDB();