{{--
    <COPYRIGHT>
    
    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.
    
    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.
    
    Notice:
    
    - You may not provide the software to third parties as a hosted or managed
    service, where the service provides users with access to any substantial set of
    the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
    in the software, and you may not remove or obscure any functionality in the
    software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
    of the licensor in the software. Any use of the licensor’s trademarks is subject
    to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
    same in return. Canyon GBS™ and Advising App™ are registered trademarks of
    Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
    vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
    Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
    in the Elastic License 2.0.
    
    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.
    
    </COPYRIGHT>
--}}
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Unsubscribe</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                display: flex;
                justify-content: center;
                margin: 0;
                padding: 0;
                background-color: #f9fafb;
                color: #374151;
            }
            .container {
                text-align: center;
                margin-top: 100px;
                max-width: 480px;
                padding: 0 20px;
            }
            h1 {
                font-size: 20px;
                font-weight: 600;
                margin-bottom: 16px;
            }

            p {
                font-size: 16px;
                line-height: 1.5;
                color: #6b7280;
                margin-bottom: 24px;
            }

            .btn {
                display: inline-block;
                padding: 10px 24px;
                font-size: 14px;
                font-weight: 500;
                color: #ffffff;
                background-color: #dc2626;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                text-decoration: none;
            }

            .btn:hover {
                background-color: #b91c1c;
            }
        </style>
    </head>
    <body>
        <div class="container">
            @if ($optedOut)
                <h1>You have been successfully unsubscribed.</h1>
            @else
                <h1>Unsubscribe</h1>
                <p>Are you sure you want to unsubscribe ?</p>
                <form method="POST" action="{{ $confirmUrl }}">
                    @csrf
                    <button type="submit" class="btn">Unsubscribe</button>
                </form>
            @endif
        </div>
    </body>
</html>
