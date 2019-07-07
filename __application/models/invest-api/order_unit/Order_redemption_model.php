<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Order_redemption_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database('cloud_simpi');
	}

	/**
	 * entry order redemption dari client of user session
	 * flow:
	 * - valid session
	 * - valid porfolio yang diredeem
	 * - valid bank penerima 
	 * - if IsAllUnit = No, Amount & Unit tidak kosong dua2nya, dan terisi salah satu
	 * - if IsALlUnit = Yes, Amount & Unit kosong dua2nya
	 * - Generate TrxID dari UUID
	 * - get TrxDate dari afa_mtm dari portfolio yang disubscribe & IsLast = 'Y'
	 * - set TrxStatusID = 8 (PENDING)
	 * - set DateCreated, DateModified = NOW
	 * - save dalam mobc_order_redemption(simpiID, TrxID, PortfolioID, ClientID, , TrxDate
	 * - , IsAllUnit, TrxAmount, TrxUnit, TrxStatusID, BankName, BankCodeType, BankCode, 
	 * - , AccountNo, AccountName, DateCreated, DateModified)
	 * - kirim email ke nasabah berisikan informasi redemption dengan attachment 
	 * - file PDF formulir redemption yang telah terisi data
	 */
	function new3($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
	}

	/**
	 * Cancel order redemption, jika status masih PENDING
	 * flow:
	 * - valid session
	 * - valid TrxID
	 * - if status = PENDING, else error Trx tidak dapat dibatalkan karena sudah diproses
	 * - delete records
	 */
	function cancel3($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
	}

	/**
	 * list data order redemption dari client of user session bdsk 2 tanggal & status if apply
	 */
	function search3($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		// SELECT a.simpiID,a.TrxID,c.PortfolioID,c.PortfolioCode,
		// c.PortfolioNameShort,d.AssetTypeCode,e.Ccy,a.TrxDate,
		// a.TrxAmount,a.IsAllUnit,a.TrxUnit,i.StatusID,i.StatusCode,
		// a.ResultAmount,a.ResultNet,a.ResultFee,a.NAVPerUnit,a.ResultUnit,a.NAVDate,
		// a.BankName,a.BankCodeType,a.BankCode,a.AccountNo,a.AccountName
		// FROM mobc_order_redemption AS a
		// INNER JOIN mobc_portfolio AS b ON a.PortfolioID = b.PortfolioID
		// INNER JOIN master_portfolio AS c ON a.PortfolioID = c.PortfolioID
		// INNER JOIN parameter_portfolio_assettype AS d ON c.AssetTypeID = d.AssetTypeID
		// INNER JOIN parameter_securities_ccy AS e ON c.CcyID = e.CcyID
		// INNER JOIN mobc_status i ON a.TrxStatusID = i.StatusID
		// WHERE a.simpiID = ? And a.ClientID = ? And b.AppsID = ?
		// And a.TrxDate >= ? And a.TrxDate <= ?
	}

}
