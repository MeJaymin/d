<?php

class User extends CI_Controller {

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Logs_model');
        $this->load->model('Notification_model');
        $this->load->model('Payment_model');
        $this->load->model('Report_model');
        $this->load->library('email');
    }

     /**
 * @api {post} api/ws_signup User Signup
 * @apiVersion 1.0.0
 * @apiName UserSignup
 * @apiGroup Users
 *
 * @apiDescription Signup
 *
 * @apiParam {Number} email_id Email id.
 * @apiParam {Character} fname First Name.
 * @apiParam {Character} [lname] Last Name.
 * @apiParam {Character} password Password.
 * @apiParam {Number} phone_number Phone Number.
 * @apiParam {Number} device_token Device token
 * @apiParam {Number} device_type Device Type E.g Android, Ios.
 */
    /*
    @Author: Jaymin Sejpal
    @description: Signup Webservice for User
    */

    public function signup() 
    {
        $baseurl = $this->config->base_url();
        $this->form_validation->set_rules('email_id', 'Email', 'trim|required');
        $this->form_validation->set_rules('fname', 'First_name', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('phone_number', 'Phone_number', 'trim|required');
        $this->form_validation->set_rules('device_token', 'device_token', 'trim|required');
        $this->form_validation->set_rules('device_type', 'device_type', 'trim|required');
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
            $Insert = "";
            $base_url = $this->config->base_url();
            $signup_data['email_id'] = $this->input->post('email_id');
            $signup_data['fname'] = $this->input->post('fname');
            $signup_data['lname'] = $this->input->post('lname');
            $signup_data['password'] = md5($this->input->post('password'));
            $signup_data['phone_number'] = $this->input->post('phone_number');
            $signup_data['profile_picture'] = "";
            $signup_data['device_token'] = $this->input->post('device_token');
            $signup_data['device_type'] = $this->input->post('device_type');
            $signup_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $signup_data['status'] = "1";
            $signup_data['created_at'] = date("Y-m-d H:i:s");

            $Insert = $this->User_model->Insert_Data($signup_data);

            if (!empty($Insert)) 
            {
                $notification_data['user_id'] = $Insert;
                $notification_data['recieve_gift'] = 1; //by default 1
                $notification_data['send_gift'] = 1; //by default 1
                $notification_data['contacts_joined'] = 1; //by default 1
                $notification_data['admin_giftcast_settings'] = 1; //by default 1
                $notification_data['gift_opened'] = 1; //by default 1
                $notification_data['created_at'] = date("Y-m-d H:i:s");
                $notification_insert = $this->Notification_model->Insert_Data($notification_data);
                if(!empty($notification_insert))
                {
                    $signup_data['id'] = $Insert;
                    $success_message = 'Registration Successful.';
                    $response['code'] = 1;
                    $response['status'] = "success";
                    unset($signup_data['password']);
                    $signup_data['is_active'] = $signup_data['status'];
                    $response['data'] = $signup_data;
                    $response['message'] = $success_message;
                    //Sending Email
                    $baseurl = $this->config->base_url();
                    $fname = $this->input->post('fname');
                    $lname = $this->input->post('lname');
                    $email = $this->input->post('email_id');
                    $emaildata['to'] = $email;
                    $emaildata['subject'] = 'Giftcast - Welcome';
                    $emaildata['message'] = "Hello".' '.$fname.''.$lname.",<br><br>Welcome to Giftcast
                    .<br/><br/> Thanks,<br/><b>Giftcast Team</b>.";
                    sendmail($emaildata);        
                    echo json_encode($response);
                }
            } 
            else 
            {
                $response['code'] = 0;
                $response['status'] = "error";
                $response['message'] = 'Email Or Mobile Number already exist';
                echo json_encode($response);
            }
        }
    }

    /**
 * @api {post} api/ws_signin User Login
 * @apiVersion 1.0.0
 * @apiName UserLogin
 * @apiGroup Users
 *
 * @apiDescription Login
 *
 * @apiParam {Number} email_id Email id.
 * @apiParam {Character} password Password.
 * @apiParam {Number} device_token Device token
 * @apiParam {Number} device_type Device Type E.g Android, Ios.
 */
    /*
    @Author: Jaymin Sejpal
    @description: Login WS for a user.*/

    public function login() 
    {

        /* Field required Validation */
        $this->form_validation->set_rules('email_id', 'Email', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('device_token', 'device_token', 'trim|required');
        $this->form_validation->set_rules('device_type', 'device_type', 'trim|required');
        if ($this->form_validation->run() === FALSE) 
        {
            $response['code'] = 0;
            $response['status'] = "error";
            $response['message'] = 'Please enter all fields';
            echo json_encode($response);
        } 
        else 
        {
            $data_log['email_id'] = $this->input->post('email_id');
            $data_log['password'] = md5($this->input->post('password'));
            
            $ipaddress = $_SERVER['REMOTE_ADDR'];
            $device_token = $this->input->post('device_token');
            $device_type = $this->input->post('device_type');
            $result = $this->User_model->getAnyData($data_log, '', '', '', '');
            
            if (!empty($result)) 
            {
                if ($result[0]->status == 1) 
                {
                    $set['device_token'] = $device_token;
                    $set['device_type'] = $device_type;
                    $set['updated_at'] = date("Y-m-d H:i:s");
                    $where['id'] = $result[0]->id;
                    $update = $this->User_model->update($set, $where);
                    
                    $data=array(
                            "user_id" => $result[0]->id,
                            "loggedin_time" => date("Y-m-d H:i:s"),
                            "ipaddress" => $ipaddress,
                            "device_token" => $device_token,
                            "device_type" => $device_type
                        );
                    $this->Logs_model->Insert_Data($data);
                    $email = $this->input->post('email');

                    $where = array('user_id' => $result[0]->id);
                    $notification_data = $this->Notification_model->getAnyData($where);

                    $data['id'] = $result[0]->id;
                    $data['email_id'] = $result[0]->email_id;
                    $data['phone_number'] = $result[0]->phone_number;
                    
                    $data['fname'] = $result[0]->fname;
                    $data['lname'] = $result[0]->lname;
                    $data['profile_picture'] = "";
                    if(!empty($result[0]->profile_picture) && $result[0]->profile_picture!="")
                    {
                        $data['profile_picture'] = ASSETS_URL.'profile_pictures/'.$result[0]->profile_picture;
                    }
                    
                    $data['is_active'] = $result[0]->status;

                    $tax_data = $this->Payment_model->getAnyData();
                    $tax = $tax_data[0]->tax;
                    $response['code'] = 1;
                    $response['status'] = "success";
                    $response['data'] = $data;
                    $response['notification_data'] = $notification_data;
                    $response['tax'] = $tax;
                    $response['message'] = 'Login successfully';
                    echo json_encode($response);
                } 
                else 
                {
                    $response['code'] = 0;
                    $response['status'] = "error";
                    $response['message'] = 'Your account is under verification process';
                    echo json_encode($response);
                }
            } 
            else 
            {
                $response['code'] = 0;
                $response['status'] = "Error";
                $response['message'] = 'Invalid Email or Password';
                echo json_encode($response);
            }
        }
    }

     /**
 * @api {post} api/ws_forget_password Forget Password
 * @apiVersion 1.0.0
 * @apiName ForgetPassword
 * @apiGroup Users
 *
 * @apiDescription Forgte Password
 *
 * @apiParam {Number} email_id Email id.
 */
    /*@Author: Jaymin Sejpal
      @description: Forget Password Webservice for User
     */
   
    public function forgot_password() 
    {
        $this->form_validation->set_rules('email_id', 'Email', 'trim|required');

        if ($this->form_validation->run() === FALSE) 
        {
            $response['code'] = 0;
            $response['status'] = "error";
            $response['message'] = 'Please enter your email address.';
            echo json_encode($response);
        }
        else 
        {
            $email = $this->input->post('email_id');
            $data = array(
                'email_id' => $email
            );
            $check = $this->User_model->getAnyData($data);
            if (!empty($check)) 
            {
                $baseurl = $this->config->base_url();
                $emaildata['to'] = $email;
                $emaildata['subject'] = 'Giftcast - Forgot Password';
                $lname = '';
                if ($check[0]->lname != '') 
                {
                    $lname = " " . $check[0]->lname;
                }
                $name = $check[0]->fname . $lname;


                $email_enc = base64_encode($check[0]->email_id);
                $id_enc = base64_encode($check[0]->id);
                $time = time();
                $url = $baseurl . "changepwd?unq=" . $id_enc . "&em=" . $email_enc . "&tm=" . $time;
                $emaildata['message'] = "Hello $name,<br/><br/> Please <a href='$url'>Click</a> here to reset your password.<br/>";
                sendmail($emaildata);
                $response['code'] = 1;
                $response['status'] = "success";
                $response['message'] = 'Please check your email to change your password.';
                
                echo json_encode($response);
            } 
            else 
            {
                $response['code'] = 0;
                $response['status'] = "error";
                $response['message'] = 'User with this email address does not exist.';
                echo json_encode($response);
            }
        }
    }
    
    /*
    @Author: Jaymin Sejpal
    @description: Update User WS for a user.*/

    public function update_user_details()
    {
        $this->form_validation->set_rules('id', 'Id', 'trim|required');
        $this->form_validation->set_rules('fname', 'Fname', 'trim');
        $this->form_validation->set_rules('lname', 'Lname', 'trim');
        $this->form_validation->set_rules('email_id', 'Email', 'trim');
        $this->form_validation->set_rules('phone_number', 'Phoneno', 'trim');
        $this->form_validation->set_rules('current_password', 'Current', 'trim');
        $this->form_validation->set_rules('new_password', 'New', 'trim');
        //$this->form_validation->set_rules('flag', 'flag', 'trim|required');
        $baseurl = $this->config->base_url();
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
            $fname = $this->input->post('fname');
            $lname = $this->input->post('lname');
            $email_id = $this->input->post('email_id');
            $phone_number = $this->input->post('phone_number');
            $current_password = $this->input->post('current_password');
            $new_password = $this->input->post('new_password');
            $profile_picture = $this->input->post('profile_picture');
            $where = array('id' => $id);
            $result = $this->User_model->getAnyData($where);
            
            if(!empty($result))
            {
                /* Start: Image Uploading using base64 */
                if(isset($profile_picture))
                {
                    $upload_item_dir = './assets/profile_pictures/';
                    $data = base64_decode($profile_picture);
                    $imagename = time() . '.png';
                    $save_file = $upload_item_dir . $imagename;
                    $success = file_put_contents($save_file, $data);
                    $target_path = $baseurl."assets/profile_pictures_thumbnails/";
                    $source_path = $baseurl."assets/profile_pictures/".$imagename;
                    /*Image thumbnail code starts*/
                     /*$config_manip = array(
                    'image_library' => 'gd2',
                    'source_image' => $source_path,
                    'new_image' => $target_path,
                    'maintain_ratio' => TRUE,
                    'create_thumb' => TRUE,
                    'thumb_marker' => '_thumb',
                    'width' => 150,
                    'height' => 150
                    );
                    
                    $this->load->library('image_lib');
                    $this->image_lib->initialize($config_manip);
                    if (!$this->image_lib->resize()) 
                    {
                        echo $this->image_lib->display_errors();
                    }*/
                    /*Image thumbnail code ends*/
                    if($success)
                    {
                        if(!empty($result[0]->profile_picture) && $result[0]->profile_picture!="")
                        {
                            unlink('./assets/profile_pictures/'.$result[0]->profile_picture);
                        }
                        $set['profile_picture']=$imagename;
                        $set['updated_at'] = date("Y-m-d H:i:s");
                    }
                }
                /* End: Image Uploading using base64 */
                if(isset($fname))
                {
                    $set['fname']=$fname;
                    $set['updated_at'] = date("Y-m-d H:i:s");
                }
                if(isset($lname))
                {
                    $set['lname']=$lname;
                    $set['updated_at'] = date("Y-m-d H:i:s");
                }
                if(isset($email_id))
                {
                    
                    $data = array(
                        'email_id' => $email_id,
                        'id!=' => $id
                    );
                    $check = $this->User_model->getAnyData($data);
                    if (empty($check)) 
                    {
                        $set['email_id']=$email_id;
                        $set['updated_at'] = date("Y-m-d H:i:s");
                    }
                    else
                    {
                        $response['code'] = 0;
                        $response['status'] = "error";
                        $response['message'] = 'Email Address has been already registered.';
                    }
                }
                if(isset($phone_number))
                {
                    $set['phone_number']=$phone_number;
                    $set['updated_at'] = date("Y-m-d H:i:s");
                }
                if(isset($new_password) && isset($current_password))
                {
                    $data = array(
                        'id' => $id,
                        'password' => md5($current_password)
                    );
                    $check = $this->User_model->getAnyData($data);

                    if (!empty($check)) 
                    {
                        $set['password']=md5($new_password);
                        $set['updated_at'] = date("Y-m-d H:i:s");
                    }
                    else
                    {
                        $response['code'] = 0;
                        $response['status'] = "error";
                        $response['message'] = 'Current Password is not asssociated with this id.';
                    }
                }
                $set['updated_at'] = date("Y-m-d H:i:s");
                $update = $this->User_model->update($set, $where);
                if(empty($response))
                {
                    $result = $this->User_model->getAnyData($where);
                    $responsedata['id'] = $result[0]->id;
                    $responsedata['fname'] = $result[0]->fname;
                    $responsedata['lname'] = $result[0]->lname;
                    $responsedata['email_id'] = $result[0]->email_id;
                    $responsedata['profile_picture'] = "";
                    if(!empty($result[0]->profile_picture) && $result[0]->profile_picture!="")
                    {
                        $responsedata['profile_picture'] = ASSETS_URL.'profile_pictures/'.$result[0]->profile_picture;
                    }
                    
                    $responsedata['phone_number'] = $result[0]->phone_number;
                    $responsedata['is_active'] = $result[0]->status;
                    $response['code'] = 1;
                    $response['status'] = "Success";
                    $response['data'] = $responsedata;
                    $response['message'] = 'User Details has been changed successfully.';
                    $response['code'] = 1;
                    $response['status'] = "success";
                    $response['message'] = 'Success';
                    echo json_encode($response);
                }
                else
                {
                    echo json_encode($response);
                }
            }
            else
            {
                $response['code'] = 0;
                $response['status'] = "error";
                $response['message'] = 'Id does not exists';
                echo json_encode($response);
            }
        }
    }

    /**
 * @api {post} api/ws_fetch_user_details User Details
 * @apiVersion 1.0.0
 * @apiName UserDetails
 * @apiGroup Users
 *
 * @apiDescription Login
 *
 * @apiParam {Number} id User id.
 */

    public function user_details()
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
            $id = $this->input->post('id');
            $where = array(
                'users.id' => $id
            );
            $baseurl = $this->config->base_url();
            $select = "users.id as user_id,users.fname,users.lname,users.phone_number,users.email_id,users.status,users.profile_picture,user_logs.ipaddress,user_logs.device_token,user_logs.device_type,user_logs.loggedin_time";
            $join_arr[0] = array( 
                                "table_name" => "user_logs", 
                                "cond" => "users.id=user_logs.id", 
                                "type" => "inner" 
                                );
            $result = $this->User_model->getAnyData($where,$select,"","",$join_arr,"");
            if (!empty($result)) 
            {
                $responsedata['user_id'] = $result[0]->user_id;
                $responsedata['fname'] = $result[0]->fname;
                $responsedata['lname'] = $result[0]->lname;
                $responsedata['phone_number'] = $result[0]->phone_number;
                $responsedata['email_id'] = $result[0]->email_id;
                if(!empty($result[0]->profile_picture))
                {
                    $responsedata['profilepicture'] =  $baseurl.'assets/profile_pictures/'. $result[0]->profile_picture;
                }
                else
                {
                    $responsedata['profilepicture'] = "";
                }
                $responsedata['ipaddress'] = $result[0]->ipaddress;
                $responsedata['device_token'] = $result[0]->device_token;
                $responsedata['device_type'] = $result[0]->device_type;
                $responsedata['is_active'] = $result[0]->status;
                $responsedata['loggedin_time'] = $result[0]->loggedin_time;

                $where = array('user_id' => $result[0]->user_id);
                $notification_data = $this->Notification_model->getAnyData($where);

                $tax_data = $this->Payment_model->getAnyData();
                $tax = $tax_data[0]->tax;

                $response['code'] = 1;
                $response['status'] = "success";
                $response['data'] = $responsedata;
                $response['notification_data'] = $notification_data;
                $response['tax'] = $tax;
                $response['message'] = 'User Details has been fetched successfully.'; 
            }
            else
            {
                $response['code'] = 0;
                $response['status'] = "error";
                $response['message'] = 'User Does not exists. Try new one';
            }
            echo json_encode($response);
        }
    }
    
    /**
 * @api {post} api/ws_invite Invite Friend
 * @apiVersion 1.0.0
 * @apiName InviteAFriend
 * @apiGroup Users
 *
 * @apiDescription Invite a friend
 *
 * @apiParam {Number} id User id.
 * @apiParam {Number} recipient User id.
 * @apiParam {Character} title Title.
 * @apiParam {Character} message Message.
 */

    public function friend_invitaion()
    {
        $this->form_validation->set_rules('id', 'Id', 'trim|required');
        $this->form_validation->set_rules('recipient', 'Recipient', 'trim|required');
        $this->form_validation->set_rules('title', 'Title', 'trim|required');
        $this->form_validation->set_rules('message', 'Message', 'trim|required');

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
            $id = $postVar['id'];
            $data = array(
                'id' => $id
            );
            $result = $this->User_model->getAnyData($data);
            $fullname = $result[0]->fname. ' '.$result[0]->lname;
            $baseurl = $this->config->base_url();
            $emaildata['to'] = $postVar['recipient'];
            $emaildata['subject'] = 'Giftcast - Invitation Mail';
            $message = "Hello,<br/>";
            $message .= "Your friend ".$fullname." has invited you to join.<br/>";
            $emaildata['message'] = $message;
            sendmail($emaildata);
            $response['code'] = 1;
            $response['status'] = "success";
            $response['message'] = 'Invite Sent to your friend.';
            echo json_encode($response);
        }
    }

    /**
 * @api {post} api/ws_deleteuser Delete Account
 * @apiVersion 1.0.0
 * @apiName DeleteAccount
 * @apiGroup Users
 *
 * @apiDescription Delete Account of user
 *
 * @apiParam {Number} id User id.
 * @apiParam {Character} reason Reason for deleting.
 * @apiParam {Character} password Password.
 */

    ///Delete Account Webservice///
    public function delete_account()
    {
        $this->form_validation->set_rules('id', 'Id', 'trim|required');
        $this->form_validation->set_rules('reason', 'Reason', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

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
            $id = $postVar['id'];
            $password = $postVar['password'];
            $data = array(
                'id' => $id,
                'password' => md5($password)
            );
            $result = $this->User_model->getAnyData($data);
            $checkUser = count($result);
            if($checkUser > 0){
                $set = array('status' => 0);
                $where = array('id' => $id);
                $removeUser = $this->User_model->update($set, $where);
                if($removeUser){
                    $response['code'] = 1;
                    $response['status'] = "success";
                    $response['message'] = 'Account has been deleted.';
                    echo json_encode($response);
                }
                else{ 
                    $response['code'] = 0;
                    $response['status'] = "error";
                    $response['message'] = 'The User does not exist.';
                    echo json_encode($response);
                }
            }
            else{
                  $response['code'] = 0;
                  $response['status'] = "error";
                  $response['message'] = 'The User does not exist.';
                  echo json_encode($response);
            }
            
        }
    }

    /**
 * @api {post} api/ws_reportproblem Report Account
 * @apiVersion 1.0.0
 * @apiName ReportProblem
 * @apiGroup Report User
 *
 * @apiDescription Delete Account of user
 *
 * @apiParam {Number} id User id.
 * @apiParam {Character} topic Topic For Reporting.
 * @apiParam {Character} title Title For Reporting.
 * @apiParam {Character} complaint Complaint.
 */

    ///// Report a Problem Webservice
    public function report_problem()
    {
        $this->form_validation->set_rules('id', 'Id', 'trim|required');
        $this->form_validation->set_rules('topic', 'Topic', 'trim|required');
        $this->form_validation->set_rules('title', 'Title', 'trim|required');
        $this->form_validation->set_rules('complaint', 'Complaint', 'trim|required');

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
            $id = $postVar['id'];
            $data = array(
                'id' => $id
            );
            $result = $this->User_model->getAnyData($data);
            
            $checkUser = count($result);
            if($checkUser > 0){
                $data = array(
                    'user_id' => $id,
                    'topic' => $postVar['topic'],
                    'title' => $postVar['title'],
                    'complaint' => $postVar['complaint'],
                    'created_at' => date("Y-m-d H:i:s"),
                    );
                $insert = $this->Report_model->Insert_Data($data);
                if($insert)
                {
                    //send email
                    $emaildata['to'] =  "jayminzap@gmail.com";//"customercare@giftcast.me";
                    $emaildata['subject'] = 'Giftcast - Report a Problem';
                    $emaildata['message'] = "Hello Admin,<br/><br/> You got a complaint report.<br/>Below are the details:<br>Name : ".$result[0]->fname.' '.$result[0]->lname."<br>Topic : ".$postVar['topic']."<br>Title : ".$postVar['title']."<br>Complaint : ".$postVar['complaint'];
                    sendmail($emaildata);
                    
                }
                 $response['code'] = 1;
                 $response['status'] = "success";
                 $response['message'] = 'Your problem is reported.';
                 echo json_encode($response); 
            }
            else{
                $response['code'] = 0;
                $response['status'] = "error";
                $response['message'] = 'The User does not exist.';
                echo json_encode($response);    
            }
        }
    }

    /**
 * @api {post} api/ws_fblogin Facebook Login
 * @apiVersion 1.0.0
 * @apiName FacebookLogin
 * @apiGroup Users
 *
 * @apiDescription Facebook Login for a user
 *
 * @apiParam {Number} facebook_id Facebook Unique id.
 * @apiParam {Character} email_id Email id.
 */


    public function facebook_login()
    {
        $this->form_validation->set_rules('email_id', 'Email', 'trim|required');
        $this->form_validation->set_rules('facebook_id', 'Facebook_id', 'trim|required');

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
            $email_id = $postVar['email_id'];
            $facebook_id = $postVar['facebook_id'];
            $device_token = $postVar['device_token'];
            $device_type = $postVar['device_type'];

            $data['email_id'] = $email_id;
            $data['facebook_id'] = $facebook_id;
            $select = "email_id = '$email_id' OR facebook_id = '$facebook_id'";
            $result = $this->User_model->getAnyData($select);
            if(!empty($result))
            {
                $set['device_type'] = $device_type;
                $set['device_token'] = $device_token;
                $set['updated_at'] = date("Y-m-d H:i:s");

                $where['id'] = $result[0]->id;
                
                $updateUser = $this->User_model->update($set, $where);
                if($updateUser)
                {
                    $fblogin_data['id'] = $result[0]->id;
                    $fblogin_data['fname'] = $result[0]->fname;
                    $fblogin_data['lname'] = $result[0]->lname;
                    $fblogin_data['email_id'] = $result[0]->email_id;
                    $fblogin_data['facebook_id'] = $result[0]->facebook_id;
                    $fblogin_data['phone_number'] = $result[0]->phone_number;
                    $fblogin_data['status'] = $result[0]->status;
                    $fblogin_data['profile_picture'] = $result[0]->profile_picture;
                    $fblogin_data['device_token'] = $device_token;
                    $fblogin_data['device_type'] = $device_type;
                    $fblogin_data['ip_address'] = $result[0]->ip_address;
                    $fblogin_data['created_at'] = $result[0]->created_at;

                    $response['code'] = 1;
                    $response['status'] = "success";
                    $response['data'] = $fblogin_data;
                    $response['message'] = 'Login successfully';
                }
            }
            else
            {
                $fname=$lname=$profile_picture="";

                if(isset($postVar['fname']) && $postVar['fname'] != '')
                {
                    $fname = $postVar['fname'];
                    $signup_data['fname'] = $fname;
                }
                if(isset($postVar['lname']) && $postVar['lname'] != '')
                {
                    $lname = $postVar['lname'];
                    $signup_data['lname'] = $lname;
                }
                if(isset($postVar['profile_picture']) && $postVar['profile_picture'] != '')
                {
                    $profile_picture = $postVar['profile_picture'];
                    $signup_data['profile_picture'] = $profile_picture;
                }

                $signup_data['email_id'] = $email_id;
                $signup_data['facebook_id'] = $facebook_id;
                $signup_data['password'] = "";
                $signup_data['phone_number'] = "";
                $signup_data['status'] = 1;
                $signup_data['device_token'] = $device_token;
                $signup_data['device_type'] = $device_type;
                $signup_data['ip_address'] = '';
                $signup_data['created_at'] = date("Y-m-d H:i:s");

                $insert = $this->User_model->insertFbData($signup_data);
                
                if(!empty($insert))
                {
                    $signup_data['id'] = $insert;
                    $emaildata['to'] = $email_id;
                    $emaildata['subject'] = 'Giftcast - Welcome';
                    $emaildata['message'] = "Hello".' '.$fname.''.$lname.",<br><br>Welcome to Giftcast
                    .<br/><br/> Thanks,<br/><b>Giftcast team</b>.";

                    sendmail($emaildata);

                    unset($signup_data['password']);

                    $response['code'] = 1;
                    $response['status'] = "success";
                    $response['data'] = $signup_data;
                    $response['message'] = "Registration successfully done";
                }
            }
            echo json_encode($response);
        }
    }
}
?>

