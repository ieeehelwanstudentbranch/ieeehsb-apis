<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <link href="{{asset('css/email.css')}}" rel="stylesheet" type="text/css">
        <style>
            table{
                width: 95%;
                border: 1px solid #000;
                background: #eee;
                direction: ltr;
            }
            th{
                border-left: 1px solid #000;
            }
            td{
                border-left: 1px solid #000;
                border-top: 1px solid #000;
            }

        </style>
    </head>
    <body>
        <h2>Please verify This email address</h2>

        <div>
            Thanks for creating an account with the verification demo app. Please follow the link below to verify your email
            address {{ URL::to('api/register/verify/' . $confirmation_code) }}.<br/>
            User Data is
            <table>
                <tr>
                    <th>Name</th>
                    <th>Faculty</th>
                    <th>University</th>
                    <th>DOB</th>
                    <th>E-Mail</th>
                    <th>Position</th>
                    @if($user->position=='EX_com')
                        <th>EX Options</th>
                    @endif
                    @if($user->position != 'EX_com')
                        <th>Committee</th>
                    @endif
                    @if($user->position == 'highBoard' && ($user->committee == 'RAS' || 'PES' || 'WIE'))
                        <th>HB Options</th>
                    @endif

                </tr>
                <tr>
                    <td>{{$user->firstName .' ' .$user->lastName }}</td>
                    <td>{{$user->faculty}}</td>
                    <td>{{$user->university}}</td>
                    <td>{{$user->DOB}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->position}}</td>
                    @if($user->position == 'EX_com')
                    <td>{{$user->ex_com_option->ex_options}}</td>
                        @endif
                    @if($user->position != 'EX_com')
                    <td>{{$user->committee}}</td>
                         @endif
                    @if($user->position == 'highBoard' && ($user->committee == 'RAS' || 'PES' || 'WIE'))
                    <td>{{$user->high_board_option->HB_options}}</td>
                        @endif

                </tr>
            </table>

            {{--<table>--}}
                {{--                @foreach($user as $users)--}}
                {{--<tr>--}}
                    {{--<th>mohamed hamdu </th>--}}
                    {{--<th>mohamed hamdu</th>--}}
                    {{--<th>mohamed hamdu</th>--}}
                    {{--<th>mohamed hamdu</th>--}}
                    {{--<th>mohamed hamdu</th>--}}
                    {{--<th>mohamed hamdu</th>--}}
                    {{--@if($user->position)--}}
                        {{--<th>mohamed hamdu</th>--}}
                    {{--@endif--}}
                    {{--<th>mohamed hamdu</th>--}}
{{--                    @if($user->position == 'highBoard' && ($user->committee == 'RAS' || 'PES' || 'WIE'))--}}
                        {{--<th>mdksmfksdmfk</th>--}}
                    {{--@endif--}}

                {{--</tr>--}}
                {{--@endforeach--}}
            {{--</table>--}}

            If you have problems, please paste the above URL into your web browser.

        </div>

    </body>
</html>