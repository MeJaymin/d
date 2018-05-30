<?php
error_reporting(0);
class Gift extends CI_Controller 
{

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Logs_model');
        $this->load->model('Gift_model');
        $this->load->model('Transactions_model');
        $this->load->model('Notification_model');
        $this->load->model('Payment_model');
        date_default_timezone_set('Asia/Kolkata');
    }

    public function dwollaConfig(){
        require('vendor/autoload.php');
        $access_token = $this->getAccessToken();
        DwollaSwagger\Configuration::$access_token = $access_token;
        // DwollaSwagger\Configuration::$debug = 1;
        $apiClient = new DwollaSwagger\ApiClient(DWOLLA_API_URL);
        return $customersApi = new DwollaSwagger\CustomersApi($apiClient);
    }

    /**
 * @api {post} api/ws_dwolla_verified_sender Dwolla Verified Sender
 * @apiVersion 1.0.0
 * @apiName VerifySender
 * @apiGroup Dwolla Bank Account
 *
 * @apiDescription Verify sender in Dwolla.
 *
 * @apiParam {Character} email_id The Email Id.
 */

    public function verifiedSender(){
          $this->form_validation->set_rules('email_id', 'Email Id', 'trim|required');
          if ($this->form_validation->run() === FALSE) 
            {
                $response['code'] = 0;
                $response['status'] = "error";
                $response['message'] = 'Please enter all fields';
                echo json_encode($response);
            } 
            else 
            {
                $emailId = $this->input->post('email_id');
                $response = $this->generateIAVToken($emailId);
                echo $response; die;
            }
    }

    /**
 * @api {post} api/ws_dwolla_verified_reciever Dwolla Verified Reciever
 * @apiVersion 1.0.0
 * @apiName VerifyReciever
 * @apiGroup Dwolla Bank Account
 *
 * @apiDescription Verify reciever in Dwolla
 *
 * @apiParam {Character} email_id The Email Id.
 */

    public function verifiedReciever(){
          $this->form_validation->set_rules('email_id', 'Email Id', 'trim|required');
          if ($this->form_validation->run() === FALSE) 
            {
                $response['code'] = 0;
                $response['status'] = "error";
                $response['message'] = 'Please enter all fields';
                echo json_encode($response);
            } 
            else 
            {
                $emailId = $this->input->post('email_id');
                $response = $this->generateIAVToken($emailId);
                echo $response; die;

            }
        
    }
    public function generateIAVToken($emailId)
    {       
        $dbEmails = array();
        if($emailId != '')
        {
            $dbEmails[] = $emailId;    
        }
        $customersApi = $this->dwollaConfig();
        //$customers = $customersApi->_list();
        $customers = $customersApi->_list();
        $total_dwolla_customers_count = $customers->total;
        $customers = $customersApi->_list($total_dwolla_customers_count,'0');
        /*pr($customers);
        die;*/
        $r = 0;
        $custData = $customers->_embedded->customers;
        $custmer_email = array();
        foreach ($custData as $key => $value) 
        {
            $custmer_email[] = $value->email;
            $custmer_bId[] = $value->id;
            $r++;
        }

        $result = array_diff($dbEmails, $custmer_email);
        // print_r($result); die;
        if(!empty($result))
        {
            foreach ($result as $key => $value) 
            {
                $this->db->select('fname,lname');
                $this->db->from('users');
                $this->db->where('email_id',$emailId);
                $res  = $this->db->get()->row_array();
                // print_r($res); die;
                $new_customer = $customersApi->create([
                  'firstName' => $res['fname'],
                  'lastName' => $res['lname'],
                  'email' =>  $emailId,
                  'type' => 'personal',
                  'address1' => '99-99 33rd St',
                  'city' => 'Some City',
                  'state' => 'NY',
                  'postalCode' => '11101',
                  'dateOfBirth' => '1970-01-01',
                  'ssn' => '1234'
                ]);
            }
        }
        $customersApi = $this->dwollaConfig();
        $getFundingSource = $customersApi->_list('','',$emailId);
        $FundId = $getFundingSource->_embedded->customers[0]->id;
        $iavToken = $customersApi->getCustomerIavToken(DWOLLA_CUSTOMER_URL.$FundId);

        $response['code'] = 1;
        $response['status'] = "success";
        $response['iavToken'] = $iavToken->token;
        $response['FundId'] = $FundId;
        $dwollresponse = json_encode($response);
        return $dwollresponse;
    }
    /**
 * @api {post} api/ws_send_gift Send Gift
 * @apiVersion 1.0.0
 * @apiName SendGift
 * @apiGroup Gifts
 *
 * @apiDescription Will send a gift from one user to another.
 *
 * @apiParam {Number} title The Users-ID.
 * @apiParam {Number} from_id The From id(From).
 * @apiParam {Number} recipient_id The Sender id(To).
 * @apiParam {Character} recipient_name The Recipient Name.
 * @apiParam {Number} amount Amount.
 * @apiParam {Character} payment_type Payment Type.
 * @apiParam {Character} transaction_id Transaction id.
 * @apiParam {Character} transaction_details Transaction details.
 * @apiParam {DateTime} gift_timestamp Timestamp.
 */
    public function send_gift()
    {
        $this->form_validation->set_rules('title', 'Title', 'trim|required');
        $this->form_validation->set_rules('from_id', 'From Id', 'trim|required');
        $this->form_validation->set_rules('recipient_id', 'Recipient Id', 'trim|required');
        //$this->form_validation->set_rules('from_name', 'From Name', 'trim|required');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('tax', 'Amount', 'trim|required');
        $this->form_validation->set_rules('final_amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('video', 'Video', 'trim|required');
        $this->form_validation->set_rules('payment_type', 'Payment Type', 'trim');
        $this->form_validation->set_rules('gift_timestamp', 'Gift timestamp', 'trim|required');
        /* Check Validation For Field Require */
        if ($this->form_validation->run() === FALSE) 
        {
            $response['code'] = 0;
            $response['status'] = "error";
            $response['message'] = 'Please enter all fields';
            echo json_encode($response);
        } 
        else 
        {
            $recipient_id = $this->input->post('recipient_id');
            $from_id = $this->input->post('from_id');
            $where = "(email_id= '$recipient_id' OR  phone_number = '$recipient_id')"; //// check email address or mobile no. exist
            $result = $this->User_model->getAnyData($where);
            $postVar = $this->input->post();
            if(!empty($result))
            {
               
                $sendGift = $this->gift_send_func($postVar,$result[0]->id);
            }
            else
            {
                $where = "(id= '$from_id')";
                $result = $this->User_model->getAnyData($where);
                $fullname = $result[0]->fname." ".$result[0]->lname;

                if(filter_var($recipient_id , FILTER_VALIDATE_EMAIL)) 
                {
                    
                    $emaildata['to'] = $recipient_id;
                    $emaildata['subject'] = 'GiftCast - Invitation';
                    $emaildata['message'] = file_get_contents(base_url().'email_templates/email-signup-unregistered.php');
                    $emaildata['message'] = str_replace('UNREGISTEREDNAME', $fullname, $emaildata['message']);
                    /*$emaildata['message'] = "Hello, <br/>Your friend ".$fullname." has send you the GiftCast. Signup to withdraw the amount.
                    <br/><br/> Thanks,<br/><b>GiftCast Team</b>.";*/
                    sendmail($emaildata);        
                }

                if(filter_var($recipient_id , FILTER_VALIDATE_EMAIL)) 
                {
                    $signup_data['email_id'] = $recipient_id ? $recipient_id : '';
                }
                else
                {
                    $signup_data['phone_number'] = $recipient_id ? $recipient_id : '';
                }
               
                /* 
                Insert Contact Name as First Name so that in email reciept, name is valid.
                Although it gets overwrite once he sign up
                */
                
                $signup_data['fname'] = $postVar['phonebook_name'];
                $signup_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
                $signup_data['status'] = "1";
                $signup_data['created_at'] = date("Y-m-d H:i:s");
                //print_r($signup_data); die;
                $Insert = $this->User_model->Insert_Data($signup_data);
                if(!empty($Insert))
                {
                    $sendGift = $this->gift_send_func($postVar,$Insert);    
                }
                else
                {
                    $response['code'] = 0;
                    $response['status'] = "error";
                    $response['message'] = 'Error while sending gift';
                    echo json_encode($response);    
                }
            }
        }
    }

    /*This will work when gift send later api works*/
    public function gift_send_later()
    {
        $this->form_validation->set_rules('title', 'Title', 'trim|required');
        $this->form_validation->set_rules('from_id', 'From Id', 'trim|required');
        $this->form_validation->set_rules('recipient_id', 'Recipient Id', 'trim|required');
        //$this->form_validation->set_rules('from_name', 'From Name', 'trim|required');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('video', 'Video', 'trim|required');
        $this->form_validation->set_rules('payment_type', 'Payment Type', 'trim');
        $this->form_validation->set_rules('fund_id', 'Transaction details', 'trim|required');
        $this->form_validation->set_rules('gift_timestamp', 'Gift timestamp', 'trim|required');
        /* Check Validation For Field Require */
        if ($this->form_validation->run() === FALSE) 
        {
            $response['code'] = 0;
            $response['status'] = "error";
            $response['message'] = 'Please enter all fields';
            echo json_encode($response);
        } 
        else 
        {

            $recipient_id = $this->input->post('recipient_id');
            $from_id = $this->input->post('from_id');
            $where = "(email_id= '$recipient_id' OR  phone_number = '$recipient_id')"; //// check email address or mobile no. exist
            $result = $this->User_model->getAnyData($where);
            $postVar = $this->input->post();
            if(!empty($result))
            {
               
                $sendGift = $this->gift_send_later_func($postVar,$result[0]->id);
                
            }
            else
            {
                $where = "(id= '$from_id')";
                $result = $this->User_model->getAnyData($where);
                $fullname = $result[0]->fname." ".$result[0]->lname;


            
                if(filter_var($recipient_id , FILTER_VALIDATE_EMAIL)) 
                {
                    $emaildata['to'] = $recipient_id;
                    $emaildata['subject'] = 'GiftCast - Invitation';
                    $emaildata['message'] = file_get_contents(base_url().'email_templates/email-signup-unregistered.php');
                    $emaildata['message'] = str_replace('UNREGISTEREDNAME', $fullname, $emaildata['message']);
                    /*$emaildata['message'] = "Hello, <br/>Your friend ".$fullname." has send you the GiftCast. Signup to withdraw the amount.
                    <br/><br/> Thanks,<br/><b>GiftCast Team</b>.";*/
                    sendmail($emaildata);        
                }
                
                if(filter_var($recipient_id , FILTER_VALIDATE_EMAIL)) 
                {
                    $signup_data['email_id'] = $recipient_id ? $recipient_id : '';
                }
                else
                {
                    $signup_data['phone_number'] = $recipient_id ? $recipient_id : '';
                }
               
                $signup_data['fname'] = $postVar['phonebook_name'];
                $signup_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
                $signup_data['status'] = "1";
                $signup_data['created_at'] = date("Y-m-d H:i:s");
                // print_r($signup_data); die;
                $Insert = $this->User_model->Insert_Data($signup_data);
                if(!empty($Insert))
                {
                    $sendGift = $this->gift_send_later_func($postVar,$Insert);    
                }
                else
                {
                    $response['code'] = 0;
                    $response['status'] = "error";
                    $response['message'] = 'Error while sending gift';
                    echo json_encode($response);    
                }
            }
        }
    }

/**
 * @api {post} api/ws_gift_history Gift History Sender
 * @apiVersion 1.0.0
 * @apiName GiftHistory
 * @apiGroup Gifts
 *
 * @apiDescription Sender can see Gift History.
 *
 * @apiParam {Number} id The Gift-ID.
 */
    public function gift_history($giftid = '')
    {
        $this->form_validation->set_rules('id', 'Id', 'trim');

        if ($this->form_validation->run() === FALSE) 
        {
            $response['code'] = 0;
            $response['status'] = "error";
            $response['message'] = 'Please enter all fields';
            echo json_encode($response);
        } 
        else 
        {
            $id = $this->input->post('id');
            if($giftid != ''){
                $where = array('gifts.id' => $giftid,'gifts.from_delete' => '0');    
            }
            else{
                $where = array('gifts.from_id' => $id,'gifts.from_delete' => '0');    
            }
            //$select ="gifts.*,users.email_id,users.profile_picture,transactions.*";
            $select = "gifts.*, u1.email_id as reciever_email,u1.fname as reciever_fname,u1.lname as reciever_lname, u2.email_id as sender_email,u2.fname as sender_fname,u2.lname as sender_lname, u2.profile_picture as sender_profile_picture";
            $join_arr[0] = array( 
                                "table_name" => "users as u1", 
                                "cond" => "gifts.recipient_id=u1.id", 
                                "type" => "inner" 
                                );
            
            $join_arr[1] = array( 
                                "table_name" => "users as u2", 
                                "cond" => "gifts.from_id=u2.id", 
                                "type" => "inner" 
                                );
            $order_by = "gifts.created_at DESC";
            $result = $this->Gift_model->getAnyData($where,$select,$order_by,"",$join_arr);
            // echo $this->db->last_query(); die;
            // print_r($result); die;
           
            if(!empty($result))
            {
                foreach($result as $key => $r)
                {
                    $where1 = array('users.id' => $r->recipient_id);
                    $select1 = "users.profile_picture";
                    $getProfilePic = $this->User_model->getAnyData($where1,$select1,"","","");   
                    // print_r($getProfilePic); die;
                    if(!empty($getProfilePic[0]->profile_picture) && $getProfilePic[0]->profile_picture!="")
                    {
                        $gift_data[$key]['sender_profile_picture'] = ASSETS_URL.'profile_pictures/'.$getProfilePic[0]->profile_picture;    
                    }
                    else
                    {
                        $gift_data[$key]['sender_profile_picture'] = "";
                    }
                    $gift_data[$key]['gift_id'] = $r->id;
                    $gift_data[$key]['title'] = $r->title;
                    $gift_data[$key]['from_id'] = $r->from_id;
                    $gift_data[$key]['from_email_id'] = $r->sender_email;
                    if(!empty($r->sender_lname))
                    {
                        $gift_data[$key]['from_name'] = $r->sender_fname.' '.$r->sender_lname;    
                    }
                    else
                    {
                        $gift_data[$key]['from_name'] = $r->sender_fname;   
                    }

                    $gift_data[$key]['recipient_id'] = $r->reciever_email; //id as email_id
                    $gift_data[$key]['gift_status'] = $r->gift_status;
                    //$gift_data[$key]['sender_profile_picture'] = ASSETS_URL.'profile_pictures/'.$r->sender_profile_picture;
                    if(!empty($r->reciever_lname))
                    {
                        $gift_data[$key]['phonebook_name'] = $r->reciever_fname.' '.$r->reciever_lname;//reciever fname and lname    
                    }
                    else
                    {
                        $gift_data[$key]['phonebook_name'] = $r->reciever_fname;//reciever fname and lname
                    }
                    $gift_data[$key]['amount'] = $r->amount;
                    $gift_data[$key]['video'] = ASSETS_URL.'user_videos/'.$r->video;
                    $gift_data[$key]['screenshot'] = ASSETS_URL.'user_videos/'.$r->screenshot;
                    $gift_data[$key]['payment_type'] = $r->payment_type;
                    $gift_data[$key]['created_at'] = $r->created_at;
                    
                }
                    $response['code'] = 0;
                    $response['status'] = "success";
                    $response['data'] = $gift_data;
                    $response['message'] = 'gifts data fetched successfully';
                    
                    if($giftid != ''){
                        return $gift_data;
                    }
                    else{
                        echo json_encode($response);
                    }
               
            }
            else
            {
                $response['code'] = 0;
                $response['status'] = "error";
                $response['message'] = 'no gifts found';
                echo json_encode($response);
            }
        }
    }

    /**
 * @api {post} api/ws_gift_history_delete Gift Delete
 * @apiVersion 1.0.0
 * @apiName GiftHistoryDelete
 * @apiGroup Gifts
 *
 * @apiDescription Gift History Delete from Sender/Reciever Side
 *
 * @apiParam {Number} id The Gift-ID.
 * @apiParam {Number} remove_id The User ID.
 * @apiParam {Number} type Type (E.g Sender,Reciever).
 */

    public function gift_delete()
    {
        $this->form_validation->set_rules('id', 'Id', 'trim|required');
        $this->form_validation->set_rules('remove_id', 'Remove id', 'trim|required');
        $this->form_validation->set_rules('type', 'Type', 'trim|required'); /// type = sender / receiver
        if ($this->form_validation->run() === FALSE) 
        {
            $response['code'] = 0;
            $response['status'] = "error";
            $response['message'] = 'Please enter all fields';
            echo json_encode($response);
        } 
        else 
        {
            $gift_id = $this->input->post('id');
            $remove_id = $this->input->post('remove_id');
            $type = $this->input->post('type');
            /* Old Code
            $data = array(
                    'id' => $id
                    );
            $delete = $this->Gift_model->delete($data);*/
            if($type == 'sender'){
                $set = array(
                    'from_delete' => '1'
                    );
                $where['from_id'] = $remove_id;    
            }
            else if($type == 'receiver'){
                $set = array(
                    'to_delete' => '1'
                    );
                $where['recipient_id'] = $remove_id;    
            }

            $where['id'] = $gift_id;
            $update = $this->Gift_model->update($set, $where);
            if($update)
            {
                $response['code'] = 1;
                $response['status'] = "success";
                $response['message'] = 'gift history has been deleted successfully';
                echo json_encode($response);
            }
            else
            {
                $response['code'] = 0;
                $response['status'] = "error";
                $response['message'] = 'recipient does not exists';
                echo json_encode($response);
            }
        }
    }

    // public function gift_delete()
    // {
    //     $this->form_validation->set_rules('id', 'Id', 'trim|required');
    //     $this->form_validation->set_rules('from_id', 'from id', 'trim|required');
    //     if ($this->form_validation->run() === FALSE) 
    //     {
    //         $response['code'] = 0;
    //         $response['status'] = "error";
    //         $response['message'] = 'Please enter all fields';
    //         echo json_encode($response);
    //     } 
    //     else 
    //     {
    //         $gift_id = $this->input->post('id');
    //         $from_id = $this->input->post('from_id');

    //         /* Old Code
    //         $data = array(
    //                 'id' => $id
    //                 );
    //         $delete = $this->Gift_model->delete($data);*/
    //         $set = array(
    //                 'from_delete' => '1'
    //                 );
    //         $where['from_id'] = $from_id;
    //         $where['id'] = $gift_id;
    //         $update = $this->Gift_model->update($set, $where);
    //         if($update)
    //         {
    //             $response['code'] = 1;
    //             $response['status'] = "success";
    //             $response['message'] = 'gift history has been deleted successfully';
    //             echo json_encode($response);
    //         }
    //         else
    //         {
    //             $response['code'] = 0;
    //             $response['status'] = "error";
    //             $response['message'] = 'recipient does not exists';
    //             echo json_encode($response);
    //         }
    //     }
    // }

    /**
 * @api {post} api/ws_gift_history_recieved Gift History Reciever
 * @apiVersion 1.0.0
 * @apiName GiftHistoryReciever
 * @apiGroup Gifts
 *
 * @apiDescription Reciever can see Gift History
 *
 * @apiParam {Number} id The Gift-ID.
 */

    public function gift_recieved()
    {
        $this->form_validation->set_rules('id', 'Id', 'trim|required');
        if ($this->form_validation->run() === FALSE) 
        {
            $response['code'] = 0;
            $response['status'] = "error";
            $response['message'] = 'Please enter all fields';
            echo json_encode($response);
        } 
        else
        {
            $recipient_id = $this->input->post('id');
            // $where = array('gifts.recipient_id' => $recipient_id);
            $where = array('gifts.recipient_id' => $recipient_id,'gifts.to_delete' => '0');
            //$select ="gifts.*,users.email_id,users.profile_picture,transactions.*";
            $select = "gifts.*, u1.fname as reciever_fname,u1.lname as reciever_lname,u1.email_id as reciever_email, u2.email_id as sender_email, u2.fname as sender_first_name, u2.lname as sender_last_name, u2.profile_picture as sender_profile_picture";
            $join_arr[0] = array( 
                                "table_name" => "users as u1", 
                                "cond" => "gifts.recipient_id=u1.id", 
                                "type" => "inner" 
                                );
            $join_arr[1] = array( 
                                "table_name" => "users as u2", 
                                "cond" => "gifts.from_id=u2.id", 
                                "type" => "inner" 
                                );
            /*$join_arr[2] = array( 
                                "table_name" => "transactions", 
                                "cond" => "gifts.id=transactions.gift_id", 
                                "type" => "inner" 
                                );*/
            
            $order_by = "gifts.created_at DESC";
            $result = $this->Gift_model->getAnyData($where,$select,$order_by,"",$join_arr);
           /* echo $this->db->last_query();
            print_r($result[0]);
            die();*/
            
            //pr($result); die;
            if(!empty($result))
            {
                
                foreach($result as $key => $r)
                {
                    // print_r($r);
                    $where1 = array('users.id' => $r->from_id);
                    $select1 = "users.profile_picture";
                    $getProfilePic = $this->User_model->getAnyData($where1,$select1,"","","");      

                    $gift_data[$key]['gift_id'] = $r->id;
                    $gift_data[$key]['title'] = $r->title;
                    $gift_data[$key]['from_id'] = $r->from_id;
                    $gift_data[$key]['from_email_id'] = $r->sender_email;
                    if(!empty($r->sender_last_name))
                    {
                        $gift_data[$key]['from_name'] = $r->sender_first_name.' '.$r->sender_last_name;    
                    }
                    else
                    {
                        $gift_data[$key]['from_name'] = $r->sender_first_name;   
                    }
                    if(!empty($getProfilePic[0]->profile_picture) && $getProfilePic[0]->profile_picture!="")
                    {
                        $gift_data[$key]['sender_profile_picture'] = ASSETS_URL.'profile_pictures/'.$getProfilePic[0]->profile_picture;    
                    }
                    else
                    {
                        $gift_data[$key]['sender_profile_picture'] = "";
                    }
                    $gift_data[$key]['recipient_id'] = $r->reciever_email;
                    
                    // $gift_data[$key]['sender_profile_picture'] = ASSETS_URL.'profile_pictures/'.$r->profile_picture;
                    if(!empty($r->reciever_lname))
                    {
                        $gift_data[$key]['phonebook_name'] = $r->reciever_fname.' '.$r->reciever_lname; //reciever fname and lname   
                    }
                    else
                    {
                        $gift_data[$key]['phonebook_name'] = $r->reciever_fname;//reciever fname and lname
                    }
                    $gift_data[$key]['gift_status'] = $r->gift_status;
                    $gift_data[$key]['recipient_gift_status_opened'] = $r->recipient_gift_status;//check whether recipient has opened or not gift
                    $gift_data[$key]['withdraw_status'] = $r->withdraw_status;
                    $gift_data[$key]['amount'] = $r->amount;
                    $gift_data[$key]['video'] = ASSETS_URL.'user_videos/'.$r->video;
                    $gift_data[$key]['screenshot'] = ASSETS_URL.'user_videos/'.$r->screenshot;
                    $gift_data[$key]['payment_type'] = $r->payment_type;
                    $gift_data[$key]['created_at'] = $r->created_at;
                    /*$gift_data[$key]['transaction_id'] = $r->transaction_id;
                    $gift_data[$key]['transaction_details'] = unserialize($r->transaction_details); //unserialize*/
                }
                $response['code'] = 1;
                $response['status'] = "success";
                $response['data'] = $gift_data;
                $response['message'] = 'gifts data fetched successfully';
                echo json_encode($response);
            }
            else
            {
                $response['code'] = 0;
                $response['status'] = "error";
                $response['message'] = 'no gifts exists';
                echo json_encode($response);
            }
        }
    }
    
    public function getAccessToken(){
            $client_id = $this->config->item('client_id');
            $client_secret = $this->config->item('client_secret');
            $url =  $this->config->item('dwolla_url');
            $data = array('client_id' => $client_id, 'client_secret' => $client_secret, 'grant_type' => 'client_credentials');
            $data_string = json_encode($data);             
            $handle = curl_init($url);
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);                                                                      
            $result = curl_exec($handle);
            curl_close($handle);
            $datas = json_decode($result, TRUE);
            $access_token = $datas['access_token'];
            return $access_token;
    }
    public function withDrawFund(){
        $this->form_validation->set_rules('fund_id', 'Fund Id', 'trim|required');
        $this->form_validation->set_rules('gift_id', 'Gift Id', 'trim|required');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('type', 'Type', 'trim|required'); //type = dwolla / customer
        $this->form_validation->set_rules('unique_bank_id', 'unique_bank_id', 'trim|required');
        if ($this->form_validation->run() === FALSE) 
        {
            $response['code'] = 0;
            $response['status'] = "error";
            $response['message'] = 'Please enter all fields';
            echo json_encode($response);
        } 
        else
        {
             $postVar = $this->input->post();
             $fundId = $postVar['fund_id'];
             $amount = $postVar['amount'];
             $type = $postVar['type'];
             $gift_id = $postVar['gift_id'];
             $unique_bank_id = $postVar['unique_bank_id'];// unique_bank_id
             $customerUrl = DWOLLA_CUSTOMER_URL.$fundId;
             $access_token = $this->getAccessToken();
             require('vendor/autoload.php');
             DwollaSwagger\Configuration::$access_token = $access_token;

            //DwollaSwagger\Configuration::$debug = 1;
            $apiClient = new DwollaSwagger\ApiClient(DWOLLA_API_URL);
            //echo DWOLLA_API_URL;
            $fsApi = new DwollaSwagger\FundingsourcesApi($apiClient);

            $fundingSources = $fsApi->getCustomerFundingSources($customerUrl,'');
            foreach ($fundingSources->_embedded->{'funding-sources'} as $key => $value) {
              if($value->type == 'bank' && $value->status == 'verified')
              {
                if($value->id == $unique_bank_id)
                {
                    $fundId = $value->id;
                    //echo '<br>';
                    $bank_name = $value->bankName;
                    //echo '<br>';
                }
              }
            }
            //pr($fundingSources); die;
            $dwollaId = $this->config->item('dwolla_merchant_id');
            $tranfer_fee = $this->config->item('dwolla_transfer_fee');
            if($type == 'dwolla'){
                $transfer_request = array (
                  '_links' => 
                  array (
                    'source' => 
                    array (
                      'href' => DWOLLA_FUND_URL.$fundId, //Joel@inlooh
                    ),
                    'destination' => 
                    array (
                      'href' => DWOLLA_FUND_URL.$dwollaId,
                    ),
                  ),
                  'amount' => 
                  array (
                    'currency' => 'USD',
                    'value' => $amount,
                  )
                );
            }
            else if($type == 'customer'){
                $transfer_request = array (
                  '_links' => 
                  array (
                    'source' => 
                    array (
                      'href' => DWOLLA_FUND_URL.$dwollaId, //Joel@inlooh
                    ),
                    'destination' => 
                    array (
                      'href' => DWOLLA_FUND_URL.$fundId,
                    ),
                  ),
                  'amount' => 
                  array (
                    'currency' => 'USD',
                    'value' => $amount,
                  )
                );
            }
            else{
                 $response['code'] = 0;
                 $response['status'] = "error";
                 $response['message'] = 'Something went wrong. please try again.';
                 echo json_encode($response);
            }
            $transferApi = new DwollaSwagger\TransfersApi($apiClient);
            $myAccount = $transferApi->create($transfer_request);

            if($myAccount){
                if($type == 'customer'){
                     $set = array(
                            'withdraw_status' => 1
                            );
                    $where['id'] = $gift_id;
                    $update = $this->Gift_model->update($set, $where);
                }
                //
                if($type == 'dwolla'){
                    
                     $set = array(
                            'fund_id' => $fundId,
                            'bank_name' => $bank_name,
                            );
                    $where['id'] = $gift_id;
                    $update = $this->Gift_model->update($set, $where);
                }
                //
                $response['code'] = 1;
                $response['status'] = "success";
                $response['message'] = 'Gift has been sent successfully.';
                echo json_encode($response);
            }
            else{
                 $response['code'] = 0;
                 $response['status'] = "error";
                 $response['message'] = 'Something went wrong. please try again.';
                 echo json_encode($response);
            }
        }
    }

    public function gift_send_func($postVar,$recipient_id){
            
            $payment_data = $this->Payment_model->getAnyData();
            $tax = $payment_data[0]->tax;

            $gift_data['title'] = $this->input->post('title');
            $gift_data['from_id'] = $this->input->post('from_id');
            $gift_data['recipient_id'] = $recipient_id;
            $gift_data['from_name'] = $this->input->post('from_name');
            $gift_data['video'] = $this->input->post('video');
            $gift_data['screenshot'] = $this->input->post('screenshot');
            $gift_data['amount'] = $this->input->post('amount');
            $gift_data['tax'] = $tax;
            $gift_data['final_amount'] = $this->input->post('final_amount');
            
            $gift_data['gift_status'] = 2 ;//0 not sent, 1 opened, 2 sent, 3 issue while running cron
            $gift_data['payment_type'] = "bank_account";
            $gift_data['gift_timestamp'] = $this->input->post('gift_timestamp');
            $gift_data['created_at'] = date("Y-m-d H:i:s");

            $fund_id = $this->input->post('fund_id');
            $amount = $this->input->post('amount');
            $type = $this->input->post('type');
            $unique_bank_id = $this->input->post('unique_bank_id');

            $insert = $this->Gift_model->Insert_Data($gift_data);
            if($insert)
            {
                $gift_id = $insert;
                $result_status = $this->depositFund($fund_id, $gift_id, $amount, $tax, $type, $unique_bank_id);
                if($result_status == 1) //if payment is successful
                {
                    ///////////send pushnotification to user//////
                    $where['id']= $insert;
                    $giftsdata = $this->Gift_model->getAnyData($where);
                    $from_id = $giftsdata[0]->from_id;
                    $where_from_id['id']= $from_id;
                    $from_data = $this->User_model->getAnyData($where_from_id);
                    //From id data
                    $amount = $giftsdata[0]->amount;
                    $tax = $giftsdata[0]->tax;
                    $final_amount = $giftsdata[0]->final_amount;
                    $bank_name = $giftsdata[0]->bank_name;
                    $created_at = $giftsdata[0]->created_at;
                    $created_at = strtotime($created_at);
                    $created_at = date('d/m/Y', $created_at);

                    $recipient_id = $giftsdata[0]->recipient_id;
                    $where_recipient_id['id']= $recipient_id;
                    $recipient_data = $this->User_model->getAnyData($where_recipient_id);

                    $where_transaction['gift_id']= $insert;
                    $transactionsdata = $this->Transactions_model->getAnyData($where_transaction);
                    //pr($transactionsdata); die;
                    $transaction_id = $transactionsdata[0]->id;
                    //send email to from user
                    $emaildata['to'] = $from_data[0]->email_id;
                    $emaildata['subject'] = 'GiftCast Receipt';            
                    $emaildata['message'] = file_get_contents(base_url().'email_templates/send_gift.php');
                    $emaildata['message'] = str_replace('AMOUNT_TITLE', $amount, $emaildata['message']);
                    $emaildata['message'] = str_replace('AMOUNT_TITLE_1', $amount, $emaildata['message']);
                    $emaildata['message'] = str_replace('TAX_PRICE', $tax, $emaildata['message']);
                    $emaildata['message'] = str_replace('TOTAL_AMOUNT', $final_amount, $emaildata['message']);
                    $emaildata['message'] = str_replace('TOTAL_AMOUNT_1', $final_amount, $emaildata['message']);
                    $emaildata['message'] = str_replace('BANKNAME', $bank_name, $emaildata['message']);
                    
                    $emaildata['message'] = str_replace('TRANSACTIONID', $transaction_id, $emaildata['message']);

                    $emaildata['message'] = str_replace('CREATEDDATE', $created_at, $emaildata['message']);

                    //Sender
                    if(!empty($from_data[0]->fname))
                    {
                        $fname = $from_data[0]->fname.'&nbsp;'.$from_data[0]->lname;
                        $emaildata['message'] = str_replace('SENDERNAME', $fname, $emaildata['message']);
                    }
                    else
                    {
                        $phone_number = $from_data[0]->phone_number;
                        $emaildata['message'] = str_replace('SENDERNAME', $phone_number, $emaildata['message']);
                    }

                    //Recipient 
                    if(!empty($recipient_data[0]->fname))
                    {
                        $fname = $recipient_data[0]->fname.'&nbsp;'.$recipient_data[0]->lname;;
                        $emaildata['message'] = str_replace('RECIPIENTNAME', $fname, $emaildata['message']);
                        $emaildata['message'] = str_replace('RECIPIENT_NAME_1', $fname, $emaildata['message']);
                        $emaildata['message'] = str_replace('RECIPIENT_NAME_2', $fname, $emaildata['message']);
                    }
                    else if(!empty($recipient_data[0]->phone_number))
                    {
                        $phone_number = $recipient_data[0]->phone_number;
                        $emaildata['message'] = str_replace('RECIPIENTNAME', $phone_number, $emaildata['message']);
                        $emaildata['message'] = str_replace('RECIPIENT_NAME_FOOTER', $phone_number, $emaildata['message']);
                    }
                    else
                    {
                        $email_id = $recipient_data[0]->email_id;
                        $emaildata['message'] = str_replace('RECIPIENTNAME', $email_id, $emaildata['message']);
                        $emaildata['message'] = str_replace('RECIPIENT_NAME_FOOTER', $email_id, $emaildata['message']);   
                    }

                    sendmail($emaildata);

                    //send email to (to) user
                    $emaildata_reciever['to'] = $recipient_data[0]->email_id;
                    $emaildata_reciever['subject'] = 'You recieved a GiftCast';            
                    $emaildata_reciever['message'] = file_get_contents(base_url().'email_templates/email_receive.php');
                    
                    
                    if(!empty($from_data[0]->fname))
                    {
                        $fname = $from_data[0]->fname.'&nbsp;'.$from_data[0]->lname;
                        $emaildata_reciever['message'] = str_replace('SENDERNAME', $fname, $emaildata_reciever['message']);
                    }
                    else
                    {
                        $phone_number = $from_data[0]->phone_number;
                        $emaildata_reciever['message'] = str_replace('SENDERNAME', $phone_number, $emaildata_reciever['message']);
                    }
                    //pr($emaildata_reciever); die;
                    sendmail($emaildata_reciever);

                    /*print_r($giftsdata);
                    print_r($from_data);
                    print_r($recipient_data[0]->fname.' '.$recipient_data[0]->lname);
                    die;*/
                    if(!empty($from_data))
                    {
                        $deviceToken = $from_data[0]->device_token;
                        $title = "Giftcast";
                        if(!empty($recipient_data[0]->fname))
                        {
                            if(!empty($recipient_data[0]->lname))
                            {
                                $fullname = $recipient_data[0]->fname.' '.$recipient_data[0]->lname;    
                            }
                            else
                            {
                                $fullname = $recipient_data[0]->fname;
                            }
                        }
                        else if(!empty($recipient_data[0]->phone_number))
                        {
                            $fullname = $recipient_data[0]->phone_number;
                        }
                        else
                        {
                            $fullname = $recipient_data[0]->email_id;   
                        }
                        
                        $message = "Your GiftCast to ".$fullname." has been delivered";
                        $giftdata = "";
                        
                        $device_type = $from_data[0]->device_type;
                        $notification_check['user_id'] = $from_data[0]->id;
                        //Check in notification tab if send gift notificaton is enabled or not
                        $notification_check = $this->Notification_model->getAnyData($notification_check);
                        $send_gift_flag = $notification_check[0]->send_gift;
                        if($send_gift_flag == 1)
                        {
                            if($device_type == 'ios')
                            {
                                send_ios_notification($deviceToken,$message,$title,$giftdata);    
                            }
                            else
                            {
                                //android
                                send_android_notification($deviceToken,$message,$title,$giftdata);
                            } 
                        }
                    }

                    
                    //Recipient id
                    //pr($recipient_data);
                    if(!empty($recipient_data))
                    {
                        $deviceToken = $recipient_data[0]->device_token;
                        $notification_check_reciever['user_id'] = $recipient_data[0]->id;

                        //Check in notification tab if recieve gift notificaton is enabled or not
                        
                        $notification_check_reciever = $this->Notification_model->getAnyData($notification_check_reciever);
                        $recieve_gift_flag = $notification_check_reciever[0]->recieve_gift;
                        $title = "GiftCast";
                        if(!empty($recipient_data[0]->fname))
                        {
                            if(!empty($recipient_data[0]->lname))
                            {
                                $message = "Hi ".$recipient_data[0]->fname.' '.$recipient_data[0]->lname.", you received a GiftCast";    
                            }
                            else
                            {
                                $message = "Hi ".$recipient_data[0]->fname.", you received a GiftCast";   
                            }
                        }
                        $giftdata = "";
                        $device_type = $recipient_data[0]->device_type;
                        if($recieve_gift_flag == 1)
                        {
                            if($device_type == 'ios')
                            {
                                send_ios_notification($deviceToken,$message,$title,$giftdata);    
                            }
                            else
                            {
                                //android
                                send_android_notification($deviceToken,$message,$title,$giftdata);
                            }
                        }
                        
                        /*$sendnotification = send_android_notification($deviceToken,$message,$title,$giftdata);*/
                    }
                    ///////////////////////////////////////////////
                    $response['code'] = 1;
                    $response['status'] = "success";
                    $response['gift_id'] = $insert;
                    $response['message'] = 'gift send successfully';
                    echo json_encode($response);    
                }
                else
                {
                    $response['code'] = 0;
                    $response['status'] = "error";
                    $response['message'] = 'something went wrong while connecting with Dwolla';
                    echo json_encode($response);
                }
            }
            else
            {
                $response['code'] = 0;
                $response['status'] = "error";
                $response['message'] = 'something went wrong';
                echo json_encode($response);
            }
    }

    function depositFund($fund_id, $gift_id, $amount, $tax, $type, $unique_bank_id){
        
        $postVar = $this->input->post();
        $fundId = $fund_id;
        $amount = $amount + $tax;
        $type = $type;
        $gift_id = $gift_id;
        $unique_bank_id = $unique_bank_id;// unique_bank_id
        $customerUrl = DWOLLA_CUSTOMER_URL.$fundId;
        $access_token = $this->getAccessToken();
        require('vendor/autoload.php');
        DwollaSwagger\Configuration::$access_token = $access_token;

        //DwollaSwagger\Configuration::$debug = 1;
        $apiClient = new DwollaSwagger\ApiClient(DWOLLA_API_URL);
        //echo DWOLLA_API_URL;
        $fsApi = new DwollaSwagger\FundingsourcesApi($apiClient);

        $fundingSources = $fsApi->getCustomerFundingSources($customerUrl,'');
        foreach ($fundingSources->_embedded->{'funding-sources'} as $key => $value) {
          if($value->type == 'bank' && $value->status == 'verified')
          {
            if($value->id == $unique_bank_id)
            {
                $fundId = $value->id;
                //echo '<br>';
                $bank_name = $value->bankName;
                //echo '<br>';
            }
          }
        }
        //pr($fundingSources); die;
        $dwollaId = $this->config->item('dwolla_merchant_id');
        $tranfer_fee = $this->config->item('dwolla_transfer_fee');
        if($type == 'dwolla'){
            $transfer_request = array (
              '_links' => 
              array (
                'source' => 
                array (
                  'href' => DWOLLA_FUND_URL.$fundId, //Joel@inlooh
                ),
                'destination' => 
                array (
                  'href' => DWOLLA_FUND_URL.$dwollaId,
                ),
              ),
              'amount' => 
              array (
                'currency' => 'USD',
                'value' => $amount,
              )
            );
        }
        else if($type == 'customer'){
            $transfer_request = array (
              '_links' => 
              array (
                'source' => 
                array (
                  'href' => DWOLLA_FUND_URL.$dwollaId, //Joel@inlooh
                ),
                'destination' => 
                array (
                  'href' => DWOLLA_FUND_URL.$fundId,
                ),
              ),
              'amount' => 
              array (
                'currency' => 'USD',
                'value' => $amount,
              )
            );
        }
        else{
             $response['code'] = 0;
             $response['status'] = "error";
             $response['message'] = 'Something went wrong. please try again.';
             echo json_encode($response);
        }

        $transferApi = new DwollaSwagger\TransfersApi($apiClient);
        $myAccount = $transferApi->create($transfer_request);
        

        $transfer = $transferApi->byId($myAccount);
        $transaction_id = $transfer->id;
        if($myAccount)
        {
            if($type == 'customer'){
                 $set = array(
                        'withdraw_status' => 1
                        );
                $where['id'] = $gift_id;
                $update = $this->Gift_model->update($set, $where);
            }
            //
            if($type == 'dwolla'){
                
                 $set = array(
                        'fund_id' => $fundId,
                        'bank_name' => $bank_name,
                        'unique_bank_id' => $unique_bank_id
                        );
                $where['id'] = $gift_id;
                $update = $this->Gift_model->update($set, $where);
            }
            //insert transaction details
            $transaction_data['gift_id'] = $gift_id;
            $transaction_data['transaction_id'] = $transaction_id;
            $transaction_data['payment_type'] = 'bank_account';
            $transaction_data['transaction_details'] = serialize($transfer);
            $transaction_data['created_at'] = date("Y-m-d H:i:s");
            $transaction_insert = $this->Transactions_model->Insert_Data($transaction_data);
            if($transaction_insert)
            {
                //
                return 1; // success
            }
            else
            {
                return 0; // error
            }
            
        }
        else
        {
            return 0; // error
        }
    }
    
    public function checkFund(){
        $email_id = $this->input->post('email_id');
        $customersApi = $this->dwollaConfig();
        $customers = $customersApi->_list('','',$email_id); // pass email address to get customer data
        // echo '<pre>';print_r($customers->_embedded->customers); die;
        if(!empty($customers->_embedded->customers)){
            $getCustomerId = $customers->_embedded->customers[0]->id; //get customer id
            $customerUrl = DWOLLA_CUSTOMER_URL.$getCustomerId; 
            $apiClient = new DwollaSwagger\ApiClient(DWOLLA_API_URL);
            $fsApi = new DwollaSwagger\FundingsourcesApi($apiClient);
            $fundingSources = $fsApi->getCustomerFundingSources($customerUrl,'');    
            $fundingArray = array();
            $checkVerified = 0;
            $fundId = '';
            foreach ($fundingSources->_embedded->{'funding-sources'} as $key => $value) 
            {
              if($value->type == 'bank' && $value->status == 'verified')
              { 
                if($value->removed != 1) //check if funding source is removed or inactive
                {
                    $fundId = $value->id;
                    $checkVerified++;
                }
              }
            }
            if($checkVerified > 0){
                $response['code'] = 1;
                $response['status'] = "success";
                $response['fundid'] = $fundId;
                $response['message'] = 'fund id is available.';    
            }
            else{
                $response['code'] = 0;
                $response['status'] = "error";
                $response['message'] = 'Fund id is not available.';
            }
            echo json_encode($response);
        }
        else{
              $response['code'] = 0;
              $response['status'] = "error";
              $response['message'] = 'Email address is not found.';
              echo json_encode($response);
        }

       
    }

    public function gift_send_later_func($postVar,$recipient_id){
        
        $payment_data = $this->Payment_model->getAnyData();
        $tax = $payment_data[0]->tax;

        $gift_data['title'] = $this->input->post('title');
        $gift_data['from_id'] = $this->input->post('from_id');
        $gift_data['recipient_id'] = $recipient_id;
        $gift_data['from_name'] = $this->input->post('from_name');
        $gift_data['video'] = $this->input->post('video');
        $gift_data['screenshot'] = $this->input->post('screenshot');
        $gift_data['amount'] = $this->input->post('amount');
        $gift_data['tax'] = $tax;
        $gift_data['final_amount'] = $this->input->post('final_amount');
        $gift_data['payment_type'] = "bank_account";
        $gift_data['gift_timestamp'] = $this->input->post('gift_timestamp');
        $gift_data['fund_id'] = $this->input->post('fund_id');
        $gift_data['unique_bank_id'] = $this->input->post('unique_bank_id');
        $gift_data['created_at'] = date("Y-m-d H:i:s");

        $insert = $this->Gift_model->Insert_Data($gift_data);
        if($insert)
        {
            $response['code'] = 1;
            $response['status'] = "success";
            $response['message'] = 'gift send successfully';
            echo json_encode($response);
        }
    }

    


    public function crone_gift_send()
    {
        //fetch data from gift whos status are not sent 
        // also fetch those time is current near to them
        //  call the payment api call
        //echo strtotime("now"); die;
        $select = "gifts.id as gift_id, gifts.*, users.id as users_id,users.*,notification_settings.send_gift";
        $join_arr[0] = array( 
                            "table_name" => "users", 
                            "cond" => "gifts.from_id=users.id", 
                            "type" => "inner" 
                        );
        $join_arr[1] = array( 
                            "table_name" => "notification_settings", 
                            "cond" => "users.id=notification_settings.user_id", 
                            "type" => "inner" 
                        );
        $where['gifts.gift_status']= 0;
        $result = $this->Gift_model->getAnyData($where,$select,"","",$join_arr);
        
        if(!empty($result))
        {
            $emaildata['to'] = 'jayminzap@gmail.com';
            $emaildata['subject'] = 'GiftCast Receipt'; 
            $emaildata['message'] = 'Crone Run , TimeStamp is '.strtotime("now").'<br>'.date('d-M-Y H:i:s');  
            sendmail($emaildata);

            foreach($result as $r)
            {
                $amount = $r->amount;
                $fund_id = $r->fund_id;
                $gift_timestamp = $r->gift_timestamp;
                $d1 = strtotime("now");
                $d2 = strtotime($gift_timestamp);
                if($d2 <= $d1)
                {
                    $device_type = $r->device_type;
                    $device_token = $r->device_token;
                    $notification_flag_send_gift = $r->send_gift;
                    
                    //nw compare the date and send it gift
                    //now do the payment starts
                    
                    $fundId = $r->fund_id; //"352019db-99d3-4c65-9685-5821d47e339f";
                    $amount = $r->amount; //18.10;
                    $tax = $r->tax; //;
                    $final_amount = $r->final_amount;
                    $total_amount = $amount + $tax; //this variable is clone for final_amount i.e ref for dwolla bank account
                    $type = "dwolla"; //just because cron job so will send it merchant (dwolla) only
                    $gift_id = $r->gift_id; //1;
                    $unique_bank_id = $r->unique_bank_id; //unique_bank_id
                    
                    $created_at = strtotime($gift_timestamp);
                    $created_at = date('d/m/Y',$created_at);
                    
                    $customerUrl = DWOLLA_CUSTOMER_URL.$fundId;
                    $access_token = $this->getAccessToken();

                    require('vendor/autoload.php');

                    DwollaSwagger\Configuration::$access_token = $access_token;
                    //DwollaSwagger\Configuration::$debug = 1;
                    $apiClient = new DwollaSwagger\ApiClient(DWOLLA_API_URL);
                    //echo DWOLLA_API_URL;
                    $fsApi = new DwollaSwagger\FundingsourcesApi($apiClient);

                    $fundingSources = $fsApi->getCustomerFundingSources($customerUrl,'');
                    foreach ($fundingSources->_embedded->{'funding-sources'} as $key => $value) 
                    {
                      /*if($value->type == 'bank' && $value->status == 'verified')
                      {
                        $fundId = $value->id;
                      }*/
                        if($value->id == $unique_bank_id)
                        {
                            $fundId = $value->id;
                            //echo '<br>';
                            $bank_name = $value->bankName;
                            //echo '<br>';
                        }
                    }
                    
                    $dwollaId = $this->config->item('dwolla_merchant_id');
                    //$tranfer_fee = $this->config->item('dwolla_transfer_fee');

                    if($type == 'dwolla')
                    {
                        $transfer_request = array (
                          '_links' => 
                          array (
                            'source' => 
                            array (
                              'href' => DWOLLA_FUND_URL.$fundId, //Joel@inlooh
                            ),
                            'destination' => 
                            array (
                              'href' => DWOLLA_FUND_URL.$dwollaId,
                            ),
                          ),
                          'amount' => 
                          array (
                            'currency' => 'USD',
                            'value' => $total_amount,
                          )
                        );
                    }
                    else
                    {
                         $response['code'] = 0;
                         $response['status'] = "error";
                         $response['message'] = 'Something went wrong. please try again.';
                         echo json_encode($response);
                    }
                    $transferApi = new DwollaSwagger\TransfersApi($apiClient);
                    $myAccount = $transferApi->create($transfer_request);

                    $transfer = $transferApi->byId($myAccount);
                    $transaction_id = $transfer->id;
                    
                    if($myAccount)
                    {
                        if($type == 'dwolla')
                        {
                            $where_user['id'] = $r->recipient_id;
                            $recipient_data = $this->User_model->getAnyData($where_user);

                            /*pr($recipient_data);*/

                            $where_user_from['id'] = $r->from_id;
                            $from_data = $this->User_model->getAnyData($where_user_from);
                                                      
                            /*pr($from_data);
                            die;*/
                            //send email to from user
                            $emaildata['to'] = $from_data[0]->email_id;
                            $emaildata['subject'] = 'GiftCast Receipt';            
                            $emaildata['message'] = file_get_contents(base_url().'email_templates/send_gift.php');
                            $emaildata['message'] = str_replace('AMOUNT_TITLE', $amount, $emaildata['message']);
                            $emaildata['message'] = str_replace('AMOUNT_TITLE_1', $amount, $emaildata['message']);
                            $emaildata['message'] = str_replace('TAX_PRICE', $tax, $emaildata['message']);
                            $emaildata['message'] = str_replace('TOTAL_AMOUNT', $final_amount, $emaildata['message']);
                            $emaildata['message'] = str_replace('TOTAL_AMOUNT_1', $final_amount, $emaildata['message']);
                            $emaildata['message'] = str_replace('BANKNAME', $bank_name, $emaildata['message']);
                            $emaildata['message'] = str_replace('TRANSACTIONID', $transaction_id, $emaildata['message']);

                            $emaildata['message'] = str_replace('CREATEDDATE', $created_at, $emaildata['message']);

                            //Sender
                            if(!empty($from_data[0]->fname))
                            {
                                $fname = $from_data[0]->fname.'&nbsp;'.$from_data[0]->lname;
                                $emaildata['message'] = str_replace('SENDERNAME', $fname, $emaildata['message']);
                            }
                            else
                            {
                                $phone_number = $from_data[0]->phone_number;
                                $emaildata['message'] = str_replace('SENDERNAME', $phone_number, $emaildata['message']);
                            }


                            //Recipient 
                            if(!empty($recipient_data[0]->fname))
                            {
                                $fname = $recipient_data[0]->fname.'&nbsp;'.$recipient_data[0]->lname;;
                                $emaildata['message'] = str_replace('RECIPIENTNAME', $fname, $emaildata['message']);
                                $emaildata['message'] = str_replace('RECIPIENT_NAME_1', $fname, $emaildata['message']);
                                $emaildata['message'] = str_replace('RECIPIENT_NAME_2', $fname, $emaildata['message']);
                            }
                            else if(!empty($recipient_data[0]->phone_number))
                            {
                                $phone_number = $recipient_data[0]->phone_number;
                                $emaildata['message'] = str_replace('RECIPIENTNAME', $phone_number, $emaildata['message']);
                                $emaildata['message'] = str_replace('RECIPIENT_NAME_FOOTER', $phone_number, $emaildata['message']);
                            }
                            else
                            {
                                $email_id = $recipient_data[0]->email_id;
                                $emaildata['message'] = str_replace('RECIPIENTNAME', $email_id, $emaildata['message']);
                                $emaildata['message'] = str_replace('RECIPIENT_NAME_FOOTER', $email_id, $emaildata['message']);   
                            }

                            sendmail($emaildata);
                            //send email to (to) user
                            $emaildata_reciever['to'] = $recipient_data[0]->email_id;
                            $emaildata_reciever['subject'] = 'GiftCast Receipt';            
                            $emaildata_reciever['message'] = file_get_contents(base_url().'email_templates/email_receive.php');
                            
                            
                            if(!empty($from_data[0]->fname))
                            {
                                $fname = $from_data[0]->fname.'&nbsp;'.$from_data[0]->lname;
                                $emaildata_reciever['message'] = str_replace('SENDERNAME', $fname, $emaildata_reciever['message']);
                            }
                            else
                            {
                                $phone_number = $from_data[0]->phone_number;
                                $emaildata_reciever['message'] = str_replace('SENDERNAME', $phone_number, $emaildata_reciever['message']);
                            }
                            sendmail($emaildata_reciever);
                            //
                            //pr($from_data); 
                            if(!empty($from_data))
                            {
                                $deviceToken = $from_data[0]->device_token;
                                $title = "Giftcast";
                                $message = "Your GiftCast to ".$recipient_data[0]->fname.' '.$recipient_data[0]->lname." has been delivered";
                                $giftdata = "";
                                
                                $device_type = $from_data[0]->device_type;
                                $notification_check['user_id'] = $from_data[0]->id;
                                //pr($notification_check); 
                                //Check in notification tab if send gift notificaton is enabled or not
                                $notification_check = $this->Notification_model->getAnyData($notification_check);
                                $send_gift_flag = $notification_check[0]->send_gift;
                                if($send_gift_flag == 1)
                                {
                                    if($device_type == 'ios')
                                    {
                                        send_ios_notification($deviceToken,$message,$title,$giftdata);    
                                    }
                                    else
                                    {
                                        //android
                                        send_android_notification($deviceToken,$message,$title,$giftdata);
                                    } 
                                }
                            }

                            //Recipient id
                            //pr($recipient_data);
                            if(!empty($recipient_data))
                            {
                                $deviceToken = $recipient_data[0]->device_token;
                                $notification_check_reciever['user_id'] = $recipient_data[0]->id;

                                //Check in notification tab if recieve gift notificaton is enabled or not
                                
                                $notification_check_reciever = $this->Notification_model->getAnyData($notification_check_reciever);
                                $recieve_gift_flag = $notification_check_reciever[0]->recieve_gift;
                                $title = "Giftcast";
                                $message = "Hi ".$recipient_data[0]->fname.' '.$recipient_data[0]->lname.", you received a GiftCast";
                                $giftdata = "";
                                $device_type = $recipient_data[0]->device_type;
                                if($recieve_gift_flag == 1)
                                {
                                    if($device_type == 'ios')
                                    {
                                        send_ios_notification($deviceToken,$message,$title,$giftdata);    
                                    }
                                    else
                                    {
                                        //android
                                        send_android_notification($deviceToken,$message,$title,$giftdata);
                                    }
                                }
                            }
                            ///////////////////////////////////////////////
                            
                            $set['gift_status'] = 2;
                            $set['updated_at']  = date("Y-m-d H:i:s");
                            $where_gift_status['id'] = $gift_id;
                            $update = $this->Gift_model->update($set, $where_gift_status);

                            $transaction_data['gift_id'] = $gift_id;
                            $transaction_data['transaction_id'] = $transaction_id;
                            $transaction_data['payment_type'] = 'bank_account';
                            $transaction_data['transaction_details'] = serialize($transfer);
                            $transaction_data['created_at'] = date("Y-m-d H:i:s");
                            $transaction_insert = $this->Transactions_model->Insert_Data($transaction_data);
                            
                        }
                    }
                    else
                    {
                        $set['gift_status'] = 3;
                        $set['updated_at']  = date("Y-m-d H:i:s");
                        $where_gift_status['id'] = $gift_id;
                        $update = $this->Gift_model->update($set, $where_gift_status);
                        if($update)
                        {    
                            $response['code'] = 0;
                            $response['status'] = "error";
                            $response['message'] = 'Something went wrong. please try again.';
                            echo json_encode($response);
                        }
                    }
                    $response['code'] = 1;
                    $response['status'] = "success";
                    $response['message'] = 'Gift has been sent successfully.';
                    echo json_encode($response);
                }
                //ends
                else
                {
                    $response['code'] = 0;
                    $response['status'] = "error";
                    $response['message'] = 'Issue with time';
                    echo json_encode($response);           
                }
            }
            
        }
        else
        {
            $response['code'] = 0;
            $response['status'] = "error";
            $response['message'] = 'All gifts are already been sent';
            echo json_encode($response);
        }
    }
    /**
 * @api {post} api/ws_gift_opened Gift Opened
 * @apiVersion 1.0.0
 * @apiName GiftOpened
 * @apiGroup Gifts
 *
 * @apiDescription Sender gets notification once reciever opens the gifts
 *
 * @apiParam {Number} gift_id The Gift-ID.
 * @apiParam {Number} recipient_id The Recipient-ID.
 */

    public function gift_opened()
    {
        $this->form_validation->set_rules('gift_id', 'gift_id', 'trim|required');
        $this->form_validation->set_rules('recipient_id', 'recipient_id', 'trim|required');
        /* Check Validation For Field Require */
        if ($this->form_validation->run() === FALSE) 
        {
            $response['code'] = 0;
            $response['status'] = "error";
            $response['message'] = 'Please enter all fields';
            echo json_encode($response);
        } 
        else 
        {
            $recipient_id = $this->input->post('recipient_id');
            $gift_id = $this->input->post('gift_id');
            $select ="gifts.*,users.fname,users.lname,users.device_token,users.device_type,notification_settings.*";
            $join_arr[0] = array( 
                                "table_name" => "users", 
                                "cond" => "gifts.from_id=users.id", 
                                "type" => "inner" 
                                );
            $join_arr[1] = array( 
                                "table_name" => "notification_settings", 
                                "cond" => "notification_settings.user_id=users.id", 
                                "type" => "inner" 
                                );
            $where['gifts.id'] =  $gift_id;
            $where['gifts.recipient_id'] =  $recipient_id;
            $giftdata = $this->Gift_model->getAnyData($where,$select,"","",$join_arr);

            if(!empty($giftdata))
            {
                /*@Note: Once gift is send,gift_status will become 2 and recipient_gift_status will be 0, once it is opened it will change the status.*/
                //gift_status - 0 not sent, 1 opened, 2 sent, 3 issue while running cron is for normal gift send.
                //recipient_gift_status 0 - not opened, 1 - already opened.

                $giftstatus = $giftdata[0]->recipient_gift_status;
                if($giftstatus == 0)
                {
                    $set['recipient_gift_status'] = 1;
                    $set['updated_at'] = date("Y-m-d H:i:s");
                    $update = $this->Gift_model->update($set, $where);
                    if($update)
                    {
                        //send notification to from_id 
                        $message = "Your GiftCast to ".$giftdata[0]->fname.' '.$giftdata[0]->lname.' has been viewed';
                        $deviceToken = $giftdata[0]->device_token;
                        $title = "Giftcast";
                        $message = $message;
                        $giftdata = "";
                       /* echo $deviceToken = $giftdata[0]->device_type;*/
                       $device_type = $giftdata[0]->device_type;
                       $gift_opened = $giftdata[0]->gift_opened;
                       if($gift_opened == 1)
                       {
                            if($device_type == 'ios')
                            {
                                send_ios_notification($deviceToken,$message,$title,$giftdata);
                            }
                            else
                            {
                                send_android_notification($deviceToken,$message,$title,$giftdata);
                            }
                        }
                           
                        $response['code'] = 1;
                        $response['status'] = "success";
                        $response['message'] = 'Gift Opened successfully';
                        echo json_encode($response);
                    }
                }
                else
                {
                    $response['code'] = 1;
                    $response['status'] = "success";
                    $response['message'] = 'Gift already Opened';
                    echo json_encode($response);
                }
            }
            else
            {
                $response['code'] = 1;
                $response['status'] = "error";
                $response['message'] = 'No Gifts exists';
                echo json_encode($response);
            }
        }
    }
}
