<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

/**
 * Mail Service Class
 *
 * This class contains functions for Mail Service Agent
 *
 */
class Mail_service extends CI_Controller 
{
	function __construct(){
		parent::__construct();
		$this->load->database('simpi_gatewaydb');
        $this->load->library('f');
        $this->load->helper('logger');
	}
    
    function logger($message='NONAME')
    {
        if (PHP_OS === 'WINNT')
            logme('scheduler', 'info', "Method [$message]");
    }

    function mail_send()
    {
		$upload_path = FCPATH.'__tmp'.DIRECTORY_SEPARATOR;
        $table = "(
            select queueID, _to, _cc, _bcc, _subject, _body, _attachment, is_test, is_sent, trying, status, CreatedAt, LastTryAt, 
            senderName, senderEmail, protocol, smtp_host, smtp_port, smtp_user, smtp_pass, smtp_timeout, charset, mail_type, smtp_crypto 
            from mail_queue t1 
            left outer join mail_setting_sender t2 on t1.senderID = t2.senderID 
            where is_test = '0' and trying <= 3 and is_sent = '0' and status in ('waiting','failed') 
            order by queueID limit 1
            ) g0 ";
        $this->db->from($table);
		if (! $row = $this->db->get()->row()) {
            $this->logger(__FUNCTION__);
            die('No email queue');
        }
       
        $this->db->update('mail_queue', ['status' => 'queuing'], ['queueID' => $row->queueID]);

        //pathing attachment
        $attachment = NULL;
        if ($row->_attachment) {
            $this->db->from('mail_attachment')->where_in('attachment_id', (array) json_decode($row->_attachment));
            if ($rows = $this->db->get()->result()) {
                foreach ($rows as $key => $val) {
                    $attachment[] = $upload_path.$val->attachment_id.$val->file_ext;
                }
            }
        }
        // exit(print_r($attachment));
        $email = (object)[
            'config'    => (object) [
                            'protocol' => $row->protocol, 
                            'smtp_host' => $row->smtp_host, 
                            'smtp_port' => $row->smtp_port, 
                            'smtp_user' => $row->smtp_user, 
                            'smtp_pass' => $row->smtp_pass, 
                            'smtp_crypto' => $row->smtp_crypto, 
                            'smtp_timeout' => $row->smtp_timeout, 
                            'charset' => $row->charset, 
                            'mailtype' => $row->mail_type, 
                        ],
            'from'      => $row->senderEmail,
            'from_name' => $row->senderName,
            'to'        => $row->_to,
            'cc'        => $row->_cc,
            'bcc'       => $row->_bcc,
            'subject'   => $row->_subject,
            'body'      => $row->_body,
            'attachment' => $attachment,
        ];
        die(var_dump($email));

        $data = [
            'trying' => $row->trying + 1,
            'LastTryAt' => date('Y-m-d H:i:s'),
        ];
        list($success, $return) = $this->f->mail_send($email);
        if (!$success) {
            $data['LastErrorMsg'] = $return['message'];
            $data['is_sent'] = '0';
            $data['status'] = 'failed';
        } else {
            $data['LastErrorMsg'] = NULL;
            $data['is_sent'] = '1';
            $data['status'] = 'success';
        }

        $this->db->update('mail_queue', $data, ['queueID' => $row->queueID]);
        $this->logger(__FUNCTION__);
        die(($success ? 'success' : $return['message']));
    }

    function test()
    {
        die($this->f->get_ip_address());
    }
}