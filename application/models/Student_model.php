<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student_model extends CI_Model {

    protected $table = 'tbl_students';
    protected $primaryKey = 'student_id';

    public function get_all($filters = array())
    {
        $this->db
            ->select('st.*, c.class_name, sec.section_name, y.year_name')
            ->from('tbl_students st')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_sections sec', 'sec.section_id = st.section_id', 'left')
            ->join('tbl_academic_years y', 'y.academic_year_id = st.academic_year_id', 'left')
            ->where('st.status >=', 0)
            ->order_by('st.student_id', 'ASC');

        if (!empty($filters['class_id'])) {
            $this->db->where('st.class_id', $filters['class_id']);
        }
        if (!empty($filters['section_id'])) {
            $this->db->where('st.section_id', $filters['section_id']);
        }
        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'All') {
            $this->db->where('st.status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $this->db->group_start()
                ->like('st.first_name', $s)
                ->or_like('st.last_name', $s)
                ->or_like('st.admission_number', $s)
                ->or_like('st.guardian_name', $s)
                ->or_like('st.guardian_phone', $s)
                ->group_end();
        }

        return $this->db->get()->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('st.*, c.class_name, sec.section_name, y.year_name')
            ->from('tbl_students st')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_sections sec', 'sec.section_id = st.section_id', 'left')
            ->join('tbl_academic_years y', 'y.academic_year_id = st.academic_year_id', 'left')
            ->where('st.student_id', $id)
            ->get()
            ->row();
    }

    public function get_profile($id)
    {
        $student = $this->get_by_id($id);
        if ($student) {
            $student->documents = $this->db
                ->where('student_id', $id)
                ->where('status', 1)
                ->get('tbl_student_documents')
                ->result();
        }
        return $student;
    }

    public function count_students($academic_year_id = NULL)
    {
        if ($academic_year_id) {
            $this->db->where('academic_year_id', $academic_year_id);
        }
        return $this->db
            ->where('status', 1)
            ->count_all_results($this->table);
    }

    public function get_gender_stats()
    {
        $boys = $this->db
            ->where('gender', 'Male')
            ->where('status', 1)
            ->count_all_results($this->table);

        $girls = $this->db
            ->where('gender', 'Female')
            ->where('status', 1)
            ->count_all_results($this->table);

        return array('boys' => $boys, 'girls' => $girls);
    }

    public function get_new_admissions_count($month = NULL, $year = NULL)
    {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        return $this->db
            ->where('MONTH(created_at)', $month)
            ->where('YEAR(created_at)', $year)
            ->where('status', 1)
            ->count_all_results($this->table);
    }

    public function get_grade_distribution()
    {
        return $this->db
            ->select('c.class_name, COUNT(st.student_id) as count')
            ->from('tbl_classes c')
            ->join('tbl_students st', 'st.class_id = c.class_id AND st.status = 1', 'left')
            ->where('c.status', 1)
            ->group_by('c.class_id')
            ->order_by('c.class_id', 'ASC')
            ->limit(4)
            ->get()
            ->result();
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
