<?php


/**
 * generates PDF file based on session data
 *
 */
class PdfController extends AMA_Controller_Front_Modules
{

	protected $_aclResourceDeny = array();

	protected $_layout = 'pdf';

	protected $_token = null;

	protected $_filenames = array();


	public function preDispatch()
	{
		require_once("DomPdf3/dompdf_config.inc.php");
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->pushAutoloader('DOMPDF_autoload');
		$this->_token = md5(isset($_SERVER['HTTP_COOKIE']) ? $_SERVER['HTTP_COOKIE'] : $_SERVER['REMOTE_ADDR']);
        $this->_token = dechex(crc32(md5($this->_token . mt_rand(0, 8000))));
		date_default_timezone_set('UTC');
	}


	public function pdfAction()
	{

	}

	/**
	 * admin only action
	 */
	public function exportAction()
	{
		$transactionId = intval($this->_request->getParam('id', 0));
		if (!$transactionId) {
			$this->_redirect('payment/invalid-order');
		}
		$transactionArchive = new AMA_Model_DbTable_TransactionsArchive();
		$storedTransaction = $transactionArchive->find($transactionId)->current()->toArray();
		/*@Author: Zaptech
        @Description: Loading Dynamic Value from back end site settings tab and passing to view*/
        $siteSettings = new AMA_Model_DbTable_SiteConfigData();
        $settings = $siteSettings->getValue('copyright_information', $this->_language);
        $invoice->settings = $settings;
		$this->view->invoice = $this->getOrder($storedTransaction,$settings);
		$subscriptionInstance = $transactionArchive->find($transactionId)->current();
		$this->view->printedDate = $subscriptionInstance->is_printed ? $subscriptionInstance->printed_date : null;
	}


	public function invoiceAction()
	{
		$uid = AMA_Utils_Login::getInstance()->user_id ? AMA_Utils_Login::getInstance()->user_id : $this->_request->getParam('uid', null);
		$transactionId = intval($this->_request->getParam('id', 0));
		$order = new AMA_Service_Order($uid, $transactionId);
		$subscription = new AMA_Model_DbTable_SubscriptionTransaction();
		if (!$order->checkAccess() || !$order->isPaid() || $subscription->isPrinted($transactionId)) {
//			$this->_redirect('payment/invalid-order');
		}
        //single entry point
        $transactionArchive = new AMA_Model_DbTable_TransactionsArchive();
        $storedTransaction = $transactionArchive->getLoggedByTransactionId($transactionId )->current()->toArray();
        $this->view->invoice = $this->getOrder($storedTransaction);
//		$this->view->invoice = $order->getInvoice();
		$config = Zend_Registry::get('config');
		$paymentPath = $config->payment->paymentPage;
		$query = http_build_query(array(
									   'merchantId' => $config->payment->merchantId,
									   'currency' => $this->view->invoice->item->currency,
									   'amount' => $this->view->invoice->item->amountGross * 100,
									   'refno' => $this->view->invoice->item->id,
                                       'theme' => $config->payment->theme,
								  ));
		$paymentPath .= '?' . $query;
		$this->view->paymentLink = $paymentPath;
		$this->view->pdfLink = $this->_basePath . '/pdf/invoice/id/' . $this->view->invoice->item->id;
		$subscription->setInvoiceIsPrinted($transactionId);

		$transactionsArchiveModel = new AMA_Model_DbTable_TransactionsArchive();
		$transactionsArchiveModel->setInvoiceIsPrinted($transactionId);
	}


	public function paintingPricingAction()
	{
		$dataSession = new Zend_Session_Namespace('chartData');
		$data = $dataSession->array;
		if (!is_array($data)) {
			throw new Exception('no data in session');
		}
		$this->view->data = $data;
		$this->view->noPicture = 'no-picture.png';
	}


	public function categoryPerformanceAction()
	{
		$dataSession = new Zend_Session_Namespace('chartData');
		$data = $dataSession->array;
		if (!is_array($data)) {
			throw new Exception('no data in session');
		}
		$this->view->data = $data;
		$this->view->action('indices', 'chart', null, array('save' => $this->_token,));
		$this->view->action('annual', 'chart', null, array('save' => $this->_token,));
		$this->view->action('risk', 'chart', null, array('save' => $this->_token,));
		$this->_filenames = array('Indices', 'Annual Return', 'Risk and Returns');
	}


	public function artistPerformanceAction()
	{
		$dataSession = new Zend_Session_Namespace('chartData');
		$data = $dataSession->array;
		if (!is_array($data)) {
			throw new Exception('no data in session');
		}
		$this->view->data = $data;
		$this->view->action('indices', 'chart', null, array('save' => $this->_token,));
		$this->view->action('annual', 'chart', null, array('save' => $this->_token,));
		$this->view->action('risk', 'chart', null, array('save' => $this->_token,));
		$this->_filenames = array('Indices', 'Annual Return', 'Risk and Returns');
	}


	public function postDispatch()
	{
		global $_dompdf_warnings;
		parent::postDispatch();
		$this->view->userToken = $this->_token;
		$convmap = array(
            0x80, 0x10ffff, 0, 0xffffff,
			0x80, 0xff, 0, 0xff, // extended ascii (latin)
			0x0400, 0x0513, 0, 0xffff, // cyrillic
			0x1d2b, 0x1d2b, 0, 0xffff, // small capital el
			0x1d78, 0x1d78, 0, 0xffff // modifier en
		);
		$this->view->pageTranslate = mb_encode_numericentity($this->_translate->getAdapter()->translate('Page'), $convmap, 'UTF-8');
		$this->view->ofTranslate = mb_encode_numericentity($this->_translate->getAdapter()->translate('of'), $convmap, 'UTF-8');
		$this->view->legalTitle = mb_encode_numericentity($this->_translate->getAdapter()->translate('Legal note'), $convmap, 'UTF-8');
		$this->view->legalLine1 = mb_encode_numericentity($this->_translate->getAdapter()->translate('AFP has dedicated great care in the calculations of these price levels &9 indices. Nevertheless, AFP is neither'), $convmap, 'UTF-8');
		$this->view->legalLine2 = mb_encode_numericentity($this->_translate->getAdapter()->translate('responsible for any lack in exhaustivity or accuracy, nor for any uses made of the information supplied.'), $convmap, 'UTF-8');
		/*
        @Author: Zaptech
        @Description: Loading Dynamic Value from back end site settings tab and passing to view*/
        $siteSettings = new AMA_Model_DbTable_SiteConfigData();
        $settings = $siteSettings->getValue('copyright_information', $this->_language);
        $settings = str_replace('{base_url}', $this->basePath, $settings);
	/* @Author: Zaptech
           @Date: 05/03/2018
           @Descriptiption: Removing/Stripping tags while generating PDF
        */	
	$settings = strip_tags($settings);
        //$settings = str_replace('<a href="/content/terms">Terms &amp; Conditions</a>', "", $settings);
        //$settings = str_replace('/ <a href="/content/privacy_policy">Privacy Policy &amp; Legal Disclaimer</a>', "", $settings);
        //$settings =  str_replace('&copy;','© 2010 -', $settings);

		//$this->view->title = $settings;
		$this->view->title = $settings;
		//ends
		
        $this->view->isChinese = (bool) ($this->_languageId == 5);
        $this->view->language = AMA_Service_Language::getLanguage();
		$name = $this->viewBuilder();
		$this->render();
		$html = $this->getResponse()->getBody();
		$dompdf = new DOMPDF();
		$dompdf->set_paper("a4", "portrait");
		$dompdf->load_html($html);
		$dompdf->render();

		if (false) {
			$dompdf->stream("module_out.pdf", array("Attachment" => 0));
		}
		else
		{
			$dompdf->stream($name . '.pdf', array("compress" => 1));
		}
		$this->imagesCleanup();
		exit(0);
	}


	protected function imagesCleanup()
	{
		$path = APPLICATION_PATH . '\..\public\uploads\tmp\\';
		foreach ($this->_filenames as $name)
		{
			$filename = $name . $this->_token . '.png';
			@unlink($path . $filename);
		}
	}


	protected function viewBuilder()
	{
		$params = $this->_request->getParams();
		$outputType = isset($params['part']) ? $params['part'] : '';
        $actionName = $this->getRequest()->getActionName();
        if($actionName == 'invoice'){
            $actionName = 'export';
        }
		$view = $actionName . '-result' . $outputType;
		$this->_helper->viewRenderer($view, null, true);
		return $view;
	}

	protected function getOrder($storedTransaction)
	{
		$invoice = new stdClass();
		//not the best idea, order should be rewritten
		$invoice->vat = $storedTransaction['vat'] != 0 ? 0.077 : 0;
		
		//model user
		$invoice->user = (object) $storedTransaction;
		$invoice->item = new stdClass();
		$invoice->item->id = $storedTransaction['transaction_id'];
		if( $storedTransaction['transaction_type'] == AMA_Model_DbTable_SubscriptionTransaction::REQUESTS)
		{
            $requestType = isset($storedTransaction['request_service_type']) ? $storedTransaction['request_service_type'] : 1;
		    $invoice->item->name = AMA_Service_Order::$types[$requestType];
		}
		else
		{
		  $invoice->item->name = $storedTransaction['subscription_name'] . ' ' . $storedTransaction['subscription_term'] . ' month';
		}
//        $amount = rtrim(sprintf('%.2f', ($transaction->amount / 100) ), "0") + 0;
		$invoice->item->currency = $storedTransaction['currency'];
  		$invoice->item->transactionId = $storedTransaction['transaction_id'];
		$invoice->item->quantity = $storedTransaction['quantity'];
		$invoice->item->amountNet = sprintf("%.2f", $storedTransaction['amount_net']);
        $invoice->item->amountGross = sprintf("%.2f", $storedTransaction['amount_gross']);

		$invoice->item->vat = sprintf("%.2f", $storedTransaction['vat']);
//		$invoice->item->amountGross = rtrim(sprintf('%.2f', ($storedTransaction['amount'] / 100) ), "0") + 0;
        $invoice->item->issuedDate = $storedTransaction['transaction_date'];
		return $invoice;
	}
}
