<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Admin_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function index()
    {
        // Đơn mới: status 0
        $this->data['total_order_new'] = $this->db
            ->where('status', 0)
            ->count_all_results('transaction');

        $this->data['total_order_all'] = $this->db
            ->count_all_results('transaction');

        // Doanh thu đơn hoàn thành: status 3
        $revenue = $this->db
            ->select_sum('amount')
            ->where('status', 3)
            ->get('transaction')
            ->row();

        $this->data['total_revenue'] = !empty($revenue->amount) ? $revenue->amount : 0;

        $this->data['total_product'] = $this->db
            ->count_all_results('product');

        $this->data['total_user'] = $this->db
            ->count_all_results('user');

        $this->data['recent_orders'] = $this->db
            ->select('id, user_name, user_phone, user_email, amount, payment, status, created')
            ->from('transaction')
            ->order_by('id', 'DESC')
            ->limit(8)
            ->get()
            ->result();

        $this->data['top_products'] = $this->db->query("
            SELECT 
                p.id,
                p.name,
                p.price,
                p.image_link,
                SUM(o.qty) AS total_sold,
                SUM(o.amount) AS total_amount
            FROM `order` o
            INNER JOIN `product` p ON o.product_id = p.id
            INNER JOIN `transaction` t ON o.transaction_id = t.id
            WHERE t.status = 3
            GROUP BY p.id, p.name, p.price, p.image_link
            ORDER BY total_sold DESC
            LIMIT 5
        ")->result();

        $year = date('Y');

        $monthly_data = $this->db->query("
            SELECT 
                MONTH(FROM_UNIXTIME(created)) AS month,
                SUM(amount) AS revenue
            FROM `transaction`
            WHERE status = 3
            AND YEAR(FROM_UNIXTIME(created)) = {$year}
            GROUP BY MONTH(FROM_UNIXTIME(created))
            ORDER BY month ASC
        ")->result();

        $monthly_revenue = array_fill(1, 12, 0);

        foreach ($monthly_data as $row) {
            if (!empty($row->month)) {
                $monthly_revenue[(int)$row->month] = (float)$row->revenue;
            }
        }

        $this->data['monthly_labels'] = array(
            'T1', 'T2', 'T3', 'T4', 'T5', 'T6',
            'T7', 'T8', 'T9', 'T10', 'T11', 'T12'
        );

        $this->data['monthly_revenue'] = array_values($monthly_revenue);

        $this->render_admin('admin/home/index', array('admin_load_chart' => false));
    }
}
