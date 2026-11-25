<!DOCTYPE html>
<html>
<body style="margin:0;padding:24px 12px;background:#f4f6fa;font-family:Arial,Helvetica,sans-serif;color:#1f2937">
    <div style="max-width:520px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden">
        <div style="background:#011e48;color:#c3b246;font-weight:bold;padding:14px 22px;font-size:15px">{{ $company }}</div>
        <div style="padding:22px">
            <p style="margin:0 0 12px">Dear {{ $first }},</p>
            <p style="margin:0 0 12px">Please find your <b>{{ $title }}</b> attached{{ $letter->signed_pdf ? ', digitally signed' : '' }}.</p>
            <p style="margin:0 0 12px">You can also open it any time in RealtySuite under <b>KeyMe &gt; My letters</b>.</p>
            <p style="margin:0 0 4px">If anything needs correcting, please reply to this email.</p>
            <p style="margin:16px 0 0">Regards,<br><b>{{ $sender?->name ?? 'HR' }}</b><br>HR, {{ $company }}</p>
        </div>
    </div>
</body>
</html>
