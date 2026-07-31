<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New contact message</title>
</head>
<body style="margin:0;background:#f3f6fb;color:#172a4a;font-family:Arial,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:32px 16px;background:#f3f6fb;">
        <tr>
            <td align="center">
                <table role="presentation" width="620" cellspacing="0" cellpadding="0" style="width:100%;max-width:620px;overflow:hidden;border-radius:16px;background:#ffffff;">
                    <tr>
                        <td style="padding:28px 32px;background:#10264b;color:#ffffff;">
                            <p style="margin:0 0 8px;color:#6ee7f3;font-size:12px;font-weight:700;letter-spacing:1.4px;text-transform:uppercase;">Resume Engineer</p>
                            <h1 style="margin:0;font-size:26px;line-height:1.2;">New contact message</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 32px;">
                            <p style="margin:0 0 22px;color:#51627b;font-size:15px;line-height:1.6;">Reply directly to this email to respond to the visitor.</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-size:14px;">
                                <tr>
                                    <td style="width:120px;padding:10px 0;border-bottom:1px solid #e7edf5;color:#74829a;font-weight:700;">From</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e7edf5;color:#172a4a;">{{ $contactMessage->name }}</td>
                                </tr>
                                <tr>
                                    <td style="width:120px;padding:10px 0;border-bottom:1px solid #e7edf5;color:#74829a;font-weight:700;">Email</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e7edf5;color:#172a4a;"><a href="mailto:{{ $contactMessage->email }}" style="color:#246bfd;text-decoration:none;">{{ $contactMessage->email }}</a></td>
                                </tr>
                                <tr>
                                    <td style="width:120px;padding:10px 0;border-bottom:1px solid #e7edf5;color:#74829a;font-weight:700;">Topic</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #e7edf5;color:#172a4a;">{{ $contactMessage->topicLabel() }}</td>
                                </tr>
                            </table>
                            <div style="margin-top:25px;padding:20px;border-radius:10px;background:#f5f8fd;color:#233857;font-size:15px;line-height:1.65;white-space:pre-line;">{{ $contactMessage->message }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
