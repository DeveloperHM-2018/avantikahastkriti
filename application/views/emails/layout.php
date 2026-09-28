<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= htmlspecialchars($subject) ?></title>
</head>

<body style="margin:0; padding:0; background-color:#f4f4f4; font-family:Arial, Helvetica, sans-serif;">
	<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding:24px 0;">
		<tr>
			<td align="center">
				<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:6px; overflow:hidden;">
					<tr>
						<td style="background-color:#1a1a1a; padding:20px 32px;">
							<span style="color:#ffffff; font-size:20px; font-weight:bold;"><?= htmlspecialchars(APP_NAME) ?></span>
						</td>
					</tr>
					<tr>
						<td style="padding:32px; color:#333333; font-size:15px; line-height:1.6;">
							<?= $body ?>
						</td>
					</tr>
					<tr>
						<td style="background-color:#f0f0f0; padding:16px 32px; color:#888888; font-size:12px;">
							&copy; <?= date('Y') ?> <?= htmlspecialchars(APP_NAME) ?>. All rights reserved.
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>

</html>
