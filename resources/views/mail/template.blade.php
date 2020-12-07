<!doctype html>
<html>

<head>
    <title>TMI</title>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        .email-template img {
            border: none;
            max-width: 100%;
            -ms-interpolation-mode: bicubic;
        }

        body.email-template {
            margin: 0;
            padding: 0;
            font-size: 14px;
            line-height: 1.4;
            background-color: #f6f6f6;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
            font-family: "BlinkMacSystemFont", "-apple-system", "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", "Helvetica", "Arial", sans-serif;
        }

        .email-template table {
            width: 100%;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            border-collapse: separate;
        }

        .email-template table td {
            font-size: 14px;
            vertical-align: top;
        }

        .email-template .body {
            width: 100%;
            background-color: #f6f6f6;
        }

        .email-template .container {
            width: 580px;
            padding: 10px;
            display: block;
            max-width: 580px;
            margin: 0 auto !important;
        }

        .email-template .content {
            padding: 10px;
            display: block;
            margin: 0 auto;
            max-width: 580px;
            box-sizing: border-box;
        }

        .email-template .main {
            width: 100%;
            border-radius: 5px;
            background: #fff;
            /* border: 1px solid #eee; */
        }

        .email-template .wrapper {
            padding: 20px;
            box-sizing: border-box;
        }

        .email-template .content-block {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .email-template .footer {
            clear: both;
            width: 100%;
            margin-top: 10px;
            text-align: center;
        }

        .email-template .footer td,
        .email-template .footer p,
        .email-template .footer span,
        .email-template .footer a {
            color: #999;
            font-size: 12px;
            text-align: center;
        }

        .email-template h1,
        .email-template h2,
        .email-template h3,
        .email-template h4 {
            margin: 0;
            color: #000;
            font-weight: 400;
            line-height: 1.4;
            margin-bottom: 30px;
        }

        .email-template h1 {
            font-size: 35px;
            font-weight: 300;
            text-align: center;
            text-transform: capitalize;
        }

        .email-template p,
        .email-template ul,
        .email-template ol {
            margin: 0;
            font-size: 14px;
            font-weight: normal;
            margin-bottom: 15px;
        }

        .email-template p li,
        .email-template ul li,
        .email-template ol li {
            margin-left: 5px;
            list-style-position: inside;
        }

        .email-template a {
            color: #3498db;
            text-decoration: underline;
        }

        .email-template p:last-child {
            margin-bottom: 0;
        }

        .email-template .btn {
            width: 100%;
            margin: 30px 0;
            box-sizing: border-box;
        }

        .email-template .btn table {
            width: auto;
        }

        .email-template .btn table td {
            border-radius: 5px;
            text-align: center;
            background-color: #ffffff;
        }

        .email-template .btn a {
            margin: 0;
            color: #3498db;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
            letter-spacing: 1px;
            padding: .5rem 1rem;
            display: inline-block;
            text-decoration: none;
            box-sizing: border-box;
            background-color: #ffffff;
            border: solid 1px #3498db;
            text-transform: capitalize;
        }

        .email-template .btn-primary table td {
            background-color: #3498db;
        }

        .email-template .btn-primary a {
            color: #fff;
            background-color: #3273dc;
            border-color: transparent;
        }

        .email-template .btn-danger a {
            color: #fff;
            background-color: #ff3860;
            border-color: transparent;
        }

        .email-template .btn-success a {
            color: #fff;
            background-color: #23d160;
            border-color: transparent;
        }

        .email-template .last {
            margin-bottom: 0;
        }

        .email-template .first {
            margin-top: 0;
        }

        .email-template .align-center {
            text-align: center;
        }

        .email-template .align-right {
            text-align: right;
        }

        .email-template .align-left {
            text-align: left;
        }

        .email-template .clear {
            clear: both;
        }

        .email-template .mt0 {
            margin-top: 0;
        }

        .email-template .mb0 {
            margin-bottom: 0;
        }

        .email-template .powered-by a {
            text-decoration: none;
        }

        .email-template hr {
            border: 0;
            margin: 20px 0;
            border-bottom: 1px solid #f6f6f6;
        }

        @media only screen and (max-width: 620px) {
            .email-template table[class=body] h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important;
            }
            .email-template table[class=body] p,
            .email-template table[class=body] ul,
            .email-template table[class=body] ol,
            .email-template table[class=body] td,
            .email-template table[class=body] span,
            .email-template table[class=body] a {
                font-size: 16px !important;
            }
            .email-template table[class=body] .wrapper,
            .email-template table[class=body] .article {
                padding: 10px !important;
            }
            .email-template table[class=body] .content {
                padding: 0 !important;
            }
            .email-template table[class=body] .container {
                padding: 0 !important;
                width: 100% !important;
            }
            .email-template table[class=body] .main {
                border-radius: 0 !important;
                border-left-width: 0 !important;
                border-right-width: 0 !important;
            }
            .email-template table[class=body] .btn table {
                width: 100% !important;
            }
            .email-template table[class=body] .btn a {
                width: 100% !important;
            }
            .email-template table[class=body] .img-responsive {
                width: auto !important;
                height: auto !important;
                max-width: 100% !important;
            }
        }

        @media all {
            .email-template .ExternalClass {
                width: 100%;
            }
            .email-template .ExternalClass,
            .email-template .ExternalClass p,
            .email-template .ExternalClass td,
            .email-template .ExternalClass div,
            .email-template .ExternalClass font,
            .email-template .ExternalClass span {
                line-height: 100%;
            }
            .email-template .apple-link a {
                color: inherit !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                text-decoration: none !important;
            }
            .email-template .btn-primary table td:hover {
                background-color: #276cda !important;
            }
            .email-template .btn-primary a:hover {
                background-color: #276cda !important;
            }
        }
    </style>
</head>

<body class="email-template">
    <table border="0" cellpadding="0" cellspacing="0" class="body">
        <tr>
            <td>&nbsp;</td>
            <td class="container">
                <div class="content">

                    <img src="{{ asset('/images/default.png') }}" alt="" style="max-width:250px;margin:1rem auto;">

                    <div id="template">

                        <!-- Template Start -->
                        @yield('content')
                        <!-- Template End -->

                    </div>

                    <div class="footer">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="content-block">
                                    <span class="apple-link">Address: {{ $company->address }}, {{ $company->state_name }}, {{ $company->country_name }}</span>
                                    <br>{{ $company->phone ? 'Tel: '.$company->phone : '' }} {{ $company->email ? 'Email:
                                    '.$company->email : '' }} {{--
                                    <br> Don't like these emails? <a href="#">Unsubscribe</a>. --}}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>
</body>

</html>
