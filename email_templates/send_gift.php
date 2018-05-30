
<!DOCTYPE html>
<html lang="en-US" prefix="og: http://ogp.me/ns#">
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=650, initial-scale=1">
<title>Preview - Bootstrap Themes</title>
<style type="text/css">
/**{box-sizing:border-box;margin:0; padding: 0;}*/
body{font-family: arial, sans-sarif; font-size: 14px; margin: 0; padding: 0; background-color: #ffffff; color: #808080; line-height: 20px;}
.topbar{background-color:#6ba2b9; padding: 10px 0; color: #fff; text-align: center;}
.container{max-width: 1000px; padding: 0 15px; margin: 0 auto;}
.header{background: url(headerbg.jpg) repeat;padding: 28px 0; text-align: center;}
.footer{background-color: #e6e6e6; text-align: center; padding: 40px 0 20px;}
.footer p{margin-bottom: 20px;}
.footer a{color: #808080; text-decoration: none; margin: 0 15px; display: inline-block;}
.footer .divider{height: 12px; background: #808080; width: 1px; display: inline-block; vertical-align: middle;}
.socialmedia{ text-align: center; margin: 40px 0 40px;}
.socialmedia a{display: inline-block; margin: 0 15px; vertical-align: middle;}
.title{font-size: 24px; text-align: center; line-height: 40px; margin: 40px 0; line-height: normal;}
.text-center{text-align: center;}
.graytable{background-color: #f2f2f2; padding:25px 40px; max-width: 600px; margin: 0 auto;}
.graytable td{line-height: 26px; color: #666666; font-size: 16px;}
.detailtable{background-color: #6ba2b9; color: #fff; padding:20px 40px; margin-bottom: 30px; max-width: 600px; margin: 0 auto;}
.detailtable td{padding: 15px 0;}
.bordertop td{border-top:1px solid #fff;}
.title br{display: none;}
.hidedesktop{display: none;}
.hidemobile{display: block;}
.title span.name{color:#ff00ff;}
@media(max-width: 767px){
  body{font-size: 13px;}
  .header img{max-height:50px;}
  .hidedesktop{display: block;}.hidemobile{display: none;}
  .header{ padding: 15px 0;}
  .title{font-size: 24px; margin: 30px 0; line-height: 32px;}
  .title br{display: block;}
  .graytable{padding: 15px;}
  .graytable td{font-size: 13px;}
  .detailtable{padding: 15px; margin-bottom: 30px;}
  .footer{padding: 30px 0 10px;}
  .footer a{margin: 0 5px;}
}
</style>
</head>
<body>
<div class="topbar">
  <div class="container">
    Hello SENDERNAME
  </div>
</div>

<div class="header">
  <div class="container">
    <a href="http://giftcast.me/"><img src="http://216.55.169.45/~giftcast/master/email_templates/logo.png" alt=="" /></a>
  </div>
</div>

<div class="content container">
  <div class="title">You gifted $AMOUNT_TITLE to<br> RECIPIENTNAME</div>
  <div class="graytable">
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <tbody>
        <tr>
          <td align="left">Paid with:</td>
          <td align="right">BANKNAME</td>
        </tr>
        <tr>
          <td align="left">Transaction amount:</td>
          <td align="right">$TOTAL_AMOUNT USD</td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="detailtable">
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <tbody>
        <tr>
          <td align="center" colspan="2" style="color: #FFFFFF">Here are the details:</td>
        </tr>
        <tr class="bordertop">
          <td align="left" style="white-space: nowrap; color: #FFFFFF">Transaction ID: TRANSACTIONID</td>
          <td align="right" style="white-space: nowrap; color: #FFFFFF">DATE: CREATEDDATE</td>
        </tr>
        <tr class="bordertop">
          <td align="left" style="color: #FFFFFF">You Gifted:<br>Fees:</td>
          <td align="right" style="white-space: nowrap; color: #FFFFFF">$AMOUNT_TITLE USD<br>$TAX_PRICE USD</td>
        </tr>
        <tr class="bordertop">
          <td align="left" style="color: #FFFFFF">RECIPIENTNAME will receive:</td>
          <td align="right" style="white-space: nowrap; color: #FFFFFF"">$AMOUNT_TITLE USD</td>
        </tr>
        <tr class="bordertop">
          <td align="center" style="color: #FFFFFF" colspan="2">RECIPIENTNAME will receive a message saying you're sent this GiftCast and instructions explaining how to accept it.</td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="text-center">
    <a href="http://giftcast.me/"><img src="http://216.55.169.45/~giftcast/master/email_templates/logo-small.png" /></a>
  </div>
  <div class="socialmedia">
    <a href="https://www.facebook.com/GiftCast-1749903395028756/"><img src="http://216.55.169.45/~giftcast/master/email_templates/facebook.png" /></a>
    <a href="https://twitter.com/giftcast1"><img src="http://216.55.169.45/~giftcast/master/email_templates/twitter.png" /></a>
    <a href="https://www.instagram.com/giftcast/"><img src="http://216.55.169.45/~giftcast/master/email_templates/instagram.png" /></a>
  </div>
</div>
<div class="footer">
  <div class="container">
    <p><a href="mailto:customercare@giftcast.me">Support</a><span class="divider"></span><a href="http://giftcast.me/terms-and-conditions.html">Terms and Conditions</a><span class="divider"></span><a href="http://giftcast.me/privacy-policy.html">Privacy Policy</a></p>
    <p>If you did not send this gift. or if you have questions, call GiftCast Customer Care at 1-8xx-xxx-xxxx or email <a href="mailto:customercare@giftcast.me" style="color: #000; margin: 0px">customercare@giftcast.me</a>.</p>
    <p>Please do not reply to this automated email.</p>
    <p>Copyright &copy; 2018 GiftCast LLC. All rights reserved.</p>
  </div>
</div>
</body>
</html>
