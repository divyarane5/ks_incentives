
<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <!-- <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"> -->
    <title>Simple Transactional Email</title>
    <style>
      /* -------------------------------------
          GLOBAL RESETS
      ------------------------------------- */
      
      /*All the styling goes here*/
      
      img {
        border: none;
        -ms-interpolation-mode: bicubic;
        max-width: 100%; 
      }

      body {
        background-color: #f6f6f6;
        font-family: sans-serif;
        -webkit-font-smoothing: antialiased;
        font-size: 14px;
        line-height: 1.4;
        margin: 0;
        padding: 0;
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%; 
      }

      table {
        border-collapse: separate;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        width: 100%; }
        table td {
          font-family: sans-serif;
          font-size: 14px;
          vertical-align: top; 
      }

      /* -------------------------------------
          BODY & CONTAINER
      ------------------------------------- */

      .body {
        background-color: #f6f6f6;
        width: 100%; 
      }

      /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
      .container {
        display: block;
        margin: 0 auto !important;
        background: #fff;
        /* makes it centered */
       
       
      }

      /* This should also be a block element, so that it will fill 100% of the .container */
      .content {
        box-sizing: border-box;
        display: block;
        margin: 0 auto;
        max-width: 100%;
   
      }

      /* -------------------------------------
          HEADER, FOOTER, MAIN
      ------------------------------------- */
      .main {
        /* background: #ffffff; */
        border-radius: 3px;
        width: 100%; 
      }

      .wrapper {
        box-sizing: border-box;
        padding: 20px; 
      }

      .content-block {
        padding-bottom: 10px;
        padding-top: 10px;
      }

      .footer {
        clear: both;
        margin-top: 10px;
        text-align: center;
        width: 100%; 
      }
        .footer td,
        .footer p,
        .footer span,
        .footer a {
          color: #999999;
          font-size: 12px;
          text-align: center; 
      }

      /* -------------------------------------
          TYPOGRAPHY
      ------------------------------------- */
      h1,
      h2,
      h3,
      h4 {
        color: #000000;
        font-family: sans-serif;
        font-weight: 400;
        line-height: 1.4;
        margin: 0;
        margin-bottom: 30px; 
      }

      h1 {
        font-size: 35px;
        font-weight: 300;
        text-align: center;
        text-transform: capitalize; 
      }

      p,
      ul,
      ol {
        font-family: sans-serif;
        font-size: 14px;
        font-weight: normal;
        margin: 0;
        margin-bottom: 15px; 
      }
        p li,
        ul li,
        ol li {
          list-style-position: inside;
          margin-left: 5px; 
      }

      a {
        color: #3498db;
        text-decoration: underline; 
      }

      /* -------------------------------------
          BUTTONS
      ------------------------------------- */
      .btn {
        box-sizing: border-box;
        width: 100%; }
        .btn > tbody > tr > td {
          padding-bottom: 15px; }
        .btn table {
          width: auto; 
      }
        .btn table td {
          background-color: #ffffff;
          border-radius: 5px;
          text-align: center; 
      }
        .btn a {
          background-color: #ffffff;
          border: solid 1px #3498db;
          border-radius: 5px;
          box-sizing: border-box;
          color: #3498db;
          cursor: pointer;
          display: inline-block;
          font-size: 14px;
          font-weight: bold;
          margin: 0;
          padding: 12px 25px;
          text-decoration: none;
          text-transform: capitalize; 
      }

      .btn-primary table td {
        background-color: #3498db; 
      }

      .btn-primary a {
        background-color: #3498db;
        border-color: #3498db;
        color: #ffffff; 
      }

      /* -------------------------------------
          OTHER STYLES THAT MIGHT BE USEFUL
      ------------------------------------- */
      .last {
        margin-bottom: 0; 
      }

      .first {
        margin-top: 0; 
      }

      .align-center {
        text-align: center; 
      }

      .align-right {
        text-align: right; 
      }

      .align-left {
        text-align: left; 
      }

      .clear {
        clear: both; 
      }

      .mt0 {
        margin-top: 0; 
      }

      .mb0 {
        margin-bottom: 0; 
      }

      .preheader {
        color: transparent;
        display: none;
        height: 0;
        max-height: 0;
        max-width: 0;
        opacity: 0;
        overflow: hidden;
        mso-hide: all;
        visibility: hidden;
        width: 0; 
      }

      .powered-by a {
        text-decoration: none; 
      }

      hr {
        border: 0;
        border-bottom: 1px solid #f6f6f6;
        margin: 20px 0; 
      }

      /* -------------------------------------
          RESPONSIVE AND MOBILE FRIENDLY STYLES
      ------------------------------------- */
      @media only screen and (max-width: 620px) {
        table.body h1 {
          font-size: 28px !important;
          margin-bottom: 10px !important; 
        }
        table.body p,
        table.body ul,
        table.body ol,
        table.body td,
        table.body span,
        table.body a {
          font-size: 16px !important; 
        }
        table.body .wrapper,
        table.body .article {
          padding: 10px !important; 
        }
        table.body .content {
          padding: 0 !important; 
        }
        table.body .container {
          padding: 0 !important;
          width: 100% !important; 
        }
        table.body .main {
          border-left-width: 0 !important;
          border-radius: 0 !important;
          border-right-width: 0 !important; 
        }
        table.body .btn table {
          width: 100% !important; 
        }
        table.body .btn a {
          width: 100% !important; 
        }
        table.body .img-responsive {
          height: auto !important;
          max-width: 100% !important;
          width: auto !important; 
        }
      }

      /* -------------------------------------
          PRESERVE THESE STYLES IN THE HEAD
      ------------------------------------- */
      @media all {
        .ExternalClass {
          width: 100%; 
        }
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
          line-height: 100%; 
        }
        .apple-link a {
          color: inherit !important;
          font-family: inherit !important;
          font-size: inherit !important;
          font-weight: inherit !important;
          line-height: inherit !important;
          text-decoration: none !important; 
        }
        #MessageViewBody a {
          color: inherit;
          text-decoration: none;
          font-size: inherit;
          font-family: inherit;
          font-weight: inherit;
          line-height: inherit;
        }
        .btn-primary table td:hover {
          background-color: #34495e !important; 
        }
        .btn-primary a:hover {
          background-color: #34495e !important;
          border-color: #34495e !important; 
        } 
      }

    </style>
  </head>
  <body>
    <span class="preheader">This is preheader text. Some clients will show this text as a preview.</span>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td>&nbsp;</td>
        <td class="container">
          <div class="content">

            <!-- START CENTERED WHITE CONTAINER -->
            <table role="presentation" class="main">

              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class="">
                  <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td>
                        <p>Dear {{$booking->developer_id}},</p>
                        <p>Greetings of the day from Keystone Real Esate Advisory Pvt Ltd.</p>
                        <p>We are excited to inform you that we have successfully closed a booking with {{$booking->developer_name}} with the below-mentioned details :</p>
                        <table class="w3-table-all w3-centered" style="border: 1px solid #ccc;     border-collapse: collapse;">
                            <tr style="border-bottom: 1px solid #ddd;">
                            <th style="padding: 8px 8px;">Project Name</th>
                            <th style="padding: 8px 8px;">Client Name</th>
                            <th style="padding: 8px 8px;">Booking Date</th>
                            <th style="padding: 8px 8px;">Configuration</th>
                            <th style="padding: 8px 8px;">Flat No</th>
                            <th style="padding: 8px 8px;">Wing</th>
                            <th style="padding: 8px 8px;">Cluster / Tower</th>
                            <th style="padding: 8px 8px;">Sales Executive </th>
                            <th>Sourcing Manager </th>
                            </tr>
                            <tr style="background-color: #f1f1f1; border-bottom: 1px solid #ddd;">
                            <td style="padding: 8px 8px; text-align: center;">{{$booking->project_name}}</td>
                            <td style="padding: 8px 8px; text-align: center;">{{$booking->client_name}}</td>
                            <td style="padding: 8px 8px; text-align: center;">{{$booking->booking_date}}</td>
                            <td style="padding: 8px 8px; text-align: center;">{{$booking->configuration}}</td>
                            <td style="padding: 8px 8px; text-align: center;">{{$booking->flat_no}}</td>
                            <td style="padding: 8px 8px; text-align: center;">{{$booking->wing}}</td>
                            <td style="padding: 8px 8px; text-align: center;">{{$booking->tower}}</td>
                            <td style="padding: 8px 8px; text-align: center;">{{$booking->sales_person}}</td>
                            <td style="padding: 8px 8px; text-align: center;">{{$booking->sourcing_manager}}</td>
                            </tr>
                            <tr>
                        </table>
                        <br>
                        <p>We request you to kindly confirm the same and help us with the cost sheet and booking application copy for documentation purposes.</p>
                        <p>Looking forward to more bookings and a fruitful association with you in upcoming project launches, and events.</p>
                        <p>It is our constant endeavour to enhance your sales experience with us. To achieve the same, we request your valuable feedback here.</p>
                        <p>Thank You!</p>
                      </td>
                    </tr>
                  </table>
                  

                </td>
              </tr>

            <!-- END MAIN CONTENT AREA -->
            </table>
            <br>
            <!-- END CENTERED WHITE CONTAINER -->
            <div style="">
                <div class="email-d" style="display:flex; align-items:center">
                    <h3 style="font-size: 16px; color: #6b6765;  font-weight: 500; margin-bottom: 0;">Username</h3>
                    
                </div>
                <div class="email-d" style="display:flex; align-items:center">
                    <h3 style="font-size: 16px; color: #6b6765;  font-weight: 500; margin-bottom: 0;">D Name</h3>
                    <h3 style="font-size: 16px; color: #6b6765;  font-weight: 500; margin-bottom: 0;"> | keystonerealestateadvisory.com</h3>  
                </div>
                <div class="email-f">

                    <h3 style="color: #6b6765; font-weight: 500; font-size:16px; margin-bottom: 0;">Mobile : 555555</h3>
                </div>
                <div class="email-f">

                    <h3 style="color: #6b6765; font-weight: 500; font-size:16px; margin-bottom: 0;">Andheri</h3>
                </div>
                <div>
                    <!-- <a href="#" target="_blank"><img src="https://www.keystonerealestateadvisory.com/emailer/img/linkdln.jpg" alt="HomeBazaar.com" style=" border-radius: 4px; margin-top:4px  "></a></div> -->


                    <a href="https://keystonerealestateadvisory.com/" target="_blank"><img src="https://keystonerealestateadvisory.com/public/img/gallery/ks-logo.webp" alt="keystonerealestateadvisory.com" style="width:30%; border:0;     margin-top: 2px;"></a>
                </div>
            </div>

          </div>
        </td>
        <td>&nbsp;</td>
      </tr>
    </table>
  </body>
</html>



							          
							






