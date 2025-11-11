<!DOCTYPE html>
<html lang="en">
<head>
  <title>Referral Program</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>


<main id="" class="main">
<style>
a.wcol{
   color: #fff !important;
    font-weight: 700;
    border-right: 1px solid white;
    padding: 0 12px;
}
section.section-content.padding-y.bg.mt-2.mb-2.imp {
    background: #ee6a29;
    margin: 0 !important;
    padding: 10px;
}
 .dashboard {
            background: #d2edd5; 
            margin: 15px auto;
            line-height: 10px;
            color: #333;
            border-radius: 4px;
            /*padding: 30px;*/
            max-width: 400px;
            border: #c8e0cb 1px solid;
            text-align: center;
        }

        a.logout-button {
            color: #09f;
        }
        .profile-photo {
            width: 100px;
            border-radius: 50%; 
        }
.hide-desktop {
    display: none;
}
 .bg-blue{
        background-color: #007bb5;
        border-radius: 3px;
    }
    .bg-blue h4{
        font-size: 20px;
        font-weight: 400;
        margin-bottom: 0px!important;
    }

   .reference-desktop-img {
    background-image: url(img/reference-banner.jpg);
    background-size: cover;
    background-repeat: no-repeat;
    background-position: 95%;
    min-height: 100px;
    max-width: 100%;
        width: 100%;
    position: relative;
    text-align: center;
    margin: 0;
    color: #fff;
}

    .reference-mobile-img {
    background-image: url(img/reference-mobile-banner.jpg);
    background-size: cover;
    background-repeat: no-repeat;
    min-height: 301px;
    background-position: 26%;
        width: 100%;
    position: relative;
    text-align: center;
    margin: 0;
    color: #fff;
}

    .banner-data-reference{
        margin-top: 2rem;
        margin-bottom: 2rem;
        background: #755b4e0f;
        /*padding: 4rem;*/
        box-shadow: 9px 17px 40px -14px rgba(123,97,81,0.61);
    -webkit-box-shadow: 9px 17px 40px -14px rgba(123,97,81,0.61);
    -moz-box-shadow: 9px 17px 40px -14px rgba(123,97,81,0.61);
    }

    .banner-data-reference h3{
        font-size: 36px;
    font-weight: 700;
    color: #fff!important;
    text-align: center;
    text-transform: uppercase;
    margin: 20px 0;

    }
    .banner-data-reference p{
        padding: 0.6rem;
    font-size: 22px!important;
    color: #fff!important;
    font-weight: 500;
    text-align: center;
    margin-bottom: 2px!important;
    background: #654839;
    }
    .card{
        border-radius:0px!important;
        height:100%;
        box-shadow: 9px 17px 40px -14px rgb(123 97 81 / 61%);
    -webkit-box-shadow: 9px 17px 40px -14px rgb(123 97 81 / 61%);
    -moz-box-shadow: 9px 17px 40px -14px rgba(123,97,81,0.61);
    margin-bottom:0px!important;
    }
    
     @media(max-width: 1024px){
         
         .ref-pad{
        padding: 0 2% 0 2%!important;
    }
     }

    @media(max-width: 767px){
      .banner-data-reference{
         background:#755b4ec4;
         margin-bottom:0px;
         margin-top:0px;
         height: 301px;
      }

      .ref-content-mob{
          padding:0px!important;
      }
      .banner-data-reference p{
          background: #6548392e;
      }
      .ref-img{
          display:none;
      }

    }
    .ref-pad{
        padding: 0 9% 0 9%;
    }
    input.btn.btn-primary.btn-block {
    background: #007bb5;
}
    p.success {
    /*background-color: #12CC1A;*/
    /*border: #0FA015 1px solid;*/
    /*padding: 5px 10px;*/
    /*color: #FFFFFF;*/
    /*border-radius: 4px;*/
}
</style>

<!--<section class="wide-net hide-mobile">-->
<!--    <div class="container-fuild">-->
<!--       <div class="col-md-12">-->
<!--          <div class="row">-->
<!--             <div class="hero_single reference-desktop-img">-->
<!--                 <div class="container">-->
<!--                     <div class="row">-->
<!--                         <div class="col-md-12 col-lg-8 my-auto">-->
<!--                            <div class="banner-data-reference">-->
                                <!--<p>Leading Property Advisor</p>-->
<!--                                <h3 class="text-dark">Referral Program</h3>-->
<!--                          </div>-->
<!--                         </div>-->
<!--                     </div>-->
<!--                 </div>-->
<!--          </div>-->
<!--       </div>-->
<!--    </div>-->
<!--    </div>-->
<!-- </section>-->

<!-- <section class="wide-net hide-desktop">-->
<!--   <div class="container-fuild">-->
<!--      <div class="col-md-12">-->
<!--         <div class="row">-->
<!--            <div class="hero_single reference-mobile-img">-->
<!--               <div class="container-fluid">-->
<!--                     <div class="row">-->
<!--                         <div class="col-md-8 my-auto ref-content-mob">-->
<!--                            <div class="banner-data-reference">-->
                                <!--<p>Leading Property Advisor</p>-->
<!--                                <h3 class="text-dark">Referral Program</h3>-->
<!--                          </div>-->
<!--                         </div>-->
<!--                     </div>-->
<!--                 </div>-->
<!--               </div>-->
<!--            </div>-->
<!--         </div>-->
<!--      </div>-->
<!--   </div>-->
<!--</section>-->

<!--<div class="container">-->
<!--    <div class="row">-->
<!--        <div class="col-md-12">-->
            
<!--            <table border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">-->
<!--                                    <tbody>-->
                                      
                                     
<!--                                      <tr>-->
<!--                                        <td style="vertical-align: top; height: 20px; font-size: 20px; line-height: 20px;" valign="top">&nbsp;</td>-->
<!--                                      </tr>-->
<!--                                      <tr>-->
<!--                                        <td class="pc-fb-font" style="vertical-align: top; text-align: center; font-family: 'Fira Sans', Helvetica, Arial, sans-serif; font-size: 24px; font-weight: 700; line-height: 1.42; letter-spacing: -0.4px; color: #151515; padding: 0 20px;" valign="top" align="left">How it works?</td>-->
<!--                                      </tr>-->
<!--                                      <tr>-->
<!--                                        <td style="vertical-align: top;" valign="top">-->
<!--                                          <table border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">-->
<!--                                            <tbody>-->
<!--                                              <tr>-->
<!--                                                <td style="vertical-align: top; height: 20px; font-size: 20px; line-height: 20px;" valign="top">&nbsp;</td>-->
<!--                                              </tr>-->
<!--                                            </tbody>-->
<!--                                          </table>-->
<!--                                        </td>-->
<!--                                      </tr>-->
<!--                                      <tr>-->
<!--                                        <td>-->
<!--                                          <table border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">-->
<!--                                            <tbody>-->
<!--                                              <tr>-->
<!--                                                <td class="pc-features-row-s1" style="vertical-align: top; font-size: 0; text-align: center;" valign="top" align="center">-->
                                                 
<!--                                                  <a href="https://www.keystonerealestateadvisory.com/" target="_blank" style="text-decoration:none; color:#333">-->
<!--                                                   <div class="pc-features-row-col" style="display: inline-block; margin: 4px; width: 31%; vertical-align: top;">-->
<!--                                                    <table border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">-->
<!--                                                      <tbody>-->
<!--                                                        <tr>-->
<!--                                                          <td style="vertical-align: top; padding: 20px; vertical-align: top;    padding: 20px; height:130px;    box-shadow: rgb(149 157 165 / 14%) 0px 8px 24px;-->
<!--    background: #fff;     border-radius: 10px;      margin: 8px;    padding: 10px;    text-align: center;" valign="top">-->
<!--                                                            <table border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">-->
<!--                                                              <tbody>-->
<!--                                                                <tr>-->
<!--                                                                  <td style="vertical-align: top; text-align: center;" valign="top" align="center"> <img src="http://fanm.co.in/emailer/img/users.png" width="" height="85" alt="" style="border: 0; line-height: 100%; outline: 0; -ms-interpolation-mode: bicubic; display: block; font-family: 'Fira Sans', Helvetica, Arial, sans-serif; font-size: 14px; color: #1B1B1B; text-align: center; Margin: 0 auto;"> </td>-->
<!--                                                                </tr>-->
<!--                                                                <tr>-->
<!--                                                                  <td style="vertical-align: top;" valign="top">-->
<!--                                                                    <table border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">-->
<!--                                                                      <tbody>-->
<!--                                                                        <tr>-->
<!--                                                                          <td style="vertical-align: top; height: 13px; font-size: 13px; line-height: 13px;" valign="top">&nbsp;</td>-->
<!--                                                                        </tr>-->
<!--                                                                      </tbody>-->
<!--                                                                    </table>-->
<!--                                                                  </td>-->
<!--                                                                </tr>-->
<!--                                                                 <tr><td class="pc-fb-font" style="vertical-align: top;font-family: 'Fira Sans', Helvetica, Arial, sans-serif;   font-size: 15px;    font-weight: 500;    line-height: 1.33;    letter-spacing: -0.2px;    color: #333;    text-align: center;    border-radius: 0px;" valign="top" align="center">Your Refer</td></tr>-->
                                                                  
                                                                  
<!--                                                                </tr>-->
<!--                                                                <tr>-->
<!--                                                                  <td style="vertical-align: top;" valign="top">-->
<!--                                                                    <table border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">-->
<!--                                                                      <tbody>-->
<!--                                                                        <tr>-->
<!--                                                                          <td style="vertical-align: top; height: 7px; font-size: 7px; line-height: 7px;" valign="top">&nbsp;</td>-->
<!--                                                                        </tr>-->
<!--                                                                      </tbody>-->
<!--                                                                    </table>-->
<!--                                                                  </td>-->
<!--                                                                </tr>-->
<!--                                                              </tbody>-->
<!--                                                            </table>-->
<!--                                                          </td>-->
<!--                                                        </tr>-->
<!--                                                      </tbody>-->
<!--                                                    </table>-->
<!--                                                  </div>-->
<!--                                                  </a>-->
<!--                                                  <a href="https://www.keystonerealestateadvisory.com/" target="_blank" style="text-decoration:none; color:#333">-->
<!--                                                   <div class="pc-features-row-col" style="display: inline-block; margin: 4px; width: 31%;  vertical-align: top;">-->
<!--                                                    <table border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">-->
<!--                                                      <tbody>-->
<!--                                                        <tr>-->
<!--                                                          <td style="vertical-align: top; padding: 20px; vertical-align: top;    padding: 20px; height:130px;     box-shadow: rgb(149 157 165 / 14%) 0px 8px 24px;-->
<!--    background: #fff;    border-radius: 10px;   margin: 8px;    padding: 10px;    text-align: center;" valign="top">-->
<!--                                                            <table border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">-->
<!--                                                              <tbody>-->
<!--                                                                <tr>-->
<!--                                                                  <td style="vertical-align: top; text-align: center;" valign="top" align="center"> <img src="http://fanm.co.in/emailer/img/buy-button.png" width="" height="85" alt="" style="border: 0; line-height: 100%; outline: 0; -ms-interpolation-mode: bicubic; display: block; font-family: 'Fira Sans', Helvetica, Arial, sans-serif; font-size: 14px; color: #1B1B1B; text-align: center; Margin: 0 auto;"> </td>-->
<!--                                                                </tr>-->
<!--                                                                <tr>-->
<!--                                                                  <td style="vertical-align: top;" valign="top">-->
<!--                                                                    <table border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">-->
<!--                                                                      <tbody>-->
<!--                                                                        <tr>-->
<!--                                                                          <td style="vertical-align: top; height: 13px; font-size: 13px; line-height: 13px;" valign="top">&nbsp;</td>-->
<!--                                                                        </tr>-->
<!--                                                                      </tbody>-->
<!--                                                                    </table>-->
<!--                                                                  </td>-->
<!--                                                                </tr>-->
                                                                 
<!--                                                               <tr><td class="pc-fb-font" style="vertical-align: top;font-family: 'Fira Sans', Helvetica, Arial, sans-serif;   font-size: 15px;    font-weight: 500;    line-height: 1.33;    letter-spacing: -0.2px;    color: #333;    text-align: center;       border-radius: 0px;" valign="top" align="center">Customer Buys</td></tr>-->
                                                                  
<!--                                                                </tr>-->
<!--                                                                <tr>-->
<!--                                                                  <td style="vertical-align: top;" valign="top">-->
<!--                                                                    <table border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">-->
<!--                                                                      <tbody>-->
<!--                                                                        <tr>-->
<!--                                                                          <td style="vertical-align: top; height: 7px; font-size: 7px; line-height: 7px;" valign="top">&nbsp;</td>-->
<!--                                                                        </tr>-->
<!--                                                                      </tbody>-->
<!--                                                                    </table>-->
<!--                                                                  </td>-->
<!--                                                                </tr>-->
<!--                                                              </tbody>-->
<!--                                                            </table>-->
<!--                                                          </td>-->
<!--                                                        </tr>-->
<!--                                                      </tbody>-->
<!--                                                    </table>-->
<!--                                                  </div>-->
<!--                                                  </a>-->
<!--                                                  <a href="https://www.keystonerealestateadvisory.com/" target="_blank" style="text-decoration:none; color:#333">-->
<!--                                                   <div class="pc-features-row-col" style="display: inline-block; margin: 4px; width: 31%; vertical-align: top;">-->
<!--                                                    <table border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">-->
<!--                                                      <tbody>-->
<!--                                                        <tr>-->
<!--                                                          <td style="vertical-align: top; padding: 20px; vertical-align: top;    padding: 20px; height:130px;    box-shadow: rgb(149 157 165 / 14%) 0px 8px 24px;-->
<!--                                                                background: #fff;       border-radius: 10px;      margin: 8px;    padding: 10px;    text-align: center;" valign="top">-->
<!--                                                            <table border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">-->
<!--                                                              <tbody>-->
<!--                                                                <tr>-->
<!--                                                                  <td style="vertical-align: top; text-align: center;" valign="top" align="center"> <img src="http://fanm.co.in/emailer/img/gift.png" width="" height="85" alt="" style="border: 0; line-height: 100%; outline: 0; -ms-interpolation-mode: bicubic; display: block; font-family: 'Fira Sans', Helvetica, Arial, sans-serif; font-size: 14px; color: #1B1B1B; text-align: center; Margin: 0 auto;"> </td>-->
<!--                                                                </tr>-->
<!--                                                                <tr>-->
<!--                                                                  <td style="vertical-align: top;" valign="top">-->
<!--                                                                    <table border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">-->
<!--                                                                      <tbody>-->
<!--                                                                        <tr>-->
<!--                                                                          <td style="vertical-align: top; height: 13px; font-size: 13px; line-height: 13px;" valign="top">&nbsp;</td>-->
<!--                                                                        </tr>-->
<!--                                                                      </tbody>-->
<!--                                                                    </table>-->
<!--                                                                  </td>-->
<!--                                                                </tr>-->
                                                                
<!--                                                               <tr><td class="pc-fb-font" style="vertical-align: top;font-family: 'Fira Sans', Helvetica, Arial, sans-serif;   font-size: 15px;   font-weight: 500;       line-height: 1.33;    letter-spacing: -0.2px;    color: #333;    text-align: center;      border-radius: 0px;" valign="top" align="center">You Get Rewarded</td></tr>-->
                                                                  
<!--                                                                </tr>-->
<!--                                                                <tr>-->
<!--                                                                  <td style="vertical-align: top;" valign="top">-->
<!--                                                                    <table border="0" cellpadding="0" cellspacing="0" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">-->
<!--                                                                      <tbody>-->
<!--                                                                        <tr>-->
<!--                                                                          <td style="vertical-align: top; height: 7px; font-size: 7px; line-height: 7px;" valign="top">&nbsp;</td>-->
<!--                                                                        </tr>-->
<!--                                                                      </tbody>-->
<!--                                                                    </table>-->
<!--                                                                  </td>-->
<!--                                                                </tr>-->
<!--                                                              </tbody>-->
<!--                                                            </table>-->
<!--                                                          </td>-->
<!--                                                        </tr>-->
<!--                                                      </tbody>-->
<!--                                                    </table>-->
<!--                                                  </div>-->
<!--                                                  </a>-->
                                                 
<!--                                                </td>-->
<!--                                              </tr>-->
<!--                                            </tbody>-->
<!--                                          </table>-->
<!--                                        </td>-->
<!--                                      </tr>-->
<!--                                    </tbody>-->
<!--                                  </table>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<section class="section-content padding-y bg mt-5 mb-5">
    <div class="container-fluid">
            <div class="row ref-pad">
                <!--<aside class="col-lg-3 col-md-6 p-0">-->

                <!--           <img  class="p-0 img-fluid ref-img " src="img/refer-banner-standing-1.jpg" style="height:100%; width:100%">-->

                <!--</aside>-->
                  <aside class="col-lg-12 col-md-6 p-0">
                    <div class="card border-none">
                    
                        <article class="card-body">
                             <!--<div id="mail-status"><p class="success"></p></div>-->
                            <div class="bg-blue p-2 mt-4 mb-4">
                                <?php 
              if($sname == 'homeloan'){
                  $formtype = "Home Loan Assistance";
              }elseif($sname == 'collection'){
                   $formtype = "Collection";
              }elseif($sname == 'property'){
                   $formtype = "Property Management";
              }elseif($sname == 'document'){
                   $formtype = "Documentation";
              }else{
                   $formtype = "Referrals";
              }
              ?>
                                    <h4 class="card-title text-left text-white"> <?php echo $formtype ?></h4>
                                </div> <hr>


                                <div class="form-row">
                                    <div class="col-md-6 form-group">
                                        <label>Name <span style="color:red;">*</span><span style="color:red;" id="name_inquiry-info" class="info"></span></label>
                                        <input type="text" name="name_inquiry" id="name_inquiry" class="form-control" placeholder="Name" value="<?php echo $client->client_name; ?>">
                                        <input type="hidden" name="userid" id="userid" value="<?php echo $client->u_id; ?>" class="form-control" placeholder="Name">
                                        <input type="hidden" name="usrname" id="username" value="<?php echo $client->u_name; ?>" class="form-control" placeholder="Name">
                                        <input type="hidden" name="usermobile" id="usermobile" value="<?php echo $client->u_name; ?>" class="form-control" placeholder="Name">
                                      
                                        <input type="hidden" name="form_type" id="form_type" value="<?php echo $sname; ?>" class="form-control" placeholder="Name">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Email Id <span style="color:red;">*</span><span style="color:red;" id="email_inquiry-info" class="info"></span></label>
                                        <input type="email" name="email_inquiry" id="email_inquiry" class="form-control" placeholder="Email Id" >
                                    </div>
                                </div>
                                <div class="form-row ">
                                    <div class="col-md-6 form-group">
                                        <label>Mobile No <span style="color:red;">*</span><span style="color:red;" id="mobileno-info" class="info"></span></label>
                                        <input type="text" name="mobileno" id="mobileno" class="form-control" placeholder="Mobile Number">
                                    </div>
                                    <?php if(($sname == 'collection')){ ?>
                                    <div class="col-md-6 form-group">
                                        <label>Address <span style="color:red;" id="address-info" class="info"></span></label>
                                        <input type="text" name="address" id="address" class="form-control" placeholder="Address">
                                    </div>
                                    <?php } ?>
                                    
                                    <?php if($sname == 'homeloan'){ ?>
                                    <div class="col-md-6 form-group">
                                        <label>Loan Amount (Rs.) <span style="color:red;" id="loanamount-info" class="info"></span></label>
                                        <input type="text" name="loanamount" id="loanamount" class="form-control" placeholder="Loan Amount">
                                    </div>
                                    <?php } ?>
                                     <?php if($sname == 'property'){ ?>
                                    <div class="col-md-6 form-group">
                                        <label>Assistance needed for <span style="color:red;" id="assistance-info" class="info"></span></label>
                                        <select name="assistance" id="assistance"  class="demoInputBox form-control">
                                            <option value = "0">Rent</option>    
                                            <option value = "1">Resale</option> 
                                        </select>
                                        <!--<input type="text" name="assistance" id="assistance" class="form-control" placeholder="Assistance needed for">-->
                                    </div>
                                    <?php }else{ ?>
                                    <input type="hidden" name="assistance" id="assistance" class="form-control" value="0" placeholder="Assistance needed for">
                                    <?php } ?>
                                    
                                </div>
                                <?php if(($sname == 'collection') || ($sname == 'document')|| ($sname == 'property')){ ?>
                                <div class="form-row border-bottom">
                                    <div class="col-md-6 form-group">
                                        <label><?php if($sname == 'collection'){ ?>Collection Requirements in detail <?php }elseif($sname == 'document'){ ?> Documentation query in detail <?php }else{ ?> Detailed Remarks if any <?php } ?> <span style="color:red;" id="remarks-info" class="info"></span></label>
                                        <!--<input type="text" name="remarks" id="remarks" class="form-control" placeholder="Collection Requirements in detail">-->
                                        <textarea id="remarks" name="remarks" rows="4" cols="50" placeholder="<?php if($sname == 'collection'){ ?>Collection Requirements in detail <?php }elseif($sname == 'document'){ ?> Documentation query in detail <?php }else{ ?> Detailed Remarks if any <?php } ?>" class="form-control"></textarea>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php if($sname == 'homeloan'){ ?>
                                <div class="form-row border-bottom">
                                    <div class="col-md-6 form-group">
                                        <label>Preferred Bank <span style="color:red;" id="preferredbank-info" class="info"></span></label>
                                        <input type="text" name="preferredbank" id="preferredbank" class="form-control" placeholder="Preferred Bank">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Detailed Remarks if any <span style="color:red;" id="remarks-info" class="info"></span></label>
                                        <textarea id="remarks" name="remarks" rows="4" cols="50" placeholder="Detailed remarks" class="form-control"></textarea>
                                        <!--<input type="text" name="remarks" id="remarks" class="form-control" placeholder="Detailed remarks">-->
                                    </div>
                                </div>
                                <?php } ?>
                          
                                <div class="form-row">
                                    <div class="col-md-4 form-group mt-3">
<input type="button" name="submit" class="btn btn-primary btn-block" value="Submit" onclick="sendContact();" >

<hr><small>Condition Apply<sup style="color:#FF0000;">*</sup></small>

                                    </div> <!-- form-group// -->
                                    
                                </div>
                                 <div id="mail-status"><p class="success"></p></div>
                       </article>
                      
                   </div>
               </aside>
           </div>
       </div>
   </section>
<script language="JavaScript" type="text/JavaScript">

        function sendContact() {
        //console.log("ff");
            var valid;	
            valid = validateContact();
            if(valid) {
                jQuery.ajax({
                    url: "https://hbserviceportal.com/sthankyou",
                    data:'name_inquiry='+$("#name_inquiry").val()+'&email_inquiry='+
                    $("#email_inquiry").val()+'&userid='+
                    $("#userid").val()+'&mobileno='+
                    $("#mobileno").val()+'&ip='+
                    $("#ip").val()+'&username='+
                    $("#username").val()+'&usermobile='+
                    $("#usermobile").val()+'&loanamount='+
                    $("#loanamount").val()+'&preferredbank='+
                    $("#preferredbank").val()+'&remarks='+
                    $("#remarks").val()+'&form_type='+
                    $("#form_type").val()+'&address='+
                    $("#address").val()+'&assistance='+
                    $("#assistance").val(),
                    type: "POST",
                    headers: {
                        'X-CSRF-Token': '{{ csrf_token() }}',
                    },
                    success:function(data){
                       //alert(data);
                        $("#mail-status").html(data);
                    },
                    error:function (){}
                });
            }
        }
        function validateContact() {
            //alert("abcd"); 
            var valid = true;	
            $(".demoInputBox").css('background-color','');
            $(".info").html('');
            if(!$("#name_inquiry").val()) {
                $("#name_inquiry-info").html("(Required)");
                $("#name_inquiry").css('background-color','#FFFFDF');
                valid = false;
            }
            if(!$("#email_inquiry").val()) {
                $("#email_inquiry-info").html("(Required)");
                $("#email_inquiry").css('background-color','#FFFFDF');
                valid = false;
            }
            if(!$("#email_inquiry").val().match(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/)) {
                $("#email_inquiry-info").html("(invalid)");
                $("#email_inquiry").css('background-color','#FFFFDF');
                valid = false;
            }
            if(!$("#mobileno").val()) {
                $("#mobileno-info").html("(Required)");
                $("#mobileno").css('background-color','#FFFFDF');
                valid = false;
            }
            
            return valid;
        }
		
</script>

            </div>
        </div>
    </div>
</div>
</main>

</body>
</html>
