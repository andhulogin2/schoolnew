<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Certificate_model extends CI_Model {

    protected $table = 'tbl_certificates';
    protected $primaryKey = 'certificate_id';

    public function get_all()
    {
        return $this->db
            ->select('cert.*, st.first_name, st.last_name, st.admission_number, c.class_name, sec.section_name')
            ->from('tbl_certificates cert')
            ->join('tbl_students st', 'st.student_id = cert.student_id', 'left')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_sections sec', 'sec.section_id = st.section_id', 'left')
            ->where('cert.status', 1)
            ->order_by('cert.certificate_id', 'ASC')
            ->get()
            ->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('cert.*, st.first_name, st.last_name, st.admission_number, c.class_name, sec.section_name')
            ->from('tbl_certificates cert')
            ->join('tbl_students st', 'st.student_id = cert.student_id', 'left')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_sections sec', 'sec.section_id = st.section_id', 'left')
            ->where('cert.certificate_id', $id)
            ->get()
            ->row();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, $data);
    }

    public function soft_delete($id)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, array('status' => 0));
    }
}
