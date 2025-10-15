<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
*/

namespace AdvisingApp\Engagement\Database\Factories;

use AdvisingApp\Engagement\Enums\EngagementResponseStatus;
use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @extends Factory<EngagementResponse>
 */
class EngagementResponseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sender_type' => $this->faker->randomElement([
                (new Student())->getMorphClass(),
                (new Prospect())->getMorphClass(),
            ]),
            'sender_id' => function (array $attributes) {
                $senderClass = Relation::getMorphedModel($attributes['sender_type']);

                /** @var Student|Prospect $senderModel */
                $senderModel = new $senderClass();

                $sender = $senderClass === Student::class
                    ? Student::inRandomOrder()->first() ?? Student::factory()->create()
                    : $senderModel::factory()->create();

                return $sender->getKey();
            },
            'sent_at' => $this->faker->dateTimeBetween('-1 year', '-1 day'),
            'type' => $this->faker->randomElement(EngagementResponseType::cases()),
            'subject' => function (array $attributes) {
                return match ($attributes['type']) {
                    EngagementResponseType::Email => $this->faker->sentence(),
                    default => null,
                };
            },
            'content' => function (array $attributes) {
                assert($attributes['type'] instanceof EngagementResponseType);

                return match ($attributes['type']) {
                    EngagementResponseType::Sms => $this->faker->sentence(),
                    EngagementResponseType::Email => self::testEmailContent(),
                };
            },
            'raw' => function (array $attributes) {
                assert($attributes['type'] instanceof EngagementResponseType);

                return match ($attributes['type']) {
                    EngagementResponseType::Sms => $this->faker->sentence(),
                    EngagementResponseType::Email => self::testEmailRaw(),
                };
            },
            'status' => EngagementResponseStatus::New,
        ];
    }

    public function email(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => EngagementResponseType::Email,
            ];
        });
    }

    public function sms(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => EngagementResponseType::Sms,
            ];
        });
    }

    protected static function testEmailContent(): string
    {
        return <<<EOD
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <style type="text/css" style="display:none;"> P {margin-top:0;margin-bottom:0;} </style>
        </head>
        <body dir="ltr">
        <div class="elementToProof" style="font-family: Calibri, Helvetica, sans-serif; font-size: 12pt; color: rgb(0, 0, 0);">
        This is a test.</div>
        <div class="elementToProof" style="font-family: Calibri, Helvetica, sans-serif; font-size: 12pt; color: rgb(0, 0, 0);">
        <br>
        </div>
        <div id="Signature" class="elementToProof">
        <div style="font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 12pt; color: rgb(0, 0, 0);">
        <br>
        </div>
        <div style="font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 12pt; color: rgb(0, 0, 0);">
        Sincerely,</div>
        <div style="font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 12pt; color: rgb(0, 0, 0);">
        John Doe</div>
        <div style="font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 12pt; color: rgb(0, 0, 0);">
        <br>
        </div>
        <div style="font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 12pt; color: rgb(0, 0, 0);">
        ---</div>
        <div style="font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 12pt; color: rgb(0, 0, 0);">
        <br>
        </div>
        <div style="font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 14pt; color: rgb(0, 0, 0);">
        <b>John Doe</b></div>
        <div style="font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 10pt; color: rgb(0, 0, 0);">
        <i>Staff Member</i></div>
        <div style="font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 8pt; color: rgb(0, 0, 0);">
        <br>
        </div>
        <div style="font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 9pt; color: rgb(0, 0, 0);">
        <b>p: </b>(123) 123-1234 x 123</div>
        <div style="font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 9pt; color: rgb(0, 0, 0);">
        <b>e: </b>john.doe@test.com&nbsp;</div>
        <div style="font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 12pt; color: rgb(0, 0, 0);">
        <br>
        </div>
        <div style="font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 12pt; color: rgb(0, 0, 0);">
        <img data-outlook-trace="F:1|T:1" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAAAdCAYAAADvqyMCAAAACXBIWXMAAAzgAAAM4AFs5W3aAAAG
        Y0lEQVR4nO1cTY7jRBR+g7hAOACLZAELdu49g+QWG2QhrOQGTrbsOkdIdmw7ucFEQShigxJpYD+W
        ZgESLMYLDoC5QVCFr0bV1e+VqxLbcff4k6xOO2X71av3/8p5cTweqUePHjw+boovX/zys615v/3+
        9Tcvfa9P0mxMRDERRTg0ciIq8Hez266LWgnvUSuSNJsS0YCIFsx9SyJaqg+77XrJyIC69t6DHn2f
        crddr1wDkzQbEtEUMhVb91AydVDHbrtWn6kxD3KugiRpFoEpUdVYYFbFlB7tA8K9gHL4YrLbrjeG
        LPgqiIkcMpHbXyRppui587xPsduuR5Ue5PWff/1qnXr71eeffR9ItBfgNe4DmfqIET2uiyTNXhHR
        OJCIwlSOC6AM66skzW5223Wpb5Ok2T08hy+UJ/EKsb5sg9uG5whRjoKzFD2uB1jpUOVQqDMK0GHU
        KWyDbIUoh8JJWRvLQc6A5I4Li3lDLMCgZqZ6ATHsEGNz00q1SENk8Kq4NA+ra05JmsUVIczc+j8y
        lCnEe4zUnJM007kNJ/xD43PMfK/pycFLLVcReOrtQRoHGMtNQhF/yyzYDPEpy9QkzfQi3TFKp5P7
        R0khrt1btMzVWNC4sHOjJM0OGJMb595ZC0R45oR5Huf6H43F86ecdU7STBuRFSfcdczJE5JysHM3
        njc+R8kx1xlCc1fkweWzB0YGlpaxoI9CiWoIkkueSNZMJeb2d8qyJml2xKJLHkkxa5Gk2T+wxFWY
        Qhn3AqOVkL2BsGlwyifNkTt/sOZ1h+dL9xhivm8anJMTECxufOFSDvp/LevIPWyYssEpX8TNTymq
        9h50qYIc/3h5tA47ofcFt0h5qFWBxfMNDwZI5qpynqFnJcUcs+HogKUz/48ZJX5QqoRycCVSidZ9
        Q3OqgqSYjYbB4A83X/O5nEwMwKu9yxB0JQfhmHuuVVkGCtRUsPihGCorCgtUJmm2YUKn2JoXtzBm
        mXPgCFsOWGSbdzoun9U5J5+xDjpPEEJPE6fSqsez3iVp5vp+btG8EcJtwhrECCuXpvegDoVYdWKF
        RVFMeqEPIvpEsCSuBTOxMu4lWUXzXtwYWyG4kMm8biosqiphqtzshkl8CSGUTzUwdE4usM9rucqY
        Iyx/YPCgLByfTMSGR3k/l2enIMp6Q3hsJpUX9kxMBld6HAiG/byhzhHw1xY+u2wtJZfmGMnT+uQP
        QXN6AohgHLjcYgWvWhWCx2aY+uwURE1MxaWqHq8SdvPwFBoOB7MgEJAbubwIR4s9nrPKD5TOQUuV
        5T93ThJYwUPy3ia0gD8yLlCSEQyDS1EiHaafcpC/v/vJ3gLy76c/fvu2xUmVjDAEMzZwK0Eb2DDV
        tDGsNRde+eRdrfddPCHRFekqkplfwMrvz3zWSCu0Y/eFOndjXwijoMq5K4SwUm6i7jvTHuS1dfxw
        JuHnggt9xp5x9AmoZnRJOfRi2EIfwbrZFs5342XIToM2IYWvoR3sIKBEzHlqZ7kbofgSHoUzTJ0K
        sQ7MOWkH6HtY7lvqJ4w8EtEmwT2TExqOB2z93sEDE61uwUFexNEbG43bpuA0Ggi5WYWBERPloitl
        3o2gDLoaY/cVYiiEWpBbnHP2UhyNrEahBCdJs9yiz1YQaZs2J+RK4CIjUZeah9fYo7YUeicLNCbt
        LUMXAbIxFQyOLS8LrMMGvCmxNrEgeye56YSCYF/NUgiRxg4hMC0Wl8fctWC9fLCqaLpJuQeXwxC6
        3FIfhKQtJ01DKTkUgaNpGNCfqkJVH4QEnj4IbSvucVLmLlWxlhdaPZ8E91ovV7GddQOsi9d7jYRr
        YkEQyyuXbG878ArCpTwoO6cgEIbbCzvoLgVQ302EWL9RVMS5zi37SEJ9u+IlNnde7S1LJL83V8r3
        yNjgei4PCnODbKf6IGDuxENRtMAtjWsLlPU4yzFH9zm/onWVBKZSkIz6/UrwRLpTPOrK+zG77Xpm
        9Bwko6Tp1rT7bDORMEcX/YbhwczTgJpyckLnf7TBeveBrvUOxqVw1P1Hodau7vdBPjQwnXZRpvpf
        NWkBqLZwW8ud70n0uD669EbhswKslFaIqVDW7H9souPoFaQ5RBVlzYO9tbpH9/Act7s/BZSoqPXo
        OHoFaR85KiVPrtDwIaIPsZpDbpU3c1RLmnj/ukdD6KtYPXpIIKL/AImy+W979AlzAAAAAElFTkSu
        QmCC
        "></div>
        <div style="font-family: Calibri, Arial, Helvetica, sans-serif; font-size: 10pt; color: rgb(204, 204, 204);">
        <i>IMPORTANT: The contents of this email and any attachments are confidential. They are intended for the named recipient(s) only. If you have received this email by mistake, please notify the sender immediately and do not disclose the contents to anyone or
         make copies thereof.</i></div>
        </div>
        </body>
        </html>
        EOD;
    }

    protected static function testEmailRaw(): string
    {
        return <<<EOD
        Return-Path: <john.doe@test.com>
        Received: from NAM11-DM6-obe.outbound.protection.outlook.com (mail-dm6nam11on2133.outbound.protection.outlook.com [40.107.223.133])
         by inbound-smtp.us-west-2.amazonaws.com with SMTP id dbpqet3bruhfet4ldc88ufb6sk2as1qka5hf3jo1
         for prod@mail.advising.app;
         Fri, 11 Apr 2025 20:43:43 +0000 (UTC)
        X-SES-Spam-Verdict: PASS
        X-SES-Virus-Verdict: PASS
        Received-SPF: pass (spfCheck: domain of canyongbs.com designates 40.107.223.133 as permitted sender) client-ip=40.107.223.133; envelope-from=john.doe@canyongbs.com; helo=NAM11-DM6-obe.outbound.protection.outlook.com;
        testuthentication-Results: amazonses.com;
         spf=pass (spfCheck: domain of canyongbs.com designates 40.107.223.133 as permitted sender) client-ip=40.107.223.133; envelope-from=john.doe@canyongbs.com; helo=NAM11-DM6-obe.outbound.protection.outlook.com;
        testdmarc=pass header.from=canyongbs.com;
        X-SES-RECEIPT: AEFBQUFBQUFBQUFIbk5IekRWYzBWYjF4TGhoVmJqNTdvYnd0K21UTzRkVG9UUW1wNVh1NUtTa1N6TmlWOFIxSEtSamtUU1Y4RTZUcElPZVNmYUFFRzFUS1grT0dnYTVLb1d0Vm9SWURXQ253anZaRXRKTm5ValRTbXNEVEl2L05yTUlkcEdFYjVrSEtCSG9wM0tibk1XVFhEMm1rYWFpOWM0WUp1eWFJM2xZem41R1kzbmUzUlVwUG5UYmRLYXowcDVTcEw4K3QwWndyR0R5Qlh2aE9OM1gxRGRYUlFHc1ZtWkFiUm5XZDdzZXVoOUJnQjhjblZCcEV4WkJ0SmNyRGdOdHl4TGVtZm5WR09mQ3VlM3FINFNRWTBLcUxveENhVHZXVEptR0JWT1dINzFZN2hRRHRlRXc9PQ==
        X-SES-DKIM-SIGNATURE: a=rsa-sha256; q=dns/txt; b=SYF1xq9fT+gjuzK+R/mOcccUJLlKHZbphu/haELJmgEZdqz6d7La65+bL1QattbKc3016QhXEywH7iwHEhJSAPsXGTvlLAiMvotLiHi2d90g1ItbocFU8cdCEijkynMOdBzB9udwrkAabj7gk71VlezmElHgl5yCalxUE5vYmpI=; c=relaxed/simple; s=gdwg2y3kokkkj5a55z2ilkup5wp5hhxx; d=amazonses.com; t=1744404224; v=1; bh=VbeUt84mhcw8xVIb4nZlLvGfvvxI3ACngwwDsiRJqL0=; h=From:To:Cc:Bcc:Subject:Date:Message-ID:MIME-Version:Content-Type:X-SES-RECEIPT;
        ARC-Seal: i=1; a=rsa-sha256; s=arcselector10001; d=microsoft.com; cv=none;
         b=FB3+mSmWX3Dc4JZ7EgW3w1mVSoIzD10pAD+pDG7RGMYaCsFwW+Oq+3GNT9TKtXUkNy6VuH9BJkp1QRuXrDClOYWl32dTRNlgF7d8fbjs8aDH2sAfYmLM4QJt/C+D8AdOJZ6sWkoBTm58FwsV/1emP4t1FH+ZwThWQpi26lbP9oW2WRbkm+2rZDj5KKZYcaTR3jjmxV4RoIUSfLdZV4XCf3TdGYSaOd7XF01EiO4t6dcDyM327NAiCxMTrwPjA+EfIDfBnLgOnQs8TARNQ5/VPV3jGjrpoYUs+khlHiP6xZ5WSl7e4yEwaRYij8DfY13GRusTuupt65w5V1WNFnbRPg==
        ARC-Message-Signature: i=1; a=rsa-sha256; c=relaxed/relaxed; d=microsoft.com;
         s=arcselector10001;
         h=From:Date:Subject:Message-ID:Content-Type:MIME-Version:X-MS-Exchange-AntiSpam-MessageData-ChunkCount:X-MS-Exchange-AntiSpam-MessageData-0:X-MS-Exchange-AntiSpam-MessageData-1;
         bh=ZuRihjOmGwLkhoRfcXxyOnNBVh7f2XiuTEllrc89tOo=;
         b=Oja4wb31rUIMBWfrRQ4JijLC7EbGULS7xL0LDh29C3PbkK+HO9ECqOUPQJ4xpJBYLNJm39A/yE5nm1vur9RXYT3MKx2xSLNHLDazVikpCdYl/vAvbnwtA3kTbTFxhGPfW/1ThQHobccyEFkaYSD8sEuyk3Z+dpPEbe+7OFg1DF/L8NIbomW/3duB5MX0fBiitB0NX41utWzMQWuGqAFekwkl+9gUpyBnb4odT0tEcGOB2jIVz2J/CCIfzBb8oB9EbHfvfZiAMkHwgzmzxBuTOjIwRCRmeDCTJqGD/bOdengKto/VHxVQE5CEpfBrdOsDLIetkm2CRDi7kqDNtwW8sA==
        ARC-Authentication-Results: i=1; mx.microsoft.com 1; spf=pass
         smtp.mailfrom=canyongbs.com; dmarc=pass action=none
         header.from=canyongbs.com; dkim=pass header.d=canyongbs.com; arc=none
        Received: from PH0PR18MB4784.namprd18.prod.outlook.com (2603:10b6:510:cd::21)
         by LV3PR18MB6349.namprd18.prod.outlook.com (2603:10b6:408:26a::5) with
         Microsoft SMTP Server (version=TLS1_2,
         cipher=TLS_ECDHE_RSA_WITH_AES_256_GCM_SHA384) id 15.20.8606.34; Fri, 11 Apr
         2025 20:43:41 +0000
        Received: from PH0PR18MB4784.namprd18.prod.outlook.com
         ([fe80::7a52:7272:b41d:4d2c]) by PH0PR18MB4784.namprd18.prod.outlook.com
         ([fe80::7a52:7272:b41d:4d2c%3]) with mapi id 15.20.8632.025; Fri, 11 Apr 2025
         20:43:41 +0000
        From: "John Doe" <john.doe@test.com>
        To: "prod@mail.advising.app" <prod@mail.advising.app>
        Subject: Test Email #2
        Thread-Topic: Test Email #2
        Thread-Index: AQHbqyJHZVJEVH0RbUWth3OdGJS8RQ==
        Date: Fri, 11 Apr 2025 20:43:41 +0000
        Message-ID:
         <PH0PR18MB4784C0F3398D849E6B906C57B7B62@PH0PR18MB4784.namprd18.prod.outlook.com>
        Accept-Language: en-US
        Content-Language: en-US
        X-MS-Has-Attach: yes
        X-MS-TNEF-Correlator:
        msip_labels:
        authentication-results: dkim=none (message not signed)
         header.d=none;dmarc=none action=none header.from=canyongbs.com;
        x-ms-publictraffictype: Email
        x-ms-traffictypediagnostic: PH0PR18MB4784:EE_|LV3PR18MB6349:EE_
        x-ms-office365-filtering-correlation-id: d556eaed-e139-4630-039a-08dd79398b4f
        x-ms-exchange-atpmessageproperties: SA
        x-ms-exchange-senderadcheck: 1
        x-ms-exchange-antispam-relay: 0
        x-microsoft-antispam:
         BCL:0;ARA:13230040|1800799024|376014|31052699007|39142699007|366016|38070700018|4053099003|8096899003;
        x-microsoft-antispam-message-info:
         =?iso-8859-1?Q?9ptZ8UcQz/CU5ZMedirYP2h0u6AqUe0NsY0HlLu/oSeaqqN5E3MKtmD5fR?=
         =?iso-8859-1?Q?Bz9BUC7fMSBW4B+qxUQuXu0qKyaAB5Oh66kPsuE1vXmjLBrV3qDUqtexha?=
         =?iso-8859-1?Q?ruakp+x8UyJOs0//WuKNv2VCalS4z2jy614RRvL5coIqnIIMKTa8X9Ue3l?=
         =?iso-8859-1?Q?05fINPf8c5qbWX/s5DLng7iZSO7woM4C039FeN5IQTxL0bQjg4v0aW9GKy?=
         =?iso-8859-1?Q?CV3wLMN1T4Kh8iuXijMyNapXeerAQw/qnEGolqP2tFc+4RYW5VzRNlYLW4?=
         =?iso-8859-1?Q?kEN+7zcXnOBcumLxqG1Mc650dW1hHe6Sck4nVGLC88BqVEg6Wx2YHucZAe?=
         =?iso-8859-1?Q?fA3nt6Fbx6+p/ny3//ng9nY5UrKl5FZyWzzSF3ZrkUrk60SuiqXHGbLDz6?=
         =?iso-8859-1?Q?M4zAr9iND1VHR0H5ES0oWbtI2K2KoaLerHIAM5F2RCL91pK8hjeLje/vxb?=
         =?iso-8859-1?Q?fusPcWzLJbyOfWT/wSZfAHrmtiAjXF7vq8CeMKeqK6+Y9Q4ap2ja6rGJ2r?=
         =?iso-8859-1?Q?Jf4Eq8N3Lm/loyp2s/LimifCCc5BcQzVBrO9PuADHladJkFlgfzvB4aOhW?=
         =?iso-8859-1?Q?tO6JSatMYY91arML1MaYpA/h3mJPI2hor7wJM9XmDFmPdRdAWzJFNsx6WM?=
         =?iso-8859-1?Q?8sWTl62CooXXgQmqDBayERT8hXiB9lfLRxlZiiTTXX7MtXEjFuhre81vRf?=
         =?iso-8859-1?Q?uO/KVUJxXOlq7PRR7WNrEyAtGalw21pbuwhtyVMwPdzLayiTzjTg576/RX?=
         =?iso-8859-1?Q?5ZN6YExgjmnLpuQnUM5zmk5BIe9fTznpWe47AX9Bn2leMaMsMi8sYXWFip?=
         =?iso-8859-1?Q?vCHZB/lnLByq/UKQKQmHpRBOf0DYsTclPRExhMcaP7Ecps1547xWD7cCrL?=
         =?iso-8859-1?Q?c/ZrTQ9dWtYRzHGP7CAj8dH3YwQf0M5gfofGa0ZAqb7W+lE+ZynOvTnQO/?=
         =?iso-8859-1?Q?LDwyITzxpifBWlhW1OKaEY/WKZw44OJLFfRsxggkTmBicGjJNDsS0nVXmf?=
         =?iso-8859-1?Q?PPMZlF8AeqxDmmF2PQWkQDf295xxBC48QjPaFW6SMSlu4JPWZbU8KVPWUa?=
         =?iso-8859-1?Q?d5cZ5ary63unvt65ngCfs2nVtqSCgP56c5B5LTNbNsWwyGTXLwJ9kOLbKU?=
         =?iso-8859-1?Q?7uFFO52F0YXzzjKwsV2uVly7mIjTnF9RjjDb+WwUapjjna9EdYaT2ROIdm?=
         =?iso-8859-1?Q?u4Snvp14xfBJhHvMpCnsfcWvPEm7z04U35f4BNAEIUvuXFUyNMMoMXl3sk?=
         =?iso-8859-1?Q?cnUmqTinaM3pD98xrL+w/qj/Idv6F5z8+o2I3JdGAWByJt2GyS7RqylGri?=
         =?iso-8859-1?Q?sY9psfUkE/K8VbfDY3iJdqV9m8QcqHj3Z+6/6UHDs2IaRKwuk2C9rTCbtL?=
         =?iso-8859-1?Q?IuJIeAkMka2/efBu2JVc6BMYhFOV4F0v+rwmlfR8USCcTTk9hyH+8RC9tq?=
         =?iso-8859-1?Q?jzpxG/pAX+FTvZ1bHvu3BXKBSMbnXVmH7y/v0aesVFgPJBsAa2U/G2pezQ?=
         =?iso-8859-1?Q?QO2faJRBDz5ggkCvsJIof6vgI6n55yn6izsfQXW2/p9Q=3D=3D?=
        x-forefront-antispam-report:
         CIP:255.255.255.255;CTRY:;LANG:en;SCL:1;SRV:;IPV:NLI;SFV:NSPM;H:PH0PR18MB4784.namprd18.prod.outlook.com;PTR:;CAT:NONE;SFS:(13230040)(1800799024)(376014)(31052699007)(39142699007)(366016)(38070700018)(4053099003)(8096899003);DIR:OUT;SFP:1102;
        x-ms-exchange-antispam-messagedata-chunkcount: 1
        x-ms-exchange-antispam-messagedata-0:
         =?iso-8859-1?Q?JRDClqUlYvWVWQKR4JfYo3wg7omu178eCaxxTvO7wu5sAz5z8ADjKFbXYt?=
         =?iso-8859-1?Q?AFRO8oD97wWhre3VXkSYU7CT/ZZeO5IrjUWInKCxFoQn/StYUefhKXNSKY?=
         =?iso-8859-1?Q?XMgXRAZvbypaGOeRVqUxfOf/hrI8Pi10Xk2GR4xoXl5mC1r/4kAp0EaAXG?=
         =?iso-8859-1?Q?PdGzGxcgjQVPcSPPwMJXNkZ+WaiuRyVej4UGpAhDbLfhw+x014mz8v3Q/M?=
         =?iso-8859-1?Q?pBFMU6rBP8lANlqfPT+/M+edfB03PAkfJfso/mTSW1waONg6wiNKt3MLjj?=
         =?iso-8859-1?Q?VcWU89xj3eBAsnSDJwAtznrs5IQ3gfFzaKdrWX64/EtOim9dJRjfCaaw+Y?=
         =?iso-8859-1?Q?zUFyPseYooElR+wnrJOpJkl/3kWFcbNCKqvtt+6MmcEqjtjwwWyHwgK2kV?=
         =?iso-8859-1?Q?xd0aTfDE5qXzpzb0jH+M4gxEj4rhwR2WtXj1IaU5IDsI1UXuc2VqK9wiqB?=
         =?iso-8859-1?Q?oIDeC6vvNP4Kzd281KJ2UpGJQhKcAm5yo20IFMS/4T3vTg1cMy85FKnqhb?=
         =?iso-8859-1?Q?jvP3y31pGLT8PKojNeT9ATIhRNgfJIyZfTt1AvqkNIS6Mhm5Sc2xPNUSl2?=
         =?iso-8859-1?Q?HyOkPG8CMVjL+oFgiZVpCm+3Ra65ND8uqQVohawSFcTKbOEebDNkmKO5tY?=
         =?iso-8859-1?Q?hUS7gcu+ybIwrzqPgn5Ms2W1n7vuYH9fx7W3lK3qyFcwmsUhdvozCvXSDI?=
         =?iso-8859-1?Q?4FM3OYYLWXycz+1sYFTG4heLLvTTXW20X8Ee01PiGdnPViNsUiVWBOBHG5?=
         =?iso-8859-1?Q?AKk9OknGv6lYwP4l9yJZl2/IcRFd9Yzy3hT6ip7e52S+FQn+kt4vUpXqkP?=
         =?iso-8859-1?Q?c78o3N2nObIaRkU1CDJB7vBPPLErd1zLSEnEiI9ASHL1Qib/SIIG1YcFic?=
         =?iso-8859-1?Q?/TpOU4Tj1Bk5T2Dgg+EylfFBYIozr2nY3q6s37abKldZ9U2HKx2rvvIL7O?=
         =?iso-8859-1?Q?qaVjQ9C023klVL7Q5WpzCUHPo9aWW80hL4vHQN3Qbhuf6CCwoyDWuzCrBM?=
         =?iso-8859-1?Q?O3tMj8dZTnpernSwNwc6D7qqzf/DnxEcVrjZ6wjfvbKN7yis2sB8+EDi4Q?=
         =?iso-8859-1?Q?lsc56pXLEef5J+4/CMchP6z93smQkCxijWV8v0ePx1xetFFGIb9pEZsF7O?=
         =?iso-8859-1?Q?xDtlHuP99suCRyN9WyfA+Bzd0cdQikcGNNFgVUd6vI+nBg2tTYLnDMrISL?=
         =?iso-8859-1?Q?VTU89WKCpsQ3tGGDf5uJhBS712eOx99JWqc6tWvu51xLZ+YcjmX6a7m1KZ?=
         =?iso-8859-1?Q?aCyYrPjPOq3KSGj13gkLpxQNIoPIa3vIIPfgbp7w5uSI3KI65PXYaprOMT?=
         =?iso-8859-1?Q?jsTQLrhzM1vOKwCy/CjVWu6bpknySflhtrKAvZm8GFiTDddClrljba/bHl?=
         =?iso-8859-1?Q?K8+jTQua/SRinWclPgVbjTEjVydpBKFY6AEtC9uFC+KhtVZMM86fdvyXbh?=
         =?iso-8859-1?Q?b3I+nFPMPbtd/AW6R8RV3Lj/6zFLcv3WEsnLa5+XPR0ZrmW/AgDn+i3muP?=
         =?iso-8859-1?Q?HT8ssaSU/JmkvUOB8FALLRUuf9TIOqJxbCTXpg4AiKlfARxVLPlT3JRWx+?=
         =?iso-8859-1?Q?EgXdOdvDK5eQWJIaTOMA6Q3PSsMTzcFFt0eNi1gLLb9UOOBoKi8oGKQ+3M?=
         =?iso-8859-1?Q?zrysBZVHcDw/w=3D?=
        Content-Type: multipart/related;
            boundary="_004_PH0PR18MB4784C0F3398D849E6B906C57B7B62PH0PR18MB4784namp_";
            type="multipart/alternative"
        MIME-Version: 1.0
        X-OriginatorOrg: canyongbs.com
        X-MS-Exchange-CrossTenant-AuthAs: Internal
        X-MS-Exchange-CrossTenant-AuthSource: PH0PR18MB4784.namprd18.prod.outlook.com
        X-MS-Exchange-CrossTenant-Network-Message-Id: d556eaed-e139-4630-039a-08dd79398b4f
        X-MS-Exchange-CrossTenant-originalarrivaltime: 11 Apr 2025 20:43:41.3311
         (UTC)
        X-MS-Exchange-CrossTenant-fromentityheader: Hosted
        X-MS-Exchange-CrossTenant-id: af905c0d-24ca-4c1b-86e8-e6ac7d45c7f1
        X-MS-Exchange-CrossTenant-mailboxtype: HOSTED
        X-MS-Exchange-CrossTenant-userprincipalname: KNXH5vHCygltQKgxVwT5xEq5x8AvL0H3fz/X7gYcLZZb0ObLf3QbLv3ZSEyVhsduWd1V8PrXCa5xsNqvkRDdkg==
        X-MS-Exchange-Transport-CrossTenantHeadersStamped: LV3PR18MB6349

        --_004_PH0PR18MB4784C0F3398D849E6B906C57B7B62PH0PR18MB4784namp_
        Content-Type: multipart/alternative;
            boundary="_000_PH0PR18MB4784C0F3398D849E6B906C57B7B62PH0PR18MB4784namp_"

        --_000_PH0PR18MB4784C0F3398D849E6B906C57B7B62PH0PR18MB4784namp_
        Content-Type: text/plain; charset="iso-8859-1"
        Content-Transfer-Encoding: quoted-printable

        This is a test.


        Sincerely,
        John Doe

        ---

        John Doe
        Staff Member

        p: (123) 123-1234 x 123
        e: john.doe@canyongbs.com
        test        [cid:8ae0e194-8be1-4069-9ab3-610a06e1a819]
        IMPORTANT: The contents of this email and any attachments are confidential.=
         They are intended for the named recipient(s) only. If you have received th=
        is email by mistake, please notify the sender immediately and do not disclo=
        se the contents to anyone or make copies thereof.

        --_000_PH0PR18MB4784C0F3398D849E6B906C57B7B62PH0PR18MB4784namp_
        Content-Type: text/html; charset="iso-8859-1"
        Content-Transfer-Encoding: quoted-printable

        <html>
        <head>
        <meta http-equiv=3D"Content-Type" content=3D"text/html; charset=3Diso-8859-=
        1">
        <style type=3D"text/css" style=3D"display:none;"> P {margin-top:0;margin-bo=
        ttom:0;} </style>
        </head>
        <body dir=3D"ltr">
        <div class=3D"elementToProof" style=3D"font-family: Calibri, Helvetica, san=
        s-serif; font-size: 12pt; color: rgb(0, 0, 0);">
        This is a test.</div>
        <div class=3D"elementToProof" style=3D"font-family: Calibri, Helvetica, san=
        s-serif; font-size: 12pt; color: rgb(0, 0, 0);">
        <br>
        </div>
        <div id=3D"Signature" class=3D"elementToProof">
        <div style=3D"font-family: Calibri, Arial, Helvetica, sans-serif; font-size=
        : 12pt; color: rgb(0, 0, 0);">
        <br>
        </div>
        <div style=3D"font-family: Calibri, Arial, Helvetica, sans-serif; font-size=
        : 12pt; color: rgb(0, 0, 0);">
        Sincerely,</div>
        <div style=3D"font-family: Calibri, Arial, Helvetica, sans-serif; font-size=
        : 12pt; color: rgb(0, 0, 0);">
        John Doe</div>
        <div style=3D"font-family: Calibri, Arial, Helvetica, sans-serif; font-size=
        : 12pt; color: rgb(0, 0, 0);">
        <br>
        </div>
        <div style=3D"font-family: Calibri, Arial, Helvetica, sans-serif; font-size=
        : 12pt; color: rgb(0, 0, 0);">
        ---</div>
        <div style=3D"font-family: Calibri, Arial, Helvetica, sans-serif; font-size=
        : 12pt; color: rgb(0, 0, 0);">
        <br>
        </div>
        <div style=3D"font-family: Calibri, Arial, Helvetica, sans-serif; font-size=
        : 14pt; color: rgb(0, 0, 0);">
        <b>John Doe</b></div>
        <div style=3D"font-family: Calibri, Arial, Helvetica, sans-serif; font-size=
        : 10pt; color: rgb(0, 0, 0);">
        <i>Founder &amp; CEO</i></div>
        <div style=3D"font-family: Calibri, Arial, Helvetica, sans-serif; font-size=
        : 8pt; color: rgb(0, 0, 0);">
        <br>
        </div>
        <div style=3D"font-family: Calibri, Arial, Helvetica, sans-serif; font-size=
        : 9pt; color: rgb(0, 0, 0);">
        <b>p: </b>(520) 357-1351 x 101</div>
        <div style=3D"font-family: Calibri, Arial, Helvetica, sans-serif; font-size=
        : 9pt; color: rgb(0, 0, 0);">
        <b>e: </b>john.doe@test.com&nbsp;</div>
        <div style=3D"font-family: Calibri, Arial, Helvetica, sans-serif; font-size=
        : 12pt; color: rgb(0, 0, 0);">
        <br>
        </div>
        <div style=3D"font-family: Calibri, Arial, Helvetica, sans-serif; font-size=
        : 12pt; color: rgb(0, 0, 0);">
        <img data-outlook-trace=3D"F:1|T:1" src=3D"cid:8ae0e194-8be1-4069-9ab3-610a=
        06e1a819"></div>
        <div style=3D"font-family: Calibri, Arial, Helvetica, sans-serif; font-size=
        : 10pt; color: rgb(204, 204, 204);">
        <i>IMPORTANT: The contents of this email and any attachments are confidenti=
        al. They are intended for the named recipient(s) only. If you have received=
         this email by mistake, please notify the sender immediately and do not dis=
        close the contents to anyone or
         make copies thereof.</i></div>
        </div>
        </body>
        </html>

        --_000_PH0PR18MB4784C0F3398D849E6B906C57B7B62PH0PR18MB4784namp_--

        --_004_PH0PR18MB4784C0F3398D849E6B906C57B7B62PH0PR18MB4784namp_
        Content-Type: image/png; name="Outlook-yat0pj1v.png"
        Content-Description: Outlook-yat0pj1v.png
        Content-Disposition: inline; filename="Outlook-yat0pj1v.png"; size=1713;
            creation-date="Fri, 11 Apr 2025 20:43:41 GMT";
            modification-date="Fri, 11 Apr 2025 20:43:41 GMT"
        Content-ID: <8ae0e194-8be1-4069-9ab3-610a06e1a819>
        Content-Transfer-Encoding: base64

        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
        QmCC

        --_004_PH0PR18MB4784C0F3398D849E6B906C57B7B62PH0PR18MB4784namp_--
        EOD;
    }
}
