<html>
    <style>
        .btn {
            display: inline-block;
            font-weight: 400;
            line-height: 1.53;
            color: #697a8d;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: 0.4375rem 1.25rem;
            font-size: 0.9375rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease-in-out;
        }
        .btn-primary {
            color: #fff;
            background-color: #696cff;
            border-color: #696cff;
            box-shadow: 0 0.125rem 0.25rem 0 rgb(105 108 255 / 40%);
        }
    </style>
    <body>
        <b>Dear {{ $candidate->name }}</b><br>
        <p><b>Congratulations!!</b> On being selected for the position of {{ $candidate->designation }} with Home Bazaar Services Pvt Ltd.</p>

        <ol>
            <li>Kindly click on the <b>Joining Form Link</b>, fill all the necessary information in it & submit the same(<b>Attached is the specimen of the Joining Link</b>)</li>
            <li>Also share all the documents mentioned below in a <b>zipped</b> folder in revert and the same needs to be carried on the day of joining.</li>
        </ol>

        <p>{{ $candidate->url }}</p>
        <p><b>*List of documents : (Carry original copies with photocopies on the joining day its Mandatory)</b></p>

        <ul style="list-style: none;">
            <li>Ø  2 Passport Size Photos(Latest Photographs)</li>
            <li>Ø  PAN Card and Aadhar Card</li>
            <li>Ø  2 Address proof (Eg: Light Bill & Voter ID)</li>
            <li>Ø  Academic Mark sheet & Certificates (10th, 12th, Graduation, PG, and MBA) </li>
            <li>Ø  Offer Letter(Current/Previous Organization)</li>
            <li>Ø  Experience Letters (From every last organization worked with)</li>
            <li>Ø  Last three months Salary Slips</li>
            <li>Ø  Saving Bank Account Details, Bank Account No., Name, IFSC Code & Branch Details.</li>
            <li>Ø  Cancelled Cheque</li>
        </ul>

        <p>
            <b>*Time:</b>Kindly reach sharp by {{ config('constants.EMPLOYEE_REACHED_TIME') }}<br>
            <b>*Attire:</b> Be in proper formal attire.
        </p>
        <p>
            <b>Note:</b> <b style="text-decoration: underline">Kindly make it compulsory to carry all the above-mentioned documents on the same day.<b><br>
                <b style="text-decoration: underline">Your Offer Letter (hardcopy) will be released only if all your documents are complete with the DOJ.</b>
        </p>
    </body>
</html>
