<!DOCTYPE html>
<html>
<body style="margin:0;padding:24px;background:#f4f6fa;font-family:Arial,Helvetica,sans-serif;color:#1f2937">
    <div style="max-width:460px;margin:0 auto;background:#ffffff;border-radius:12px;padding:24px">
        <div style="font-size:18px;font-weight:bold;color:#011e48;margin-bottom:12px">RealtySuite</div>
        <p style="margin:0 0 12px">Hi {{ $name }},</p>
        <p style="margin:0 0 16px">Use this code to set a new password:</p>
        <div style="font-size:32px;font-weight:bold;letter-spacing:8px;color:#011e48;background:#f4f6fa;border-radius:10px;padding:14px;text-align:center">{{ $code }}</div>
        <p style="margin:16px 0 0;font-size:14px">The code works for {{ $minutes }} minutes and only once.</p>
        <p style="margin:12px 0 0;font-size:13px;color:#6b7280">If you didn't ask to reset your password, ignore this email and tell HR. Never share this code with anyone.</p>
    </div>
</body>
</html>
