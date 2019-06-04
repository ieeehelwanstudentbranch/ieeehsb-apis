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
            Please follow the link below to verify the applicant email
            address {{ URL::to('api/register/verify/' . $confirmation_code) }}.<br/>
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

                        @if($user->position == 'highBoard' && ($user->committee == 'RAS' || 'PES' || 'WIE'))
                            <th>HB Options</th>
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

                        @if($user->position == 'highBoard' && ($user->committee == 'RAS' || 'PES' || 'WIE'))
                            <td>{{$user->high_board_option->HB_options}}</td>
                        @endif
                    </tr>
                </tbody>
            </table>
            If you have problems, please paste the above URL into your web browser.
        </div>
    </body>
</html>