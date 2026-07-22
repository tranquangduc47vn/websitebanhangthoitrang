<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property CI_Config $config
 * @property CI_Input $input
 */
class MY_Model extends CI_Model {
    
    var $table = '';
    var $key = 'id';
    
    var $order = array('id', 'DESC');
    var $select = '';

    function create($data = array())
    {
        return $this->db->insert($this->table, $data);
    }

    function update($id, $data)
    {
        if (!$id) return FALSE;

        $where = array();
        $where[$this->key] = $id;

        $this->update_rule($where, $data);
        return TRUE;
    }

    function update_rule($where, $data)
    {
        if (!$where) return FALSE;

        $this->db->where($where);
        $this->db->update($this->table, $data);

        return TRUE;
    }

    function delete($id)
    {
        if (!$id) return FALSE;

        if (is_numeric($id)) {
            $where = array($this->key => $id);
        } else {
            $where = $this->key . " IN (".$id.") ";
        }

        $this->del_rule($where);
        return TRUE;
    }

    function del_rule($where)
    {
        if (!$where) return FALSE;

        $this->db->where($where);
        $this->db->delete($this->table);

        return TRUE;
    }

    function get_info($id, $field = '')
    {
        if (!$id) return FALSE;

        $where = array();
        $where[$this->key] = $id;

        return $this->get_info_rule($where, $field);
    }

    function get_info_rule($where = array(), $field= '')
    {
        if($field) {
            $this->db->select($field);
        }

        $this->db->where($where);
        $query = $this->db->get($this->table);

        return ($query->num_rows()) ? $query->row() : FALSE;
    }

    function get_total($input = array())
    {
        $this->get_list_set_input($input);
        return $this->db->get($this->table)->num_rows();
    }

    function get_sum($field, $where = array())
    {
        $this->db->select_sum($field);
        $this->db->where($where);
        $this->db->from($this->table);

        $row = $this->db->get()->row();

        return $row ? array_values((array)$row)[0] : 0;
    }

    function get_row($input = array())
    {
        $this->get_list_set_input($input);
        return $this->db->get($this->table)->row();
    }

    function get_list($input = array())
    {
        $this->get_list_set_input($input);
        return $this->db->get($this->table)->result();
    }

    protected function get_list_set_input($input = array())
    {
        if (!empty($input['where'])) {
            $this->db->where($input['where']);
        }

        if (!empty($input['where_in'])) {
            $this->db->where_in(
                $input['where_in'][0],
                $input['where_in'][1]
            );
        }

        if (!empty($input['like'])) {
            $this->db->like($input['like'][0], $input['like'][1]);
        }

     if (isset($input['order_raw']))
{
    $this->db->order_by($input['order_raw'], '', FALSE);
}
elseif (isset($input['order'][0]) && isset($input['order'][1]))
{
    $this->db->order_by($input['order'][0], $input['order'][1]);
} else {
            $this->db->order_by($this->order[0], $this->order[1]);
        }

        if (isset($input['limit'][0]) && isset($input['limit'][1])) {
            $this->db->limit($input['limit'][0], $input['limit'][1]);
        }
    }

    function check_exists($where = array())
    {
        $this->db->where($where);
        $query = $this->db->get($this->table);

        return ($query->num_rows() > 0);
    }
}
