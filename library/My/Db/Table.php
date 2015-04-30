<?php
/**
 *
 * Enter description here ...
 * @author berenice
 *
 */

/**
 *
 * Enter description here ...
 * @author berenice
 *
 */
class My_Db_Table extends Zend_Db_Table_Abstract
{
    /**
     * Total de registros devueltos
     *
     * @var integer
     */
    private $_total   = 0;

    /**
     * Total de registros devueltos en el fetchRows
     *
     * @var unknown_type
     */
    private $_totalRows;

    /**
     * Obtiene el total de registros y obtiene el TOTAL de elementos en la tabla!
     *
     * @param mixed $where
     * @param mixed $order
     * @param mixed $page Pagina
     * @param mixed $size Tamaño de pagina
     * @return mixed Arreglo de elementos
     */
    public function fetchRows($where = null, $order = null, $page = null,$size = null){

        if($where instanceof Zend_Db_Select ){
            $select = $where;
        }else if (!($where instanceof Zend_Db_Table_Select)) {
            $select = $this->select();

            if ($where !== null) {
                $this->_where($select, $where);
            }

            if ($order !== null) {
                $this->_order($select, $order);
            }

            if ($page !== null || $size !== null) {
                $select->limitPage($page, $size);/*Page size*/
            }

        } else {
            $select = $where;
        }
        ;
        $select  = str_replace("SELECT ","SELECT SQL_CALC_FOUND_ROWS ",$select->__toString());
        $result = $this->getAdapter()->query($select)->fetchAll();

        $total = $this->getAdapter()->query("SELECT FOUND_ROWS();")->fetchColumn();

        $this->_setTotalRows($total);

        if(!is_array($result)){
            $result = array(0=>(array)$result);
        }
        return $result;
    }

    /**
     * Asigna valor a variable total
     *
     * @param integer $total
     * @return void
     */
    protected function _setTotal($total = 0){
        $this->_total = $total;
    }

    /**
     * Obtiene el total de registros generados por una consulta
     *
     * @return integer
     */
    public function getTotal(){ 
        return $this->_total;
    }
    
    /**
     * Estabblece el total de elementos en la consulta
     * @param int $total Total de elementos
     * @return void
     */
    protected function _setTotalRows($total)
    {
        $this->_totalRows = $total;
    }
    
    /**
     * Obtiene el total de elementos de la consulta antes realizada
     *
     * @return int
     */
    public function getTotalRows()
    {
        return $this->_totalRows;
    }
    
    /*
     * Carga un query sin cache
     */
    public function querySinCache($sql){
    	$db  = $this->getAdapter();
        //echo ($sql);
        $results  = $db->query($sql)->fetchAll();
        
        return $results;
    }
     

    /**
     * Carga la query en caso de que exista en caché, de lo contrario accede a bd
     * y carga el resultado de la query en caché 
     *
     * @param string $sql
     * @return array|stdClass
     */
    public function query($sql,$fetch=true){
    	
        $db           = $this->getAdapter();
        if($fetch){
            $stmt         = $db->query($sql)->fetchAll();     
            if(!is_array($stmt)) $stmt = array(0=>(array)$stmt);        
            return $stmt;        
        }else{
            return $db->query($sql);   
        }
    }

    /**
     * Devolverá falso si no es vista o definición de vista en SQL
     * @param string $view  Nombre de posible vista
     * @return bool|string
     */
    private function _getViewDefinition($view){
        /*  $sql      = "SELECT VIEW_DEFINITION FROM information_schema.VIEWS WHERE TABLE_NAME = '" . $view . "'";
         $adapter  = $this->getDefaultAdapter();
         $bodyView = $adapter->fetchRow($sql);
         return ($bodyView == false)?false:$bodyView['VIEW_DEFINITION']; */
        return false;
    }

    /**
     * Obtiene objeto con las tablas afectadas por la consulta
     *
     * @param string|Zend_Db_Select $sql
     * @throws Falta de schema
     * @return stdClass
     */
    private function _sqlTables($sql)
    {
        $sql    = str_replace('(', '', $sql);
        $sql    = str_replace('`', '', $sql);
        $prevcl = preg_split("/[\s,]+/", trim($sql));
        $tables = array();
        $from   = false;
        if (trim($this->_schema) == '') {
            throw new Exception('Se debe definir esquema en el model', 777);
        }
        foreach($prevcl as $string) {
            if ($from) {
                $string = str_replace($this->_schema . '.', '', $string);
                $tables[$string] = 1;
                $from     = false;
            }
            $from |=  (in_array(strtolower($string), array('from', 'join')));
        }
        return (object)$tables;
    }

    /**
     * Inserts a new row.
     *
     * @param  array  $data  Column-value pairs.
     * @return mixed         The primary key of the row inserted.
     */
    public function insert(array $data)
    {
        $primary = parent::insert($data);
        $this->_actualizaCambios();
        return $primary;
    }

    /**
     * Updates existing rows.
     *
     * @param  array        $data  Column-value pairs.
     * @param  array|string $where An SQL WHERE clause, or an array of SQL WHERE clauses.
     * @return int          The number of rows updated.
     */
    public function update(array $data, $where)
    {
        $numrows = parent::update($data, $where);

        // Si se realizaron cambios, borrar id de Cache
        if ($numrows > 0 ) {
            $this->_actualizaCambios();
        }
        return $numrows;
    }
    /**
     * Deletes existing rows.
     *
     * @param  array|string $where SQL WHERE clause(s).
     * @return int          The number of rows deleted.
     */
    public function delete($where)
    {
        $numrows = parent::delete($where);

        // Si se realizaron cambios, borrar id de Cache
        if ($numrows > 0 ) {
            $this->_actualizaCambios();
        }

        return $numrows;
    }

    /**
     *
     * Método que se encarga de remover cache en caso de existir ese id cuando se hagan cambios
     */
    private function _actualizaCambios()
    {
        $cacheModel   = Zend_Registry::getInstance()->cacheAdapter['models'];
        $idCacheTabla = $this->_schema . '_' . $this->_name;
        $idCacheAsoc  = $cacheModel->load($idCacheTabla);
        if ($idCacheAsoc !== false) {
            foreach($idCacheAsoc as $idCacheQuery=>$algo) {
                if (false !== $cacheModel->test($idCacheQuery)) {
                    $cacheModel->remove($idCacheQuery);
                }
            }
        }
    }
}
