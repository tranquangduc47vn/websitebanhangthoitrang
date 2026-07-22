<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends MY_Admin_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('transaction_model');
        $this->load->model('product_model');
        $this->load->helper('permission');
        if (!admin_can('order.manage', $this->session->userdata('login'))) {
            $this->session->set_flashdata('message_fail', 'Bạn không có quyền xem báo cáo đơn hàng.');
            redirect(admin_url('home'));
        }
    }

    public function index()
    {
        $type      = $this->input->get('type');
        $date_from = $this->input->get('date_from');
        $date_to   = $this->input->get('date_to');

        // Chỉ đơn hoàn thành (status 3)
        $where_transaction = "status = 3";

        $start = 0;
        $end   = 0;

        if (!empty($type))
        {
            switch ($type)
            {
                case 'day':

                    $start = strtotime(date('Y-m-d 00:00:00'));
                    $end   = strtotime(date('Y-m-d 23:59:59'));

                    break;

                case 'week':

                    $start = strtotime('monday this week');
                    $end   = strtotime('sunday this week 23:59:59');

                    break;

                case 'month':

                    $start = strtotime(date('Y-m-01 00:00:00'));
                    $end   = strtotime(date('Y-m-t 23:59:59'));

                    break;

                case 'year':

                    $start = strtotime(date('Y-01-01 00:00:00'));
                    $end   = strtotime(date('Y-12-31 23:59:59'));

                    break;
            }
        }

        else
        {
            if (!empty($date_from))
            {
                $start = strtotime($date_from . ' 00:00:00');
            }

            if (!empty($date_to))
            {
                $end = strtotime($date_to . ' 23:59:59');
            }
        }

        if ($start && $end)
        {
            $where_transaction .= " AND created BETWEEN $start AND $end";
        }
        elseif ($start)
        {
            $where_transaction .= " AND created >= $start";
        }
        elseif ($end)
        {
            $where_transaction .= " AND created <= $end";
        }

        // Alias cột cho JOIN transaction t
        $where_transaction_t = str_replace(
            array('status','created'),
            array('t.status','t.created'),
            $where_transaction
        );

        $sql = "
            SELECT
                COUNT(id) total_orders,
                SUM(amount) total_revenue
            FROM transaction
            WHERE $where_transaction
        ";

        $result = $this->db->query($sql)->row();

        $this->data['total_orders'] =
            $result->total_orders ? $result->total_orders : 0;

        $this->data['total_revenue'] =
            $result->total_revenue ? $result->total_revenue : 0;

        $sql = "
            SELECT SUM(o.qty) total_qty
            FROM `order` o
            JOIN transaction t
            ON o.transaction_id=t.id
            WHERE $where_transaction_t
        ";

        $result = $this->db->query($sql)->row();

        $this->data['total_qty_sold'] =
            $result->total_qty ? $result->total_qty : 0;

        $sql = "
        SELECT
            p.id,
            p.name,
            p.quantity,

            (
                SELECT SUM(o.qty)
                FROM `order` o
                JOIN transaction t
                ON o.transaction_id=t.id
                WHERE
                    o.product_id=p.id
                    AND $where_transaction_t
            ) da_ban,

            COALESCE(

                (
                    SELECT o.amount/o.qty
                    FROM `order` o
                    JOIN transaction t
                    ON o.transaction_id=t.id
                    WHERE
                        o.product_id=p.id
                        AND $where_transaction_t
                    LIMIT 1
                ),

                p.price

            ) price

        FROM product p

        ORDER BY da_ban DESC
        ";

        $this->data['products_report'] =
            $this->db->query($sql)->result();

        $chart = array();

        $year = date('Y');

        for($m=1;$m<=12;$m++)
        {
            $month = str_pad($m,2,'0',STR_PAD_LEFT);

            $start_month = strtotime("$year-$month-01 00:00:00");

            $end_month = strtotime(
                date(
                    'Y-m-t 23:59:59',
                    strtotime("$year-$month-01")
                )
            );

            $sql = "
            SELECT SUM(amount) revenue
            FROM transaction
            WHERE
                status=3
                AND created BETWEEN
                $start_month
                AND
                $end_month
            ";

            $row = $this->db->query($sql)->row();

            $chart[] = $row->revenue ? (int)$row->revenue : 0;
        }

        $this->data['chart_data'] = json_encode($chart);

        $this->data['current_type'] = $type;

        $this->render_admin('admin/report/index', array('admin_load_chart' => true));
    }
}