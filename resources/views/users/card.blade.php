<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }} - Digital Visiting Card</title>

    <!-- <link rel="stylesheet" href="{{ asset('css/stylecards.css') }}"> -->
    <link rel="stylesheet" href="{{ asset("assets/css/stylecards.css") }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
  .sav_txt { font-size: 11px; font-weight: bold; top: 15px; }

  .savecont {
      position: fixed; width: 60px; height: 60px; bottom: 70px; right: 16px;
      background-color: #000; color: #FFF; border-radius: 50px; text-align: center;
      box-shadow: 0 0 6px #999; z-index: 1000;
  }
  .share_box {
      position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
      background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.2);
      z-index: 9999; width: 90%; max-width: 400px; display: none;
  }
  .share_box p { text-align: center; font-weight: bold; margin-bottom: 15px; }
  .shar_btns { display: flex; align-items: center; gap: 10px; padding: 10px; cursor: pointer; }
  .shar_btns:hover { background-color: #f2f2f2; border-radius: 8px; }
  .close {
    position: absolute;
    top: 10px;
    right: 12px;
    cursor: pointer;
    font-size: 26px;
    font-weight: bold;
    line-height: 1;
    padding: 8px; /* increases clickable area */
    z-index: 10000;
}
  .qrcode_box {right: 32px; }
  .active {
    border-color: #198754;
    background: #198754;
    color: #FFF;
}
.oth_parent_div {
    padding: 10px 0 50px;
}
.oth_man_div {
    display: flex;
    box-sizing: border-box;
    cursor: pointer;
    padding: 6px 10px;
    margin: 15px 0px 4px 15px;
    width: 100%;
    -webkit-box-align: center;
    align-items: center;
    opacity: 1;
    background-color: #2e2c41;
    box-shadow: 0px 0px 10px 0px #0000002e;
    border: none;
    border-radius: 4px;
    max-width: 370px;
    color: #FFF;
}
.dis_flex .big_btns {
    background: #2e2c41;
    color: white;
    border-radius: 3px;
    font-size: 12px;
    padding: 10px;
    display: inline-block;
    text-align: center;
    margin: 3px 0px;
    border: 1px solid;
    width: 139px;
    font-weight: 500;
    box-shadow: 0px 0px 10px 0px #0000002e;
}
.logoclass{
        width: 30%;
    max-width: 100%;
    height: auto;
    background: white;
    border-radius: 0px;
    background: #2e2c41;
    margin-top: 10px;
}
</style>
</head>

<body>

<div class="card">

    {{-- USER PHOTO --}}
    <div class="card_content">
        <div class="c_c_img">
            <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('ks/images/default_user.png') }}"
                alt="{{ $user->name }}">
        </div>
    </div>

    <div class="card_content2">

        {{-- USER NAME --}}
        <h2>{{ $user->first_name }} {{ $user->last_name }}</h2>

        {{-- DESIGNATION --}}
        <p>
            {{ $user->designation->name ?? 'Designation' }}
        </p>

        {{-- COMPANY --}}
        <p style="font-size:12px;margin-top:5px;">
            {{ $user->businessUnit ? $user->businessUnit->name : 'Company Name' }}
        </p>

        {{-- CONTACT SHORTCUT ICONS --}}
        <div class="dis_flex pad">
            {{-- Call --}}
            @php
                $contact = $user->official_contact ?? $user->personal_contact;
            @endphp

            @if($contact)
                {{-- Call --}}
                <a href="tel:+{{ $contact }}" class="atag">
                    <img src="{{ asset('assets/img/icons/call.png') }}" alt="Call" class="imgtag">
                </a>

                {{-- WhatsApp --}}
                <a href="https://api.whatsapp.com/send?phone={{ $contact }}" class="atag">
                    <img src="{{ asset('assets/img/icons/whatsapp.png') }}" alt="WhatsApp" class="imgtag">
                </a>
            @endif
            {{-- Location --}}
         
                <a href="https://maps.app.goo.gl/8Vv9PSdNqZWvZ4RK8" class="atag" target="_blank">
                    <img src="{{ asset('assets/img/icons/location.png') }}" alt="Location" class="imgtag">
                </a>
            

            {{-- Email --}}
            @if($user->official_email)
                <a href="mailto:{{ $user->official_email }}" class="atag">
                    <img src="{{ asset('assets/img/icons/email.png') }}" alt="Email" class="imgtag">
                </a>
            @endif

            {{-- Website --}}
            @if($user->businessUnit)
                <a href="{{ $user->businessUnit ? $user->businessUnit->domain : 'Company Name' }}" class="atag" target="_blank">
                    <img src="{{ asset('assets/img/icons/website.png') }}" alt="Website" class="imgtag">
                </a>
            @endif
        </div>

        

        {{-- SHARE VIA WHATSAPP --}}
        <div class="dis_flex">
            <div class="share_wtsp">
                @php
                    // Current user card URL
                    $userUrl = url()->current();
                    $defaultMessage = "View my Digital Card at link below: Click to Call, Whatsapp, Save to Contacts and more..\n\n{$userUrl}";
                @endphp

                <!-- Hidden link used for WhatsApp share -->
                <a id="shareViaWhatsAppLink" 
                href="https://api.whatsapp.com/send?text={{ urlencode($defaultMessage) }}&phone=" 
                target="_blank" 
                style="display:none;"></a>

                <!-- <form id="wtsp_form" target="_blank" onsubmit="return false;">
                    <label class="lbl_91 css8_lbl">
                        +<input type="text" id="cntcode" value="91" style="width:32px; margin-top:-11px; padding:5px; box-shadow:none;">
                    </label>
                    <input type="number" name="phone" id="txtPhone" pattern="[0-9]*" inputmode="numeric" autocomplete="off"
                        placeholder="Share Card via WhatsApp" maxlength="10" style="box-shadow:5px 0 10px #0000002e;" 
                        onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                    <input type="hidden" name="text" value="{{ $defaultMessage }}">
                    <div class="wtsp_share_btn" onclick="shareViaWhatsApp();">
                        <i class="fa fa-whatsapp"></i> Share
                    </div>
                </form> -->
            </div>
        </div>

        <script>
        function shareViaWhatsApp() {
            var cod = $('#cntcode').val();
            var pon = $('#txtPhone').val();
            if(pon) {
                var shareLink = $("#shareViaWhatsAppLink");
                shareLink.attr("href", "https://api.whatsapp.com/send?text={{ urlencode($defaultMessage) }}&phone=" + cod + pon);
                shareLink[0].click();

                // Reset input
                $('#txtPhone').val('');
                shareLink.attr("href", "https://api.whatsapp.com/send?text={{ urlencode($defaultMessage) }}&phone=");
            }
        }

        $(document).ready(function(){
            $('#txtPhone').focus(function() { $(".savecont").hide(); });
            $('#txtPhone').keyup(function() { 
                if($(this).val() == '') $(".savecont").show(); 
                else $(".savecont").hide(); 
            });

            $(document).scroll(function() {
                var y = $(this).scrollTop();
                if(y > 100 && y < 200) $('.savecont').fadeOut();
                else $('.savecont').fadeIn();
            });

            $('.wtsp_share_btn').click(function(){
                $('#txtPhone').val('');
                $('#txtPhone').blur();
                $('.savecont').show();
            });
        });
        </script>

    <div class="dis_flex">
        <a href="{{ route('user.vcf', $user->id) }}" download>
            <div class="big_btns">
                Save to Contacts <i class="fa fa-user-plus"></i>
            </div>
        </a>
        
    </div>

      <!-- <div class="dis_flex">
          <a href="https://www.facebook.com/keystonerealestateadvisorypvtltd/" target="_blank"><div class="social_med"><i class="fa fa-facebook"></i></div></a>
          <a href="https://www.instagram.com/keystonerealestateadvisory/" target="_blank"><div class="social_med"><i class="fa fa-instagram"></i></div></a>
          <a href="https://in.linkedin.com/company/keystone-realestate-advisory" target="_blank"><div class="social_med"><i class="fa fa-linkedin"></i></div></a>
          <a href="https://www.youtube.com/@keystone_real_estate_advisory" target="_blank"><div class="social_med"><i class="fa fa-youtube"></i></div></a>
      </div> -->
  </div>
</div>

<div class="savecont">
  <a href="{{ route('user.vcf', $user->id) }}" class="savbtn">
    <div class="sav_txt">Save<br>Contact</div>
    <span class="pulse-ring"></span>
</a>
</div>
<!-- <div class="card2" id="other_links">
    <h3>Our Associates for Construction</h3>
    <div class="oth_parent_div">
    	<!--<div class="oth_man_div">-->
    	<!--    <img src="https://digicarda.com/images/link-icon.png" style="object-fit: cover; width: 12%; padding-right: 15px;">-->
    	<!--    <a href="https://drive.google.com/file/d/1ou791Tv3JmPj1_lj3VWSIdoKwDNd0nTy/view?usp=sharing">Our Profile</a>-->
    	<!--</div>-
    	<div class="oth_man_div">
    	    <img src="{{ asset('ks/images/link-icon.png') }}" style="object-fit: cover; width: 12%; padding-right: 15px;">
    	    <a href="https://keystonerealestateadvisory.com/aum-sai-company-profile.pdf" target="_blank">Aum Sai Constructions</a>
    	</div>
    	<div class="oth_man_div">
    	    <img src="{{ asset('ks/images/link-icon.png') }}" style="object-fit: cover; width: 12%; padding-right: 15px;">
    	    <a href="https://keystonerealestateadvisory.com/AJ-Constructions-profile.pdf" target="_blank">AJ Constructions</a>
    	</div>
    </div>
</div> -->
<div class="card2" id="about_us">
		<h3>About Us</h3>
    	
		<p>Keystone Finserv provides fast, tailored financial solutions through leading banking partners. We simplify the loan process and support clients at every step.

<b>Mission</b>
Trusted, transparent, client-focused financial guidance.

<b>Vision</b>
Innovative, reliable, and service-driven financial partnership.

<!-- <b>Values</b>
•	Innovative.
•	Ethical.
•	Reliability.
•	Integrity.
•   Committed -->

   
	</div>
<div class="card2" id="product_services">
   <h3>Our End to End Solutions </h3>
   <div class="product_s">
      <p>Home Loan</p>
      <img src="{{ asset('assets/img/icons/house.png') }}" alt="Logo"><br><br>
      <a href="https://api.whatsapp.com/send?phone={{ $contact }}	&amp;text=Enquiry for product: Home Loan" target="_blank">
         <div class="btn_buy">Enquire Now</div>
      </a>
   </div>
   <div class="product_s">
      <p>Property Search </p>
      
      <img src="{{ asset('assets/img/icons/real-estate.png') }}" alt="Logo"><br><br>
      <a href="https://api.whatsapp.com/send?phone={{ $contact }}	&amp;text=Enquiry for product: Property Search" target="_blank">
         <div class="btn_buy">Enquire Now</div>
      </a>
   </div>
   <div class="product_s">
      <p>Loan Against Property </p>
      <img src="{{ asset('assets/img/icons/mortgage-loan.png') }}" alt="Logo"><br><br>
      <a href="https://api.whatsapp.com/send?phone={{ $contact }}	&amp;text=Enquiry for product: Loan Against Property" target="_blank">
         <div class="btn_buy">Enquire Now</div>
      </a>
   </div>
   <div class="product_s">
      <p>Personal Loan</p>
      <img src="{{ asset('assets/img/icons/personal.png') }}" alt="Logo"><br><br>
      <a href="https://api.whatsapp.com/send?phone={{ $contact }}	&amp;text=Enquiry for product: Personal Loan" target="_blank">
         <div class="btn_buy">Enquire Now</div>
      </a>
   </div>
   <div class="product_s">
      <p>Business Loan</p>
      <img src="{{ asset('assets/img/icons/personal.png') }}" alt="Logo"><br><br>
      <a href="https://api.whatsapp.com/send?phone={{ $contact }}	&amp;text=Enquiry for product: Business Loan" target="_blank">
         <div class="btn_buy">Enquire Now</div>
      </a>
   </div>
   
</div>
	<br>
	<br>
	<br>
	<br>
<div class="menu_bottom">
  <div class="menu_container">
      <div class="menu_item"><a href="tel:+{{ $contact }}"><i class="fa fa-phone"></i><div class="link_btn">Call Now</div></a></div>
      <div class="menu_item"><a href="https://wa.me/{{ $contact }}"><i class="fa fa-whatsapp"></i><div class="link_btn">Whatsapp</div></a></div>
      <!-- <div class="menu_item" id="share_box_pop"><i class="fa fa-share-alt"></i>Share</div>
      <div class="menu_item" id="qr_box_pop"><i class="fa fa-qrcode"></i>QR Code</div> -->
  </div>
</div>

<!-- Share Popup Box -->
<div class="share_box" id="share_box">
    <div class="close" id="close_sharer">×</div>

  <p>Share My Digital Card</p>

  <a href="https://api.whatsapp.com/send?text={{ urlencode($defaultMessage) }}" target="_blank">

    <div class="shar_btns"><i class="fa fa-whatsapp"></i><p>WhatsApp</p></div>
  </a>

  <!-- <a href="https://www.facebook.com/sharer/sharer.php?u=https://keystonerealestateadvisory.com/suryabhanmaurya" target="_blank">
    <div class="shar_btns"><i class="fa fa-facebook"></i><p>Facebook</p></div>
  </a>

  <a href="https://www.linkedin.com/shareArticle?mini=true&url=https://keystonerealestateadvisory.com/suryabhanmaurya" target="_blank">
    <div class="shar_btns"><i class="fa fa-linkedin"></i><p>LinkedIn</p></div>
  </a>
  <a href="sms:?body=View my Digital Card at link below: Click to Call, Whatsapp, Save to Contacts and more..   https://keystonerealestateadvisory.com/suryabhanmaurya" target="_blank">
      <div class="shar_btns"><i class="fas fa-comment-dots"></i><p>SMS</p></div>
  </a> -->

</div>
<!--<div class="share_box" style="display: block;">-->
				
				
<!--<div class="close" id="close_sharer">×</div>-->
<!--<p>Share My Digital Card </p>-->
<!--		<a href="https://api.whatsapp.com/send?text=View my Digital Card at link below: Click to Call, Whatsapp, Save to Contacts and more..  https://keystonerealestateadvisory.com/suryabhanmaurya"><div class="shar_btns"><i class="fa fa-whatsapp" id="whatsapp2" target="_blank"></i><p>WhatsApp</p></div></a>-->
<!--	<a href="sms:?body=View my Digital Card at link below: Click to Call, Whatsapp, Save to Contacts and more..   https://keystonerealestateadvisory.com/suryabhanmaurya" target="_blank"><div class="shar_btns"><i class="fas fa-comment-dots"></i><p>SMS</p></div></a>-->
	
<!--	<a href="https://www.facebook.com/sharer/sharer.php?u=https://keystonerealestateadvisory.com/suryabhanmaurya" target="_blank"><div class="shar_btns"><i class="fa fa-facebook"></i><p>Facebook</p></div></a>-->
<!--	<a href="https://twitter.com/intent/tweet?text=https://keystonerealestateadvisory.com/suryabhanmaurya" target="_blank"><div class="shar_btns"><i class="fa fa-twitter"></i><p>Twitter</p></div></a>-->
<!--	<a href="" target="_blank"><div class="shar_btns"><i class="fa fa-instagram"></i><p>Instagram</p></div></a>-->
<!--	<a href="https://www.linkedin.com/cws/share?url=https://keystonerealestateadvisory.com/suryabhanmaurya" target="_blank"><div class="shar_btns"><i class="fa fa-linkedin"></i><p>Linkedin</p></div></a>-->
<!--</div>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
$(document).ready(function(){

    // WhatsApp Share Function
    window.shareViaWhatsApp = function() {
        var cod = $('#cntcode').val().trim();
        var pon = $('#txtPhone').val().trim();
        if(!pon){ alert("Please enter a WhatsApp number"); return; }
        var fullHref = "https://api.whatsapp.com/send?phone=" + cod + pon + "&text=" + encodeURIComponent("Hey! Check out Suryabhan Maurya’s digital business card:\n\n👉 https://keystonerealestateadvisory.com/suryabhanmaurya");
        $('#shareViaWhatsAppLink').attr("href", fullHref)[0].click();
        $('#txtPhone').val('');
    };

    // SHARE POPUP OPEN/CLOSE
    $('#share_box_pop').click(function(){ $('#share_box').show(); });
    $('#close_sharer').click(function(){ $('#share_box').hide(); });

    // QR POPUP
    // --- QR POPUP OPEN ---
    $('#qr_box_pop').click(function () {

    // Prevent duplicate popup
    if ($('#qrModal').length > 0) return;

    let name = "{{ $user->name }}";
    let company = "{{ $user->businessUnit->name ?? '' }}";
    let vcfUrl = "{{ route('user.vcf', $user->id) }}";
    let profileUrl = "{{ url()->current() }}";

    // Create popup
    $('body').append(`
        <div class="qrcode_box" id="qrModal" style="display:block;">
            <div class="close" id="close_qrbox">×</div>
            <div class="qrpop_hed">Scan my QR Code</div>

            <div style="margin-top:22px; text-align:center; width:100%;">
                <p class="clr">${company}<br><strong>${name}</strong></p>

                <div>
                    <button id="btnVcf" class="btn-crd active">Save Contact</button>
                    <button id="btnCrd" class="btn-crd">Digital Profile</button>
                </div>

                <!-- VCF QR -->
                <div id="vcfDiv">
                    <img src="https://quickchart.io/chart?cht=qr&chs=184x184&chl=${vcfUrl}">
                    <p style="font-size:12px;">${vcfUrl}</p>
                    <a href="${vcfUrl}" target="_blank">
                        <div class="big_btns">Download <i class="fa fa-download"></i></div>
                    </a>
                </div>

                <!-- DIGITAL PROFILE QR -->
                <div id="crdDiv" style="display:none;">
                    <img id="profileQR" 
                         src="https://quickchart.io/chart?cht=qr&chs=184x184&format=png&chl=${profileUrl}">
                    <p style="font-size:12px;">${profileUrl}</p>

                    <!-- PNG Download -->
                    <a id="downloadProfilePng" href="#">
                        <div class="big_btns">Download PNG <i class="fa fa-download"></i></div>
                    </a>
                </div>
            </div>
        </div>
    `);

    // Auto-connect PNG download to QR image
    setTimeout(() => {
        let imgSrc = $('#profileQR').attr('src');
        $('#downloadProfilePng').attr('href', imgSrc);
        }, 300);
    });
    
    
    // --- CLOSE POPUP ---
    $(document).on('click', '#close_qrbox', function () {
        $('#qrModal').remove();
    });
    
    
    // --- TOGGLE VCF QR ---
    $(document).on('click', '#btnVcf', function () {
        $('#btnCrd').removeClass('active');
        $(this).addClass('active');
    
        $('#crdDiv').hide();
        $('#vcfDiv').fadeIn();
    });
    
    // --- TOGGLE DIGITAL PROFILE QR ---
    $(document).on('click', '#btnCrd', function () {
        $('#btnVcf').removeClass('active');
        $(this).addClass('active');
    
        $('#vcfDiv').hide();
        $('#crdDiv').fadeIn();
    });
    
    });
    
    // FORCE PNG DOWNLOAD
    $(document).on('click', '#downloadProfilePng', function(e) {
        e.preventDefault();
    
        const qrUrl = "https://quickchart.io/chart?cht=qr&chs=184x184&format=png&chl=https://keystonerealestateadvisory.com/suryabhanmaurya";
    
        fetch(qrUrl)
            .then(res => res.blob())
            .then(blob => {
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = "digital-profile.png"; // forced download name
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            })
            .catch(err => console.error("Download Error:", err));
    });
    </script>
    
    <script>
    $(document).on('click', '#btnVcf', function() {
        $(this).addClass('active');
        $('#btnCrd').removeClass('active');
        $('#vcfDiv').fadeIn();
        $('#crdDiv').fadeOut();
    });
    
    $(document).on('click', '#btnCrd', function() {
        $(this).addClass('active');
        $('#btnVcf').removeClass('active');
        $('#crdDiv').fadeIn();
        $('#vcfDiv').fadeOut();
    });
    
</script>
</body>
</html>
