define({ "api": [
  {
    "type": "post",
    "url": "api/ws_dwolla_verified_reciever",
    "title": "Dwolla Verified Reciever",
    "version": "1.0.0",
    "name": "VerifyReciever",
    "group": "Dwolla_Bank_Account",
    "description": "<p>Verify reciever in Dwolla</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Character",
            "optional": false,
            "field": "email_id",
            "description": "<p>The Email Id.</p>"
          }
        ]
      }
    },
    "filename": "application/controllers/webservices/Gift.php",
    "groupTitle": "Dwolla_Bank_Account"
  },
  {
    "type": "post",
    "url": "api/ws_dwolla_verified_sender",
    "title": "Dwolla Verified Sender",
    "version": "1.0.0",
    "name": "VerifySender",
    "group": "Dwolla_Bank_Account",
    "description": "<p>Verify sender in Dwolla.</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Character",
            "optional": false,
            "field": "email_id",
            "description": "<p>The Email Id.</p>"
          }
        ]
      }
    },
    "filename": "application/controllers/webservices/Gift.php",
    "groupTitle": "Dwolla_Bank_Account"
  },
  {
    "type": "post",
    "url": "api/ws_gift_history",
    "title": "Gift History Sender",
    "version": "1.0.0",
    "name": "GiftHistory",
    "group": "Gifts",
    "description": "<p>Sender can see Gift History.</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>The Gift-ID.</p>"
          }
        ]
      }
    },
    "filename": "application/controllers/webservices/Gift.php",
    "groupTitle": "Gifts"
  },
  {
    "type": "post",
    "url": "api/ws_gift_history_delete",
    "title": "Gift Delete",
    "version": "1.0.0",
    "name": "GiftHistoryDelete",
    "group": "Gifts",
    "description": "<p>Gift History Delete from Sender/Reciever Side</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>The Gift-ID.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "remove_id",
            "description": "<p>The User ID.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "type",
            "description": "<p>Type (E.g Sender,Reciever).</p>"
          }
        ]
      }
    },
    "filename": "application/controllers/webservices/Gift.php",
    "groupTitle": "Gifts"
  },
  {
    "type": "post",
    "url": "api/ws_gift_history_recieved",
    "title": "Gift History Reciever",
    "version": "1.0.0",
    "name": "GiftHistoryReciever",
    "group": "Gifts",
    "description": "<p>Reciever can see Gift History</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>The Gift-ID.</p>"
          }
        ]
      }
    },
    "filename": "application/controllers/webservices/Gift.php",
    "groupTitle": "Gifts"
  },
  {
    "type": "post",
    "url": "api/ws_gift_opened",
    "title": "Gift Opened",
    "version": "1.0.0",
    "name": "GiftOpened",
    "group": "Gifts",
    "description": "<p>Sender gets notification once reciever opens the gifts</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "gift_id",
            "description": "<p>The Gift-ID.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "recipient_id",
            "description": "<p>The Recipient-ID.</p>"
          }
        ]
      }
    },
    "filename": "application/controllers/webservices/Gift.php",
    "groupTitle": "Gifts"
  },
  {
    "type": "post",
    "url": "api/ws_send_gift",
    "title": "Send Gift",
    "version": "1.0.0",
    "name": "SendGift",
    "group": "Gifts",
    "description": "<p>Will send a gift from one user to another.</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "title",
            "description": "<p>The Users-ID.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "from_id",
            "description": "<p>The From id(From).</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "recipient_id",
            "description": "<p>The Sender id(To).</p>"
          },
          {
            "group": "Parameter",
            "type": "Character",
            "optional": false,
            "field": "recipient_name",
            "description": "<p>The Recipient Name.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "amount",
            "description": "<p>Amount.</p>"
          },
          {
            "group": "Parameter",
            "type": "Character",
            "optional": false,
            "field": "payment_type",
            "description": "<p>Payment Type.</p>"
          },
          {
            "group": "Parameter",
            "type": "Character",
            "optional": false,
            "field": "transaction_id",
            "description": "<p>Transaction id.</p>"
          },
          {
            "group": "Parameter",
            "type": "Character",
            "optional": false,
            "field": "transaction_details",
            "description": "<p>Transaction details.</p>"
          },
          {
            "group": "Parameter",
            "type": "DateTime",
            "optional": false,
            "field": "gift_timestamp",
            "description": "<p>Timestamp.</p>"
          }
        ]
      }
    },
    "filename": "application/controllers/webservices/Gift.php",
    "groupTitle": "Gifts"
  },
  {
    "type": "post",
    "url": "api/ws_notification_settings",
    "title": "Notification settings",
    "version": "1.0.0",
    "name": "Notification_Settings",
    "group": "Notifications",
    "description": "<p>Notification settings can be enabled/disabled.</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "user_id",
            "description": "<p>The user id.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "recieve_gift",
            "description": "<p>Recieve gift</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "send_gift",
            "description": "<p>Send gift</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "contacts_joined",
            "description": "<p>Contacts Joined</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "admin_giftcast_settings",
            "description": "<p>Admin Notification</p>"
          }
        ]
      }
    },
    "filename": "application/controllers/webservices/Notification.php",
    "groupTitle": "Notifications"
  },
  {
    "type": "post",
    "url": "api/ws_reportproblem",
    "title": "Report Account",
    "version": "1.0.0",
    "name": "ReportProblem",
    "group": "Report_User",
    "description": "<p>Delete Account of user</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>User id.</p>"
          },
          {
            "group": "Parameter",
            "type": "Character",
            "optional": false,
            "field": "topic",
            "description": "<p>Topic For Reporting.</p>"
          },
          {
            "group": "Parameter",
            "type": "Character",
            "optional": false,
            "field": "title",
            "description": "<p>Title For Reporting.</p>"
          },
          {
            "group": "Parameter",
            "type": "Character",
            "optional": false,
            "field": "complaint",
            "description": "<p>Complaint.</p>"
          }
        ]
      }
    },
    "filename": "application/controllers/webservices/User.php",
    "groupTitle": "Report_User"
  },
  {
    "type": "post",
    "url": "api/ws_deleteuser",
    "title": "Delete Account",
    "version": "1.0.0",
    "name": "DeleteAccount",
    "group": "Users",
    "description": "<p>Delete Account of user</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>User id.</p>"
          },
          {
            "group": "Parameter",
            "type": "Character",
            "optional": false,
            "field": "reason",
            "description": "<p>Reason for deleting.</p>"
          },
          {
            "group": "Parameter",
            "type": "Character",
            "optional": false,
            "field": "password",
            "description": "<p>Password.</p>"
          }
        ]
      }
    },
    "filename": "application/controllers/webservices/User.php",
    "groupTitle": "Users"
  },
  {
    "type": "post",
    "url": "api/ws_fblogin",
    "title": "Facebook Login",
    "version": "1.0.0",
    "name": "FacebookLogin",
    "group": "Users",
    "description": "<p>Facebook Login for a user</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "facebook_id",
            "description": "<p>Facebook Unique id.</p>"
          },
          {
            "group": "Parameter",
            "type": "Character",
            "optional": false,
            "field": "email_id",
            "description": "<p>Email id.</p>"
          }
        ]
      }
    },
    "filename": "application/controllers/webservices/User.php",
    "groupTitle": "Users"
  },
  {
    "type": "post",
    "url": "api/ws_forget_password",
    "title": "Forget Password",
    "version": "1.0.0",
    "name": "ForgetPassword",
    "group": "Users",
    "description": "<p>Forgte Password</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "email_id",
            "description": "<p>Email id.</p>"
          }
        ]
      }
    },
    "filename": "application/controllers/webservices/User.php",
    "groupTitle": "Users"
  },
  {
    "type": "post",
    "url": "api/ws_invite",
    "title": "Invite Friend",
    "version": "1.0.0",
    "name": "InviteAFriend",
    "group": "Users",
    "description": "<p>Invite a friend</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>User id.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "recipient",
            "description": "<p>User id.</p>"
          },
          {
            "group": "Parameter",
            "type": "Character",
            "optional": false,
            "field": "title",
            "description": "<p>Title.</p>"
          },
          {
            "group": "Parameter",
            "type": "Character",
            "optional": false,
            "field": "message",
            "description": "<p>Message.</p>"
          }
        ]
      }
    },
    "filename": "application/controllers/webservices/User.php",
    "groupTitle": "Users"
  },
  {
    "type": "post",
    "url": "api/ws_fetch_user_details",
    "title": "User Details",
    "version": "1.0.0",
    "name": "UserDetails",
    "group": "Users",
    "description": "<p>Login</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>User id.</p>"
          }
        ]
      }
    },
    "filename": "application/controllers/webservices/User.php",
    "groupTitle": "Users"
  },
  {
    "type": "post",
    "url": "api/ws_signin",
    "title": "User Login",
    "version": "1.0.0",
    "name": "UserLogin",
    "group": "Users",
    "description": "<p>Login</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "email_id",
            "description": "<p>Email id.</p>"
          },
          {
            "group": "Parameter",
            "type": "Character",
            "optional": false,
            "field": "password",
            "description": "<p>Password.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "device_token",
            "description": "<p>Device token</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "device_type",
            "description": "<p>Device Type E.g Android, Ios.</p>"
          }
        ]
      }
    },
    "filename": "application/controllers/webservices/User.php",
    "groupTitle": "Users"
  },
  {
    "type": "post",
    "url": "api/ws_signup",
    "title": "User Signup",
    "version": "1.0.0",
    "name": "UserSignup",
    "group": "Users",
    "description": "<p>Signup</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "email_id",
            "description": "<p>Email id.</p>"
          },
          {
            "group": "Parameter",
            "type": "Character",
            "optional": false,
            "field": "fname",
            "description": "<p>First Name.</p>"
          },
          {
            "group": "Parameter",
            "type": "Character",
            "optional": true,
            "field": "lname",
            "description": "<p>Last Name.</p>"
          },
          {
            "group": "Parameter",
            "type": "Character",
            "optional": false,
            "field": "password",
            "description": "<p>Password.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "phone_number",
            "description": "<p>Phone Number.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "device_token",
            "description": "<p>Device token</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "device_type",
            "description": "<p>Device Type E.g Android, Ios.</p>"
          }
        ]
      }
    },
    "filename": "application/controllers/webservices/User.php",
    "groupTitle": "Users"
  }
] });
