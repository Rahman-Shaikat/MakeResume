<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>Verify your email address</title>
</head>
<body style="margin:0;padding:0;background:#f3f6fb;color:#1f2a3d;font-family:Arial,Helvetica,sans-serif;">
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">Verify your email to begin building your professional resume.</div>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;background:#f3f6fb;">
        <tr>
            <td align="center" style="padding:42px 16px;">
                <table role="presentation" width="620" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:620px;border-collapse:separate;background:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 18px 50px rgba(17,29,56,.10);">
                    <tr>
                        <td style="padding:28px 38px;background:#111d38;border-bottom:4px solid #246bfd;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td>
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="width:38px;height:38px;">
                                                    <img src="{{ asset('assets/common/media/logo.png') }}" width="38" height="38" alt="Resume Studio" style="display:block;width:38px;height:38px;border:0;">
                                                </td>
                                                <td style="padding-left:11px;color:#ffffff;font-size:19px;font-weight:800;letter-spacing:-.4px;">Resume<span style="color:#66dceb;">Studio</span></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td align="right" style="color:#91a0ba;font-size:11px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;">Account security</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:44px 38px 18px;">
                            <div style="display:inline-block;padding:7px 11px;border-radius:20px;background:#eaf1ff;color:#246bfd;font-size:10px;font-weight:800;letter-spacing:1.1px;text-transform:uppercase;">Email verification</div>
                            <h1 style="margin:20px 0 12px;color:#111d38;font-size:30px;line-height:1.18;letter-spacing:-.8px;">Welcome, {{ $user->name }}.</h1>
                            <p style="margin:0;color:#69758a;font-size:15px;line-height:1.7;">You’re one step away from creating a polished resume. Confirm that this email address belongs to you to secure your account and unlock Resume Studio.</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:10px 38px 12px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;border:1px solid #e2e8f2;border-radius:12px;background:#f8faff;">
                                <tr>
                                    <td style="padding:15px 17px;color:#536075;font-size:12px;">
                                        <span style="display:block;margin-bottom:4px;color:#8a95a8;font-size:9px;font-weight:800;letter-spacing:1px;text-transform:uppercase;">Email address</span>
                                        <strong style="color:#26334a;font-size:14px;">{{ $user->email }}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:22px 38px 25px;">
                            <a href="{{ $verificationUrl }}" style="display:inline-block;padding:15px 30px;border-radius:10px;background:#246bfd;color:#ffffff;font-size:14px;font-weight:800;text-decoration:none;box-shadow:0 8px 20px rgba(36,107,253,.24);">Verify email address&nbsp;&nbsp;→</a>
                            <p style="margin:14px 0 0;color:#8a95a8;font-size:11px;">This secure link expires in {{ $expirationMinutes }} minutes.</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 38px 32px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;border-radius:11px;background:#f0f9f7;">
                                <tr>
                                    <td style="width:34px;padding:16px 0 16px 17px;color:#1f9d72;font-size:19px;vertical-align:top;">✓</td>
                                    <td style="padding:16px 17px 16px 8px;color:#547068;font-size:12px;line-height:1.55;">
                                        <strong style="display:block;margin-bottom:3px;color:#26745c;">Why verify?</strong>
                                        Verification protects your private resume information and ensures only you can access your account.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:25px 38px;border-top:1px solid #e8ecf2;background:#fbfcfe;color:#8a95a8;font-size:11px;line-height:1.6;">
                            <strong style="display:block;margin-bottom:5px;color:#566277;">Button not working?</strong>
                            Copy and paste this URL into your browser:<br>
                            <a href="{{ $verificationUrl }}" style="color:#246bfd;text-decoration:none;word-break:break-all;">{{ $verificationUrl }}</a>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:23px 38px;background:#111d38;color:#8592aa;font-size:10px;line-height:1.7;">
                            If you didn’t create this account, you can safely ignore this email.<br>
                            <span style="color:#c8d1df;">Resume Studio · Build your story with confidence.</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
