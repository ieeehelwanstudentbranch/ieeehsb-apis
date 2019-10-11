<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <link href="{{asset('css/email.css')}}" rel="stylesheet" type="text/css">
</head>
<body>
    <div>
        <header class="emailHeaer"></header>
        <h2>Please verify {{$user->firstName .' ' .$user->lastName}} account</h2>
        <p>
            Please follow the link below to verify the applicant email address <a href="http://evaluation-system.ieeehsb.org/verify/{{ $confirmation_code }}" target="_blank"> Active This Account </a>
            {{--<a href="{{ URL::to('api/register/verify/' . $confirmation_code) }}" target="_blank">Active This Account</a>--}}
        </p>    
        <p>If this an anonymous user, You can delete this account by clicking on this link <a href="{{URL::to('api/delete-user/' . encrypt($user->id)) }}">Delete</a>
            {{--<a href="http://localhost:3000/delete-user/{{ encrypt($user->id) }}">Delete</a> --}}
        </p>
        User Data is
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>E-Mail</th>
                    <th>Position</th>

                    @if($user->position=='EX_com')
                        <th>EX Options</th>
                    @endif

                    @if($user->position != 'EX_com')
                        <th>Committee</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>{{$user->firstName .' ' .$user->lastName }}</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->position}}</td>

                    @if($user->position == 'EX_com')
                        <td>{{$user->ex_com_option->ex_options}}</td>
                    @endif

                    @if($user->position != 'EX_com')
                        <td>{{$user->committee->name}}</td>
                    @endif
                </tr>
            </tbody>
        </table>
        <p>If you have problems, please paste the above URL into your web browser.</p>
    </div>
</body>
</html>