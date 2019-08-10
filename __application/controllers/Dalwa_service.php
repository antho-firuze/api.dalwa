<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

/**
 * Dalwa Service Class
 *
 * This class contains various functions for Dalwa Service Agent
 *
 */
class Dalwa_service extends CI_Controller 
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_SYSTEM);
        $this->load->library('f');
        $this->load->helper('logger');
	}
    
    function logger($message='NONAME')
    {
        if (PHP_OS === 'WINNT')
            logme('scheduler', 'info', "Method [$message]");
    }

    function _find_first_array(Array $array, $key, $val)
    {
        $array = (array) $array;

        foreach ($array as $subarray){  
            $subarray = (array) $subarray;
            if (isset($subarray[$key]) && $subarray[$key] == $val)
                return $subarray;       
        } 
    }

    function generate_bill($client_id)
    {
		$this->db->trans_strict(TRUE);
		$this->db->trans_start();

        // $client_id = 1;

        if (!$result = $this->db->get_where('bill_generate', ['period_y' => date('Y'), 'period_m' => date('m')]))
            die('Database Error: '.$this->db->error()['message']);

        if ($period = $result->row()) {
            if ($period->status == 'success') {
                $this->logger(__FUNCTION__);
                die('Billing already generated');
            }
        }

        if (!$result = $this->db->get_where('c_partner', ['client_id' => $client_id, 'is_active' => 'Y', 'is_student' => 'Y']))
            die('Database Error: '.$this->db->error()['message']);

        if (!$rows = $result->result()) {
            $this->logger(__FUNCTION__);
            die('No Billing Generated');
        }

        if (!$result = $this->db->get_where('bill_type', ['client_id' => $client_id, 'is_mandatory' => 'Y']))
            die('Database Error: '.$this->db->error()['message']);

        if (!$bill_types = $result->result()) {
            $this->logger(__FUNCTION__);
            die('No Billing Generated');
        }

        foreach ($rows as $r) {
            $data = array();
            //1. Syahria Pondok
            $bill_type = (object) $this->_find_first_array($bill_types, 'bill_type_id', 1);
            $data[] = [
                'client_id'      => $r->client_id,
                'partner_id'     => $r->partner_id,
                'bill_status_id' => 1,
                'bill_type_id'   => $bill_type->bill_type_id,
                'desc'           => $bill_type->name,
                'due_date'       => date('Y-m-d'),
                'amount'         => $bill_type->amount,
                'is_auto'        => 'Y',
                'created_at'     => date('Y-m-d H:i:s'),
            ];
            //2. Syahria Umum
            $bill_type = (object) $this->_find_first_array((array) $bill_types, 'class', $r->class);
            $data[] = [
                'client_id'      => $r->client_id,
                'partner_id'     => $r->partner_id,
                'bill_status_id' => 1,
                'bill_type_id'   => $bill_type->bill_type_id,
                'desc'           => $bill_type->name,
                'due_date'       => date('Y-m-d'),
                'amount'         => $bill_type->amount,
                'is_auto'        => 'Y',
                'created_at'     => date('Y-m-d H:i:s'),
            ];
            //3. Sumbangan Tanah
            $bill_type = (object) $this->_find_first_array((array) $bill_types, 'bill_type_id', 5);
            $data[] = [
                'client_id'      => $r->client_id,
                'partner_id'     => $r->partner_id,
                'bill_status_id' => 1,
                'bill_type_id'   => $bill_type->bill_type_id,
                'desc'           => $bill_type->name,
                'due_date'       => date('Y-m-d'),
                'amount'         => $bill_type->amount,
                'is_auto'        => 'Y',
                'created_at'     => date('Y-m-d H:i:s'),
            ];
            //4. Laundry
            if ($r->join_laundry == 'Y') {
                $bill_type = (object) $this->_find_first_array((array) $bill_types, 'bill_type_id', 6);
                $data[] = [
                    'client_id'      => $r->client_id,
                    'partner_id'     => $r->partner_id,
                    'bill_status_id' => 1,
                    'bill_type_id'   => $bill_type->bill_type_id,
                    'desc'           => $bill_type->name,
                    'due_date'       => date('Y-m-d'),
                    'amount'         => $bill_type->amount,
                    'is_auto'        => 'Y',
                    'created_at'     => date('Y-m-d H:i:s'),
                ];
            }

            $this->db->insert_batch('bill', $data);

            // Mobile Notification 
            // Process.....
        }

        $this->db->trans_complete();
        //Failed
		if ($this->db->trans_status() === FALSE)
		{
            if ($period) {
                $this->db->update('bill_generate', [
                    'status'     => 'failed', 
                    'error_msg'  => $this->db->error()['message'],
                ], ['bill_generate_id' => $period->bill_generate_id]);
            } else {
                $this->db->insert('bill_generate', [
                    'client_id'  => $client_id,
                    'period_y'   => date('Y'),
                    'period_m'   => date('m'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'status'     => 'failed',
                    'error_msg'  => $this->db->error()['message'],
                ]);
            }
            print_r($this->db->error()['message']);
            die('Billing generate failed');
            // return [FALSE, ['message' => $this->db->last_query()]];
			// return [FALSE, ['message' => $this->db->error()['message']]];
        }
        
        //Success
        if ($period) {
            $this->db->update('bill_generate', [
                'status'     => 'success',
                'error_msg'  => null,
            ], ['bill_generate_id' => $period->bill_generate_id]);
        } else {
            $this->db->insert('bill_generate', [
                'client_id'  => $client_id,
                'period_y'   => date('Y'),
                'period_m'   => date('m'),
                'created_at' => date('Y-m-d H:i:s'),
                'status'     => 'success',
            ]);
        }
        print_r($this->db->error()['message']);
        die('Billing generated successfully');
    }

    function check_payment_expiration($client_id)
    {
		$this->db->trans_strict(TRUE);
		$this->db->trans_start();

        // $client_id = 1;

        if (!$result = $this->db->get_where('payment_setting', ['client_id' => $client_id]))
            die('Database Error: '.$this->db->error()['message']);

        if (!$setting = $result->row())
            die('Table Payment Setting is invalid');

        if (!isset($setting->expiration_time_sec))
            die('Field [expiration_time_sec] on Table Payment Setting is invalid');

		$str = '(
            select * from payment
            where client_id = ? and payment_status_id = 1 and payed_at is null and grand_total > 0 and UNIX_TIMESTAMP(created_at) + ? < ?
		) g0';
		$table = $this->f->compile_qry($str, [$client_id, $setting->expiration_time_sec, time()]);
        $this->db->from($table);
        if (!$result = $this->db->get())
            die('Database Error: '.$this->db->error()['message']);

        if(!$payments = $result->result()) {
            print_r(now());
            print_r(time());
            print_r($this->db->last_query());
            die('No payment to be expired');
        }

        foreach ($payments as $r) {

            // Grabing payment detail
            $this->db->select('GROUP_CONCAT(bill_id) as bill_ids');
            if (!$result = $this->db->get_where('payment_dt', ['payment_id' => $r->payment_id]))
                die('Database Error: '.$this->db->error()['message']);

            // Back billing status become => 1 (not paid)
            if ($payment_dt = $result->row()) {
                $str = $this->db->update_string('bill', ['bill_status_id' => 1], "bill_id in ($payment_dt->bill_ids)");
                $this->db->query($str);
            }

            // Recall VIRTUAL ACCOUNT BILLS 
            // Process......

            // Back payment status become => 3 (expired)
            $this->db->update('payment', ['payment_status_id' => 3], ['payment_id' => $r->payment_id]);
        }

        $this->db->trans_complete();
        //Failed
		if ($this->db->trans_status() === FALSE)
		{
            print_r($this->db->error()['message']);
            echo('<br>');
            die('failed');
            // return [FALSE, ['message' => $this->db->last_query()]];
			// return [FALSE, ['message' => $this->db->error()['message']]];
        }
        
        //Success
        print_r($this->db->error()['message']);
        die('successfully');
    }

    function test()
    {
        $this->load->library('f');
        die($this->f->gen_token());
    }
}