<?php
function getSqls(){

    $sql = \DB::getQueryLog ();
    $_SQL = [];
    foreach ($sql as $k=>$v){
        if(!isset($_SQL [$v['query']])){
            $_SQL [$v['query']] = 1;
        }else{
            $_SQL [$v['query']] ++;
        }
    }

    edump($_SQL);

}




if (! function_exists ( 'createInsertSql' )) {

    /**
     * Create An Insert Sql Statement
     *
     * @param string $tbname
     * @param array $data
     * @return string
     */
    function createInsertSql($tbname, array $data) {
        $fields = implode ( '`,`', array_keys ( $data ) );
        $values = implode ( '\',\'', array_values ( $data ) );
        $sql = 'insert into `' . $tbname . '`(`' . $fields . '`)values(\'' . $values . '\')';
        return $sql;
    }

    /**
     * Create An Insert Sql Statement With Param Placeholder
     *
     * @param string $tbname
     * @param array $data
     * @return multitype:string multitype:
     */
    function createInsertSqlBind($tbname, array $data) {
        $keys = array_keys ( $data );
        $values = array_values ( $data );
        $fields = implode ( '`,`', $keys );
        $places = array_fill ( 0, count ( $keys ), '?' );
        $places = implode ( ',', $places );
        $sql = 'insert into `' . $tbname . '`(`' . $fields . '`)values(' . $places . ')';
        return [
            'sql' => $sql,
            'data' => $values
        ];
    }
}

if (! function_exists ( 'createUpdateSql' )) {

    /**
     * Create A Update Sql Statement
     *
     * @param string $tbname
     * @param array $data
     * @param string $where
     * @return string
     */
    function createUpdateSql($tbname, array $data, $where = '') {
        $set = '';
        $wh = '';
        foreach ( $data as $k => $v ) {
            $set .= ',`' . $k . '` = \'' . $v . '\'';
        }
        if (is_array ( $where )) {
            foreach ( $where as $k => $v ) {
                $wh .= ' and `' . $k . '` = \'' . $v . '\'';
            }
            $wh = substr ( $wh, 4 );
        } else {
            $wh = $where;
        }
        $wh = empty ( $wh ) ? $wh : ' WHERE ' . $wh;
        $set = substr ( $set, 1 );
        $sql = 'UPDATE `' . $tbname . '` SET ' . $set . $wh;
        return $sql;
    }
}


if (! function_exists ( 'insert' )) {

    /**
     * Execute Insert Sql Statment
     *
     * @param unknown $table
     * @param array $data
     */
    function insert($table, array $data) {
        $result = createInsertSqlBind ( $table, $data );
        return DB::insert ( $result ['sql'], $result ['data'] );
    }
}

if (! function_exists ( 'update' )) {

    /**
     * Execute Update Sql Statment
     *
     * @param unknown $table
     * @param array $data
     * @param unknown $where
     */
    function update($table, array $data, $where) {
        $sql = createUpdateSql ( $table, $data, $where );
        return DB::update ( $sql );
    }
}
if (! function_exists ( 'lastInsertId' )) {

    /**
     * Get Last Insert Id
     */
    function lastInsertId() {
        return DB::getPdo ()->lastInsertId ();
    }
}
if (! function_exists ( 'lastSql' )) {

    /**
     * Get Last Query
     *
     * @return mixed
     */
    function lastSql() {
        $sql = DB::getQueryLog ();
        $query = end ( $sql );
        return $query;
    }

    function dumpQuserys() {
        $sqls = DB::getQueryLog ();
        dump($sqls);
    }
    function edumpQuserys() {
        $sqls = DB::getQueryLog ();
        dump($sqls);
        exit;
    }
}



if (! function_exists ( 'sql' )) {

    /**
     * Echo An Sql Statment Friendly
     *
     * @param string $subject
     *        	Sql Statment
     * @param array $binds
     *        	The Bind Params
     * @return unknown
     */
    function sql($subject, array $binds = []) {
        $pattern = '/(select\s+|from\s+|where\s+|and\s+|or\s+|\s+limit|,|(?:left|right|inner)\s+join)/i';

        $var = preg_replace ( $pattern, '<br/>\\1', $subject );

        $i = 0;

        $binds && $var = preg_replace_callback ( '/\?/', function ($matchs) use(&$i, $binds) {
            return '\'' . $binds [$i ++] . '\'';
        }, $var );

            echo $var . '<br/>';
    }

    /**
     * Echo Last Sql
     */
    function sqlLastSql() {
        $query = lastSql ();
        sql ( $query ['query'], $query ['bindings'] );
    }

    /**
     * Echo Last Sql And Exit
     */
    function esqlLastSql() {
        $query = lastSql ();
        sql ( $query ['query'], $query ['bindings'] );
        exit ();
    }
}


if (! function_exists('groupInsert')) {

    /**
     * Insert A Group Of Values
     *
     * @param unknown $k
     * @param unknown $v
     */
    function fireInsert($k, $v)
    {
        $fields = implode('`,`', array_keys($v[0]));
        $sql = 'insert into `' . $k . '`(`' . $fields . '`)values';
        foreach ($v as $obj) {
            $values[] = '(\'' . implode('\',\'', array_values($obj)) . '\')';
        }
        $sql .= implode(',', $values);
        \DB::insert($sql);
    }

    /**
     * Group Insert
     *
     * @param string $tbname
     * @param unknown $inputDate
     */
    function groupInsert($tbname = '', $inputDate = [])
    {
        static $_queue = [];
        if ($tbname == '[fire]') {
            fireInsert($k, $v);
            $_queue = [];
        } else
            if (substr_replace($tbname, '', 1, strlen($tbname) - 2) == '[]') {
                $tbname = substr($tbname, 1, strlen($tbname) - 2);
                fireInsert($tbname, $_queue[$tbname]);
                unset($_queue[$tbname]);
            } else {
                $_queue[$tbname][] = $inputDate;
                if (count($_queue[$tbname]) > 8000) {
                    groupInsert('[' . $tbname . ']');
                }
            }
    }
}

