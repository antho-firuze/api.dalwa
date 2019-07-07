<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Order_subscription_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database('cloud_simpi');
	}

	/**
	 * entry order subscription dari client of user session: into mobc_order_subscription
	 * flow:
	 * - valid session
	 * - valid porfolio yang disubscribe
	 * - valid bank account tujuan transfer
	 * - portfolio dari bank account sama dengan portfolio yang disubscribe
	 * - Generate TrxID dari UUID
	 * - get TrxDate dari field NextDate afa_mtm dari portfolio yang disubscribe & IsLast = 'Y'
	 * - set TrxStatusID = 1 (NOT COMPLETE)
	 * - set DateCreated, DateModified = NOW
	 * - set PaymentProof = 'N'
	 * - save dalam mobc_order_subscription(simpiID, TrxID, PortfolioID, ClientID, AccountID
	 * - , TrxDate, TrxAmount, TrxStatusID, PaymentProof, DateCreated, DateModified)
	 * - kirim email ke nasabah berisikan terima kasih telah melakukan subscription, dan rincian  
	 * - informasi subscription, pilihan produk, rekening bank tujuan transfer, dan nilai subscription 
	 */
	function new3($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		$this->load->library('simpi');
		list($success, $return) = $this->simpi->check_is_account_active($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) = $this->simpi->check_valid_portfolio3($request);
		if (!$success) return [FALSE, $return];
		
		list($success, $return) = $this->simpi->check_valid_bank3($request);
		if (!$success) return [FALSE, $return];
		
		list($success, $return) = $this->simpi->get_TrxDate($request);
		if (!$success) return [FALSE, $return];
		
		$this->load->helper('myencrypt');
		$request->params->simpiID = $request->simpiID;
		$request->params->AppsID = $request->AppsID;
		$request->params->ClientID = $request->ClientID;
		$request->params->TrxID = UUIDv4();
		$request->params->TrxStatusID = 1;
		$request->params->PaymentProof = 'N';
		$request->params->DateCreated = date('Y-m-d H:i:s');
		$request->params->DateModified = date('Y-m-d H:i:s');

		$datas = ['mobc_order_subscription' => $request->params];
		list($success, $return) = $this->f->batch_insert($datas);
		if (!$success) return [FALSE, $return];

		$this->simpi->get_simpi_info($request);
		$this->simpi->get_user_info($request);
		$this->simpi->get_portfolio_info($request, $request->params->PortfolioID);
		$this->simpi->get_bank_info($request, $request->params->AccountID);
		$this->simpi->get_status_info($request, $request->params->TrxStatusID);
		// return [TRUE, ['message' => $request]];
		$email = [
			'_to' 			=> $request->user_info->email,
			'_subject' 	=> $this->f->lang('email_subject_subscription'),
			'_body'			=> $this->f->lang('email_body_subscription', [
				'name' 			=> $request->user_info->full_name, 
				'PortfolioCode' => $request->portfolio_info->PortfolioCode, 
				'PortfolioNameShort' => $request->portfolio_info->PortfolioNameShort, 
				'Ccy' 					=> $request->portfolio_info->Ccy, 
				'CompanyName' 	=> $request->bank_info->CompanyName, 
				'AccountNo' 		=> $request->bank_info->AccountNo, 
				'TrxAmount' 		=> $request->params->TrxAmount, 
				'PaymentProof' 		=> $request->params->PaymentProof, 
				'StatusCode' 		=> $request->status_info->StatusCode, 
				'simpiName' 		=> $request->simpi_info ? $request->simpi_info->simpiName : '@MI',
				'powered_by' 		=> 'Powered by PT. SIMPIPRO INDONESIA @2018',
			]),
		];
		list($success, $message) = $this->f->mail_queue($email);
		if (!$success) return [FALSE, $message];
		
		return [TRUE, ['message' => $this->f->lang('success_insert')]];
	}

	/**
	 * upload bukti transfer dari order subscription dari client of user session (mobc_order_subscription)
	 * flow:
	 * - valid session
	 * - valid file
	 * - valid format: image atau PDF
	 * - ftp file ke lokasi yang ditentukan
	 * - save: PaymentProof = Y, FileUploadPaymentProof = lokasi & nama file
	 * - set TrxStatusID = 8 (PENDING)
	 */
	function upload3($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (! isset($request->params->TrxID) || empty($request->params->TrxID))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'TrxID')]];

		$upload_url = REPOS_URL.$request->simpiID.SEPARATOR.'subscription'.SEPARATOR;
		$upload_path = REPOS_DIR.$request->simpiID.DIRECTORY_SEPARATOR.'subscription';
		is_dir($upload_path) OR mkdir($upload_path, 0777, true);

		$config['file_name'] 		 = $request->params->TrxID;
		$config['upload_path']   = $upload_path;
		$config['allowed_types'] = 'gif|jpg|png|pdf';
		$config['overwrite']     = true;
		$config['max_size']      = 100;
		// $config['max_width']     = 1024;
		// $config['max_height']    = 768;
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload('userfile')) {
			$error = $this->upload->display_errors();
			return [FALSE, ['message' => $error]];
		} else {
			$data = (object) $this->upload->data();

			$datas = [
				'mobc_order_subscription' => [
					['PaymentProof' => 'Y', 'PaymentFileUpload' => $upload_url.$data->file_name, 'TrxStatusID' => 8, 'DateModified' => date('Y-m-d H:i:s')],
					['TrxID' => $request->params->TrxID]
				],
			];
			$this->f->batch_update($datas);
			
			return [TRUE, ['message' => $this->f->lang('success_upload')]];
		}
	}

	/**
	 * Cancel order subscription, jika status masih NOT COMPLETE atau PENDING
	 * flow:
	 * - valid session
	 * - valid TrxID
	 * - if status = NOT COMPLETE or PENDING, else error Trx tidak dapat dibatalkan karena sudah diproses
	 * - delete records
	 */
	function cancel3($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		$this->load->library('simpi');
		list($success, $return) = $this->simpi->check_allow_delete_status($request, [1, 8]); // 1:NOT COMPLETE, 8:PENDING
		if (!$success) return [FALSE, $return];

		$datas = ['mobc_order_subscription' => (array) $request->params];
		list($success, $return) = $this->f->batch_delete($datas);
		if (!$success) return [FALSE, $return];

		return [TRUE, ['message' => $this->f->lang('success_delete')]];
	}

	/**
	 * list data order subscription dari client of user session bdsk 2 tanggal & status if apply
	 */
	function search3($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (! isset($request->params->DateFrom) || empty($request->params->DateFrom))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'DateFrom')]];

		if (! isset($request->params->DateTo) || empty($request->params->DateTo))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'DateTo')]];

		$str = '(
			SELECT a.simpiID,a.TrxID,c.PortfolioID,c.PortfolioCode,c.PortfolioNameShort,
			d.AssetTypeCode,e.Ccy,h.CompanyCode,h.CompanyName,f.AccountNo,
			a.ReferralCode,a.TrxDate,a.TrxAmount,
			i.StatusID,i.StatusCode,a.PaymentProof,
			a.ResultNet,a.ResultFee,a.NAVPerUnit,a.ResultUnit,a.NAVDate,a.DateCreated  
			FROM mobc_order_subscription AS a 
			INNER JOIN mobc_portfolio AS b ON a.PortfolioID = b.PortfolioID 
			INNER JOIN master_portfolio AS c ON a.PortfolioID = c.PortfolioID 
			INNER JOIN parameter_portfolio_assettype AS d ON c.AssetTypeID = d.AssetTypeID 
			INNER JOIN parameter_securities_ccy AS e ON c.CcyID = e.CcyID 
			INNER JOIN master_portfolio_bankaccount AS f ON a.simpiID = f.simpiID AND a.AccountID = f.AccountID 
			INNER JOIN market_company_office AS g ON f.OfficeID = g.OfficeID 
			INNER JOIN market_company AS h ON g.CompanyID = h.CompanyID 
			INNER JOIN mobc_status i ON a.TrxStatusID = i.StatusID 
			WHERE a.simpiID = ? And a.ClientID = ? And b.AppsID = ? 
			And a.TrxDate >= ? And a.TrxDate <= ?
		) g0';
		$table = $this->f->compile_qry($str, [$request->simpiID, $request->ClientID, $request->AppsID, $request->params->DateFrom, $request->params->DateTo]);
		$this->db->from($table)->order_by('DateCreated DESC');
		return $this->f->get_result($request);
	}


}
