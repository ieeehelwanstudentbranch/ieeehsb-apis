<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>Password Reset</h2>

		<div>
			{{$user->firstName}} ! To reset your password, complete this form: {{ URL::to('/api/password/reset', $reset_code) }}.<br/>
		</div>
	</body>
</html>
