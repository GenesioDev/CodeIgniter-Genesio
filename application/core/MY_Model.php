<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    /**
     * Table used by this model
     *
     * @var string
     */
    protected $table;

    /**
     * Primary use by this table
     *
     * @var integer
     */
    protected $pKey = 'id';

    /**
     * Liste fields for save and create
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * MY_Model constructor.
     *
     * @param int $id
     */
    public function __construct($id = 0)
    {
        parent::__construct();

        $this->generateProperty();
        $this->_class = get_class($this);

        if($id) {
            $this->getById($id);
        }
    }

    /**
     * Give the unique row with this id
     *
     * @param $id
     * @return $this
     */
    public function getById($id)
    {
        $this->hydrate(
            $this->db->select('*')
            ->from($this->table)
            ->where($this->pKey, $id)
            ->get()
            ->result()
        );

        return $this;
    }

    /**
     * Get function to do a select query
     *
     * @param array $where
     * @param null $limit
     * @param int $offset
     * @param null $orderBy
     * @param string $orderDir
     * @return $this
     */
    public function get($where = array(), $limit = NULL, $offset = 0, $orderBy = null, $orderDir = 'DESC')
    {
        $this->db->select('*');
        $this->db->from($this->table);
        if (count($where) > 0) {
            foreach($where as $field => $value) {
                $this->db->where($field, $value);
            }
        }
        if ($orderBy != null) {
            $this->db->order_by($orderBy, $orderDir);
        }
        $this->db->limit($limit, $offset);

        $this->hydrate($this->db->get()->result());

        return $this;
    }

    /**
     * Delete object from database
     *
     * @return bool
     */
    public function delete()
    {
        if($this->exists()) {
            return (bool)$this->db->where($this->pKey, $this->{$this->pKey})
                ->delete($this->table);
        }

        return FALSE;
    }

    /**
     * Delete all selected object
     */
    public function deleteAll()
    {
        foreach($this->all as $o) {
            $this->db->where($o->pKey, $o->{$o->pKey})
                ->delete($this->table);
        }

        return $this;
    }

    /**
     * Count row in the table compared to where conditions
     *
     * @param array $where
     * @return int
     */
    public function count($where = array()) {
        $this->db->from($this->table);

        if (count($where) > 0) {
            foreach($where as $field => $value) {
                $this->db->where($field, $value);
            }
        }

        return (int) $this->db->count_all_results();
    }

    /**
     * Save data (create or update if pKey exist)
     *
     * @return $this|bool
     */
    public function save() {
        $aData = array();

        foreach($this->_fields as $field) {
            $aData[$field] = $this->$field;
        }

        if($this->exists()) {
            return $this->update($aData);
        } else {
            if($this->create($aData)) {
                $this->id = $this->db->insert_id();
                return TRUE;
            }
            return FALSE;
        }

        return $this;
    }

    /**
     * Check if we have an available data in object
     *
     * @return bool
     */
    public function exists() {
        if($this->{$this->pKey}) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Get column from this table to do property
     */
    protected function generateProperty()
    {
        // Desc of this table
        $query = $this->db->query('DESC ' . $this->table);

        foreach($query->result() as $item) {
            $this->{$item->Field} = NULL;

            if($item->Field != $this->pKey && !in_array($item->Field, $this->_fields)) {
                $this->_fields[] = $item->Field;
            }
        }
    }

    /**
     * Hydrate model object
     *
     * @param array $data
     */
    protected function hydrate($data = array())
    {
        if(count($data) > 0) {
            /*echo '<pre>';
            var_dump($data);
            echo '</pre>';
            die();*/

            // First element to hydrate current object
            foreach($data[0] as $key => $value) {
                $this->$key = $value;
            }

            // All element in all propriety
            $this->all = array();
            foreach($data as $object) {
                $aObject = new $this->_class();
                foreach ($object as $key => $value) {
                    $aObject->$key = $value;
                }
                $this->all[] = $aObject;
            }
        }
    }

    /**
     * Insert a new row in db
     *
     * @param array $escaped_fields
     * @param array $not_escaped_fields
     * @return bool
     */
    protected function create($escaped_fields = array(), $not_escaped_fields = array())
    {
        if(empty($escaped_fields) AND empty($not_escaped_fields)) { return false; }

        return (bool) $this->db->set($escaped_fields)
            ->set($not_escaped_fields, null, false)
            ->insert($this->table);
    }

    /**
     * Update a row in db
     *
     * @param array $escaped_fields
     * @param array $not_escaped_fields
     * @return bool
     */
    protected function update($escaped_fields = array(), $not_escaped_fields = array())
    {
        if(empty($escaped_fields) AND empty($not_escaped_fields)) { return false; }

        return (bool) $this->db->set($escaped_fields)
            ->set($not_escaped_fields, null, false)
            ->where($this->pKey, $this->{$this->pKey})
            ->update($this->table);
    }
}

/* End of file MY_Model.php */
/* Location: ./system/application/core/MY_Model.php */