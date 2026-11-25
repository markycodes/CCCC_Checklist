<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Reset your CCCC password</title>
</head>
<body style="margin:0; padding:0; background-color:#f5f2ee; color:#2b2924; font-family:Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f5f2ee;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px; overflow:hidden; border:1px solid #e8e2d9; border-radius:12px; background-color:#ffffff;">
                    <tr>
                        <td style="padding:24px 32px; background-color:#a94720;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="padding-right:14px; vertical-align:middle;">
                                        <img src="{{ asset('assets/img/CCCClogo.webp') }}" width="52" height="52" alt="Catbalogan City Community College seal" style="display:block; width:52px; height:52px; border:0; border-radius:6px; background-color:#ffffff;">
                                    </td>
                                    <td style="vertical-align:middle; color:#ffffff;">
                                        <div style="font-family:Georgia, 'Times New Roman', serif; font-size:17px; font-weight:bold; line-height:1.35;">Catbalogan City</div>
                                        <div style="font-family:Georgia, 'Times New Roman', serif; font-size:17px; font-weight:bold; line-height:1.35;">Community College</div>
                                        <div style="padding-top:5px; color:#f8e4d8; font-size:10px; font-weight:bold; letter-spacing:1.5px;">CCCC STUDENT CHECKLIST</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px 36px 28px;">
                            <p style="margin:0 0 10px; color:#a94720; font-size:11px; font-weight:bold; letter-spacing:1.6px;">ACCOUNT SECURITY</p>
                            <h1 style="margin:0 0 22px; color:#29251f; font-family:Georgia, 'Times New Roman', serif; font-size:30px; font-weight:normal; line-height:1.2;">Reset your password</h1>
                            <p style="margin:0 0 16px; color:#4b473f; font-size:15px; line-height:1.7;">Hello,</p>
                            <p style="margin:0 0 24px; color:#4b473f; font-size:15px; line-height:1.7;">We received a request to reset the password for your CCCC Student Checklist account. Use the button below to choose a new password.</p>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 24px;">
                                <tr>
                                    <td align="center" style="border-radius:5px; background-color:#a94720;">
                                        <a href="{{ $resetUrl }}" style="display:inline-block; padding:14px 24px; border:1px solid #a94720; border-radius:5px; color:#ffffff; font-size:14px; font-weight:bold; line-height:1.2; text-decoration:none;">Reset password</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:0 0 8px; color:#6d675e; font-size:13px; line-height:1.6;">If the button does not work, copy and paste this secure link into your browser:</p>
                            <p style="margin:0 0 24px; font-size:13px; line-height:1.6; overflow-wrap:anywhere; word-break:break-word;">
                                <a href="{{ $resetUrl }}" style="color:#a94720; text-decoration:underline;">{{ $resetUrl }}</a>
                            </p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-left:3px solid #bc7045; background-color:#faf8f4;">
                                <tr>
                                    <td style="padding:14px 16px; color:#60594f; font-size:13px; line-height:1.65;">
                                        This link expires in <strong>{{ $expiresInMinutes }} minutes</strong>. If you did not request a password reset, you can ignore this email; your password will remain unchanged.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 32px; border-top:1px solid #eee8df; background-color:#fdfcf9; color:#827a6e; font-size:11px; line-height:1.6; text-align:center;">
                            This message is from <strong style="color:#60594f;">Catbalogan City Community College (CCCC)</strong>.<br>
                            CCCC Student Checklist · Automated account security message
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
