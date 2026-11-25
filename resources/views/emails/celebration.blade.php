@php
    $isBirthday = $type === 'birthday';
    $keystone = \App\Services\CelebrationService::isKeystone($company);
    $logo = $keystone ? asset('assets/celebrations/keystone-logo-light.png') : null;
@endphp
<!DOCTYPE html>
<html>
<body style="margin:0;padding:24px 12px;background:#f4f6fa;font-family:Arial,Helvetica,sans-serif;color:#1f2937">
    <div style="max-width:520px;margin:0 auto;background:#011e48;border-radius:16px;overflow:hidden">
        <div style="padding:22px 24px 0;text-align:center">
            @if($logo)
                <img src="{{ $logo }}" alt="{{ $company }}" style="height:46px;max-width:220px">
            @else
                <div style="color:#c3b246;font-weight:bold;font-size:16px">{{ $company }}</div>
            @endif
        </div>
        <div style="padding:22px 28px 30px;text-align:center;color:#ffffff">
            <div style="font-size:40px;line-height:1">{{ $isBirthday ? '🎂' : '🏆' }}</div>
            <div style="font-family:Georgia,'Times New Roman',serif;font-size:30px;color:#c3b246;margin:10px 0 4px">
                @if($isBirthday)
                    Happy Birthday
                @else
                    Happy {{ $years }}{{ \App\Services\CelebrationService::suffix($years) }} Work Anniversary
                @endif
            </div>
            <div style="font-size:22px;font-weight:bold;letter-spacing:.5px;margin-bottom:16px">{{ $person->name }}</div>
            <p style="font-size:15px;line-height:1.6;margin:0 0 18px;color:#e6eaf2">{{ $message }}</p>
            <p style="font-size:14px;margin:0;color:#c3b246">With warm wishes,<br><b>Team {{ $keystone ? 'Keystone' : $company }}</b></p>
        </div>
    </div>
    <p style="text-align:center;font-size:11px;color:#8a94a6;margin:12px 0 0">Sent by RealtySuite on behalf of HR.</p>
</body>
</html>
