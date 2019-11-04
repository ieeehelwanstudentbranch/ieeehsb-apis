<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
		<style>
			.emailContainer{
				background-color: #ccc;
				padding: 20px;
			}
			.emailWrapper{
				padding: 10px 20px;
				margin: auto;
				text-align: left;
				width: 70%;
				background-color: #fff;
			}
			.emailHeader{
				object-fit: contain;
				object-fit: contain;
				margin: 20px auto;
				display: block;
			}
	
			.emailButton{
				display: block;
				border: 1px solid transparent;
				padding: 15px 30px;
				color: white !important;
				text-decoration: none;
				border-radius: 5px;
				width: max-content;
				align-self: center;
				margin: 20px 0;
				text-transform: uppercase;
				margin: auto
			}
			.success{
				background-color: green;
			}
			.danger{
				background-color: #c50000
			}
			.emailFooter{
				text-align: center;
			}
			.emailFooter ul{
				margin: auto;
				padding: 0
			}
			.emailFooter ul li{
				display: inline-block;
				list-style-type: none
			}
			.emailFooter ul li img{
				display: block;
				width: 50px;
				height: 50px;
			}
		</style>
	</head>
	<body>
		<section class="emailContainer">
			<div class="emailWrapper">
				<img class="emailHeader" src="https://i.ibb.co/TWc5Rc1/Colored-Horizontal-Version3.png" alt="Colored-Horizontal-Version3">
				<p>Hello, {{$user->firstName}}.</p>
				<p>You had recently asked for change your password.</p>
				<p>If you already requested that so follow this link to complete your request and change password.
					<a href="https://evaluation-system.ieeehsb.org/password/reset/{{ $reset_code }}" target="_blank">https://evaluation-system.ieeehsb.org/password/reset/{{ $reset_code }}</a>
					if not you, so please ignore this mail and delete it.
				</p>
				<p>If you have any problems, feel free to contact with us <a href="mailto:hr.admin@ieeehsb.org">hr.admin@ieeehsb.org</a>.</p>
				<footer class="emailFooter">
					Follow US
					<ul>
						<li><a href="https://www.facebook.com/ieeehsb/"><img src="https://i.ibb.co/3dWpRYM/facebook.png" alt="facebook"></a></li>
						<li><a href="https://www.instagram.com/ieee_hsb/"><img src="https://i.ibb.co/F4Ptw5S/instagram.png" alt="instagram"></a></li>
					</ul>
				</footer>
			</div>
		</section>
		{{-- <h2>Password Reset</h2> --}}

		{{-- <div>
			{{$user->firstName}} ! To reset your password, complete this form: {{ URL::to('/api/password/reset', $reset_code) }}.<br/>
		</div> --}}
	</body>
</html>
