<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" >
    <style>
        td {
            padding: 5px;
            height: 16px;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 15px;
        }
        @media (min-width: 1400px){
            .container, .container-lg, .container-md, .container-sm, .container-xl, .container-xxl {
                max-width: 768px;
            }
        }
        .page-break {
            page-break-before: always;
        }
        .container {
            margin-top: 20px;
            margin-bottom: 40px;
            background: #fff;
            box-shadow: 0px 0px 13px #00000038;
            padding: 52px;
        }
        .blue_bg{
            background: #f0f8ff7a;
        }
    </style>
</head>
<body class="{{ (isset($pdf) && $pdf) ? "" : "blue_bg" }}">
    <div class="{{ (isset($pdf) && $pdf) ? "" : "container" }}" style="{{ (isset($pdf) && $pdf) ? "" : "margin-top:20px; margin-bottom:40px;" }}">
        @if(!isset($pdf))
        <a href="{{ route('joining_form.download_pdf', $joiningForm->candidate_id) }}" style="float: right;margin-top: -34px;padding: 3px 14px;border-radius: 7px;font-weight: bold;appearance: auto;writing-mode: horizontal-tb !important;color: buttontext;background-color: buttonface;border-style: outset;border-color: buttonborder;">Download</a>
        @endif
        <h5 style="text-align: center;text-decoration: underline;font-weight:bold;margin-bottom:25px">EMPLOYEE JOINING FORM</h5>
        <table style="width: 100%;margin:auto;border:1px solid black;">
            <tr style="border-bottom: 1px solid black;">
                <td><b>Date of Joining:</b> {{ date("d-M-Y", strtotime($joiningForm->joining_date)) }}</td>
                <td><b>Designation:</b> {{ $joiningForm->designation }}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-decoration: underline;text-align: center;background-color:#b6b6b68a"><b >PERSONAL DETAILS</b></td>
            </tr>
        </table>
        <table style="width: 100%;margin:auto;border:1px solid black;border-top:none;">
            <tr style="border-bottom: 1px solid black;">
                <td style="width: 40%"><b>First Name:</b> {{ $joiningForm->first_name }}</td>
                <td style="width: 40%"><b>Last Name:</b> {{ $joiningForm->last_name }}</td>
                <td rowspan="3" style="border-left: 1px solid black;width: 20%;padding:0;text-align:center">
                    <img src="{{ url('storage/app/'.$joiningForm->photo) }}" alt="Test" style="width: 90%;max-height: 100px;margin:auto">
                </td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td colspan="2"><b>Father/Husband Name:</b> {{ $joiningForm->middle_name }}</td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td colspan="2" style="height: 75px;vertical-align: top;"><b>Present Address:</b> {{ $joiningForm->present_address }}</td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td colspan="3" style="height: 75px;vertical-align: top;"><b>Permanent Address:</b> {{ $joiningForm->permanent_address }}</td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td><b>Mobile:</b> {{ $joiningForm->mobile }}</td>
                <td colspan="2" ><b>Personal Email ID:</b> {{ $joiningForm->email }}</td>
            </tr>
        </table>
        <table style="width: 100%;margin:auto;border:1px solid black;border-top:none;">
            <tr style="border-bottom: 1px solid black;">
                <td style="border-right: 1px solid black;width: 50%"><b>Date of Birth:</b> {{ $joiningForm->dob }}</td>
                <td><b>Marital Status:</b> {{ config('constants.MARITAL_STATUS_OPTIONS.'.$joiningForm->marital_status) }}</td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td style="border-right: 1px solid black;"><b>PAN No:</b> {{ $joiningForm->pan_number }}</td>
                <td><b>Blood Group:</b> {{ $joiningForm->blood_group }}</td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td style="border-right: 1px solid black;"><b>Aadhaar No:</b> {{ $joiningForm->aadhar_number }}</td>
                <td><b>Gender:</b> {{ $joiningForm->gender }}</td>
            </tr>
        </table>
        <table style="width: 100%;margin:auto;border:1px solid black;border-top:none;">
            <tr style="border-bottom: 1px solid black;">
                <td colspan="3"><b>Emergency Contact Details:</b></td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td style="border-right: 1px solid black;width:33.33%"><b>Name:</b> {{ $joiningForm->emergency_contact_name }}</td>
                <td style="border-right: 1px solid black;width:33.33%"><b>Relation:</b> {{ $joiningForm->emergency_contact_relation }}</td>
                <td><b>Contact No:</b> {{ $joiningForm->emergency_contact_number }}</td>
            </tr>
        </table>
        @php
            $educationalDetails = json_decode($joiningForm->educational_details);
        @endphp
        <table style="width: 100%;margin:auto;border:1px solid black;margin-top: 35px;">
            <tr style="border-bottom: 1px solid black;">
                <td colspan="6" style="text-decoration: underline;text-align: center;background-color:#b6b6b68a"><b >EDUCATIONAL DETAILS</b></td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td style="border-right: 1px solid black;width:16.66%;text-align:center"><b>Degree</b></td>
                <td style="border-right: 1px solid black;width:16.66%;text-align:center"><b>University/ Institute</b></td>
                <td style="border-right: 1px solid black;width:16.66%;text-align:center"><b>From</b></td>
                <td style="border-right: 1px solid black;width:16.66%;text-align:center"><b>To</b></td>
                <td style="border-right: 1px solid black;width:16.66%;text-align:center"><b>Percentage/ Grade</b></td>
                <td style="border-right: 1px solid black;width:16.66%;text-align:center"><b>Specialization</b></td>
            </tr>
            @if (!empty($educationalDetails))
                @foreach ($educationalDetails as $educational)
                <tr style="border-bottom: 1px solid black;">
                    <td style="border-right: 1px solid black;width:16.66%;text-align:center">{{ $educational->degree }}</td>
                    <td style="border-right: 1px solid black;width:16.66%;text-align:center">{{ $educational->university }}</td>
                    <td style="border-right: 1px solid black;width:16.66%;text-align:center">{{ $educational->from }}</td>
                    <td style="border-right: 1px solid black;width:16.66%;text-align:center">{{ $educational->to }}</td>
                    <td style="border-right: 1px solid black;width:16.66%;text-align:center">{{ $educational->percentage }}</td>
                    <td style="border-right: 1px solid black;width:16.66%;text-align:center">{{ $educational->specialization }}</td>
                </tr>
                @endforeach
            @endif
        </table>

        @php
            $employmentDetails = json_decode($joiningForm->organizational_details);
        @endphp
        <table class="page-break" style="width: 100%;margin:auto;border:1px solid black;margin-top: 35px;">
            <tr style="border-bottom: 1px solid black;">
                <td colspan="6" style="text-decoration: underline;text-align: center;background-color:#b6b6b68a"><b >EMPLOYMENT DETAILS (Last three Organizations)</b></td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td rowspan="2" style="border-right: 1px solid black;width:16.66%;text-align:center"><b>Sr. No.</b></td>
                <td rowspan="2" style="border-right: 1px solid black;width:16.66%;text-align:center"><b>Organization</b></td>
                <td rowspan="2" style="border-right: 1px solid black;width:16.66%;text-align:center"><b>Designation</b></td>
                <td colspan="2" style="border-right: 1px solid black;width:16.66%;text-align:center"><b>Period of Service</b></td>
                <td rowspan="2" style="border-right: 1px solid black;width:16.66%;text-align:center"><b>Full Time / Part Time</b></td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td style="border-right: 1px solid black;width:16.66%;text-align:center"><b>From</b></td>
                <td style="border-right: 1px solid black;width:16.66%;text-align:center"><b>To</b></td>
            </tr>
            @if (!empty($employmentDetails))
                @foreach ($employmentDetails as $key => $employment)
                    <tr style="border-bottom: 1px solid black;">
                        <td style="border-right: 1px solid black;width:16.66%;text-align:center">{{ $key+1 }}</td>
                        <td style="border-right: 1px solid black;width:16.66%;text-align:center">{{ $employment->organization }}</td>
                        <td style="border-right: 1px solid black;width:16.66%;text-align:center">{{ $employment->designation }}</td>
                        <td style="border-right: 1px solid black;width:16.66%;text-align:center">{{ $employment->from }}</td>
                        <td style="border-right: 1px solid black;width:16.66%;text-align:center">{{ $employment->to }}</td>
                        <td style="border-right: 1px solid black;width:16.66%;text-align:center">{{ $employment->type }}</td>
                    </tr>
                @endforeach
            @endif
        </table>

        @php
            $familyDetails = json_decode($joiningForm->family_details);
        @endphp
        <table style="width: 100%;margin:auto;border:1px solid black;margin-top: 35px;">
            <tr style="border-bottom: 1px solid black;">
                <td colspan="5" style="text-decoration: underline;text-align: center;background-color:#b6b6b68a"><b >FAMILY DETAILS</b></td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td style="border-right: 1px solid black;width:20%;text-align:center"><b>Sr. No.</b></td>
                <td style="border-right: 1px solid black;width:20%;text-align:center"><b>Name</b></td>
                <td style="border-right: 1px solid black;width:20%;text-align:center"><b>Relationship with Employee</b></td>
                <td style="border-right: 1px solid black;width:20%;text-align:center"><b>Contact Number</b></td>
                <td style="border-right: 1px solid black;width:20%;text-align:center"><b>Date of Birth</b></td>
            </tr>
            @if (!empty($familyDetails))
            @foreach ($familyDetails as $key => $family)
                <tr style="border-bottom: 1px solid black;">
                    <td style="border-right: 1px solid black;width:20%;text-align:center">{{ $key+1 }}</td>
                    <td style="border-right: 1px solid black;width:20%;text-align:center">{{ $family->name }}</td>
                    <td style="border-right: 1px solid black;width:20%;text-align:center">{{ $family->relationship }}</td>
                    <td style="border-right: 1px solid black;width:20%;text-align:center">{{ $family->contact_number }}</td>
                    <td style="border-right: 1px solid black;width:20%;text-align:center">{{ $family->dob }}</td>
                </tr>
            @endforeach
        @endif
        </table>


        @php
            $professionalDetails = json_decode($joiningForm->professional_details, true);
        @endphp
        <table style="width: 100%;margin:auto;border:1px solid black;margin-top: 35px;">
            <tr style="border-bottom: 1px solid black;">
                <td colspan="2" style="text-decoration: underline;text-align: center;background-color:#b6b6b68a"><b >PROFESSIONAL REFERENCES (only reporting manager, superiors & Sr. Colleagues are allowed)</b></td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td style="border-right: 1px solid black;width:20%;"><b>Name:</b>{{ $professionalDetails[0]['name'] }}</td>
                <td style="border-right: 1px solid black;width:20%;"><b>Name:</b>{{ $professionalDetails[1]['name'] }}</td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td style="border-right: 1px solid black;width:20%;"><b>Organization:</b>{{ $professionalDetails[0]['name'] }}</td>
                <td style="border-right: 1px solid black;width:20%;"><b>Organization:</b>{{ $professionalDetails[1]['name'] }}</td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td style="border-right: 1px solid black;width:20%;"><b>Designation:</b>{{ $professionalDetails[0]['name'] }}</td>
                <td style="border-right: 1px solid black;width:20%;"><b>Designation:</b>{{ $professionalDetails[1]['name'] }}</td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td style="border-right: 1px solid black;width:20%;"><b>Contact No:</b>{{ $professionalDetails[0]['name'] }}</td>
                <td style="border-right: 1px solid black;width:20%;"><b>Contact No:</b>{{ $professionalDetails[1]['name'] }}</td>
            </tr>
        </table>

        <table style="width: 100%;margin:auto;border:1px solid black;margin-top: 35px;">
            <tr style="border-bottom: 1px solid black;">
                <td style="text-decoration: underline;text-align: center;background-color:#b6b6b68a"><b >BANK ACCOUNTS DETAILS:</b></td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid black;"><b>Bank Name:</b> {{ $joiningForm->bank_name }}</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid black;"><b>Branch Name:</b> {{ $joiningForm->branch_name }}</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid black;"><b>Account Number:</b> {{ $joiningForm->account_number }}</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid black;"><b>IFSC Code:</b> {{ $joiningForm->ifsc }}</td>
            </tr>
        </table>

        <table class="page-break" style="width: 100%;margin:auto;border:1px solid black;margin-top: 35px;">
            <tr style="border-bottom: 1px solid black;">
                <td colspan="2" style="text-decoration: underline;text-align: left;background-color:#b6b6b68a"><b >GENERAL INFORMATION:</b></td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td style="border-right: 1px solid black; width: 50%">Have you ever suffered Or suffering from any physical impairment, disease or mental illness? If Yes Give Detail -</td>
                <td style="width: 50%">{{ $joiningForm->suffered_from_disease }}</td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td style="border-right: 1px solid black; width: 50%">Provide general practitioner details (Name & Contact Number) If Any-</td>
                <td style="width: 50%">{{ $joiningForm->practitioner_details }}</td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td style="border-right: 1px solid black; width: 50%">Have you ever been convicted in a court of law? If yes Give Details -</td>
                <td style="width: 50%">{{ $joiningForm->convicted_in_law }}</td>
            </tr>
        </table>

        <table style="width: 100%;margin:auto;border:1px solid black;margin-top: 35px;">
            <tr style="border-bottom: 1px solid black;">
                <td colspan="2" style="text-decoration: underline;text-align: left;background-color:#b6b6b68a"><b >DECLARATION:</b></td>
            </tr>
            <tr>
                <td colspan="2">
                    <p>I hereby declare that the above statements made in my application form are true, complete and correct to the best of my knowledge and belief. In the event of any information being found false or incorrect at any stage, my services are liable to be terminated without notice.</p>
                </td>
            </tr>
            <tr>
                <td colspan="2"><b>Place: </b></td>
            </tr>
            <tr>
                <td ><b>Date: </b></td>
                <td ><b>Signature:  </b></td>
            </tr>
        </table>

        <!-------------- Aggrement ------------------->
        <div class="page-break" style="text-align: justify">
            <p style="margin-bottom:25px;text-align: center;font-size: 14px;font-weight:bold;text-decoration: underline;{{ (isset($pdf) && $pdf) ? "" : "margin-top:40px;" }}">CONFIDENTIALITY AND NON-DISCLOSURE AGREEMENT</p>
            <p>This Confidentiality and Non-Disclosure Agreement is between “The employee” <b style="text-decoration: underline;"> {{ $joiningForm->first_name. ' ' .$joiningForm->last_name   }}</b> having PAN:<b style="text-decoration: underline;"> {{ $joiningForm->pan_number }}</b> and residential Address as per Aadhar Card <b style="text-decoration: underline;"> {{ $joiningForm->aadhar_number }}</b> (henceforth referred to as “The Employee/Service Provider”) and <b>Home Bazaar Services Pvt Ltd.</b> (henceforth referred to as “Company”).</p>
            <p>
                Whereas the Service Provider (“The Employee”) will receive, collect or have access to <b>Personal and/or Confidential Information*</b> from the company for the sole purpose of providing the service.
                In consideration of the opportunity to execute “Purpose” _______________________________, the employee gives the following undertaking to the company, namely to:
            </p>
            <ul>
                <li><p>Hold all <b>Personal and/or Confidential Information</b> in strict confidence, and only disclose said information with the express written consent of the Company; shared in writing OR orally by any executive of the company.</p></li>
                <li><p>Ensure that the employees of The Employee hold the <b>Personal and/or Confidential</b> Information (as defined below) in strict confidence;</p></li>
                <li>
                    <p><b>Personal and/or Confidential Information</b> means all information related to Client’s business and its actual or anticipated research and development or related to a Work Product/Service delivered or agreed to be delivered from Provider to Client, including without limitation</p>
                    <ul>
                        <li><p>Trade secrets, inventions, ideas, processes, computer source and object code, formulae, data, programs, other works of authorship, know-how, improvements, discoveries, developments, designs, frameworks, marketing Strategies and techniques;</p></li>
                        <li><p>Information regarding products or plans for research and development, marketing and business plans and strategies, budgets, financial statements, contracts, prices, suppliers, and customers; </p></li>
                        <li><p>Information regarding the skills and compensation of Client’s employees, recruitment strategies, contractors, and any other service providers; Information designated by Client, either in writing or orally, as Confidential Information,</p></li>
                        <li><p>The existence of any business discussions, information, negotiations, or agreements between Client and any third party; and</p></li>
                        <li><p>All/any such information related which is sensitive/important in nature for the Company &to any third party that is disclosed to the Company or to The Employee during the course of Client’s business (“Third Party Information”).</p></li>
                    </ul>
                </li>
                <li>
                    <p>The Employee will not join/provide services to any of the old/existing company/employees of the company if they start a business in same or similar industry; it includes Business Associates/Partners of the company as well.</p>
                </li>
                <li>
                    <p>Implement procedures that will preclude the accidental or unauthorized disclosure of the Personal and/or Confidential Information.</p>
                </li>
                <li>
                    <p>The Employee is responsible for the performance and quality of the Services in accordance with the timely and professional manner, consistent with industry practice, at a location, place and time that employee deems appropriate.</p>
                </li>
                <li>
                    <p>Maintains 100% integrity & Confidentiality of the organization with respect to its future plans & strategies.</p>
                </li>
                <li>
                    <p>Not reproduce or make copies of the Personal and/or Confidential Information except with the express written authorization of the Company.</p>
                </li>
                <li>
                    <p>Internal AOP structures and additional benefits of any sort that would be introduced from time to time by the management are expected to be kept confidential by the employee.</p>
                </li>
                <li>
                    <p>The employee shall not share any Lead assigned by the organization to any third party or individual without prior intimation of the same to the immediate reporting.</p>
                </li>
                <li>
                    <p>All the CRM information with respect to the booked clients is expected to be kept confidential and not shared with any third party or an individual.</p>
                </li>
                <li>
                    <p>The employee shall maintain healthy relationships with the organization and its associates, he/she shall not reveal confidential information about the organization and its associates with any other associates.</p>
                </li>
                <li>
                    <p>Immediately destroy the Personal and/or Confidential Information, including any copies or reproductions made thereof, within maximum seven (“15”) working days after its use or upon fulfilment or termination of the original purpose for which said information was provided; </p>
                </li>
                <li>
                    <p>Dispose of the Personal and/or Confidential Information in a manner acceptable to the Company and provide the Company with written notification of the date, time and method used in the disposal of all Personal and/or Confidential Information and/or return of all media that contain that information to the Company within Fifteen (“ 15”) days of completion of task; and </p>
                </li>
                <li>
                    <p>Agrees that the Company may require confirmation of the steps taken to comply with the requirements of this Agreement, including but not limited to conducting an audit of “The Employee” privacy practices. Failure to do so will result in the termination of this Agreement and “The Employee” will immediately return all Personal and/or Confidential Information received from the Company or collected on its behalf.</p>
                </li>
            </ul>
            <p>The Service Provider (“The Employee”) agrees that it will indemnify and hold the Company, its successors, trustees, officers, and employees harmless from and defend them against all expenses including legal fees, fines, exemplary damages, punitive damages and amounts paid in any settlement arising out of any breach of this Confidentiality and Non-Disclosure Agreement.</p>
            <p>The obligations undertaken pursuant to this Confidentiality and Non-Disclosure Agreement shall be unlimited as to time and will not cease even upon fulfilment or termination of the original purpose for which the Personal and/or Confidential Information was disclosed.</p>
            <p>To show their acceptance of the provisions of this Confidentiality and Non-Disclosure Agreement, the duly authorized signing officers of the parties have signed below.</p>
            <p style="margin-top: 16px"><b>Company Authority:</b></p>
            <p style="margin-bottom: 16px">
                Name: Shrikant Basare<br>
                Title: Director<br>
                Company: Home Bazaar Services Pvt Ltd<br>
                Date: ____________________<br>
                Place: Navi Mumbai<br>
                Signature with Company Stamp: ____________________________________ <br>

            </p>
            <p><b>The Employee:</b></p>
            <p>My signature certifies I have read and understand the Terms of Agreement as referenced above and the information on this form is true, accurate and complete.</p>
            <p  style="margin-top: 16px">Date: ______________________ <br>
                Place: ______________________<br>
                Signature: ___________________<br>
            </p>
        </div>
    </div>
</body>
</html>
