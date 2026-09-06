<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: 'Vazirmatn';
            font-style: normal;
            font-weight: 400;
            src: url('{{ $fontRegularDataUri }}') format('truetype');
            font-display: block;
        }

        @font-face {
            font-family: 'Vazirmatn';
            font-style: normal;
            font-weight: 700;
            src: url('{{ $fontBoldDataUri }}') format('truetype');
            font-display: block;
        }

        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            font-family: 'Vazirmatn', sans-serif;
            direction: rtl;
            text-align: right;
            unicode-bidi: embed;
            color: #1c1917;
            font-size: 12px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        h1 {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 4px;
        }

        .subtitle {
            color: #78716c;
            font-size: 11px;
            margin-bottom: 20px;
        }

        .summary-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            direction: rtl;
        }

        .summary-grid td {
            border: 1px solid #e7e5e4;
            padding: 10px 12px;
            width: 33.33%;
            text-align: right;
        }

        .summary-label {
            color: #78716c;
            font-size: 10px;
            display: block;
            margin-bottom: 4px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: 700;
        }

        .num {
            direction: ltr;
            unicode-bidi: isolate;
            display: inline-block;
        }

        table.daily {
            width: 100%;
            border-collapse: collapse;
            direction: rtl;
        }

        table.daily th,
        table.daily td {
            border: 1px solid #e7e5e4;
            padding: 8px 10px;
            text-align: right;
        }

        table.daily th {
            background: #f5f5f4;
            font-weight: 700;
            font-size: 11px;
            color: #57534e;
        }

        table.daily td.num-cell {
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>کاربان — گزارش دوره‌ای کارمند</h1>
    <p class="subtitle">
        {{ $history->summary->userName }} —
        از <span class="num">{{ $jalaliFrom }}</span> تا <span class="num">{{ $jalaliTo }}</span>
    </p>

    <table class="summary-grid">
        <tr>
            <td>
                <span class="summary-label">ساعات کار</span>
                <span class="summary-value"><span class="num">{{ $history->summary->hoursWorked }}</span></span>
            </td>
            <td>
                <span class="summary-label">کل تسک‌ها</span>
                <span class="summary-value"><span class="num">{{ $history->summary->plannedCount }}</span></span>
            </td>
            <td>
                <span class="summary-label">انجام‌شده</span>
                <span class="summary-value"><span class="num">{{ $history->summary->doneCount }}</span></span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="summary-label">انجام‌نشده</span>
                <span class="summary-value"><span class="num">{{ $history->summary->notDoneCount }}</span></span>
            </td>
            <td>
                <span class="summary-label">تسک‌های اضافه</span>
                <span class="summary-value"><span class="num">{{ $history->summary->extraCount }}</span></span>
            </td>
            <td>
                <span class="summary-label">درصد تکمیل</span>
                <span class="summary-value"><span class="num">{{ $history->summary->completionRate }}٪</span></span>
            </td>
        </tr>
    </table>

    <table class="daily">
        <thead>
            <tr>
                <th>تاریخ</th>
                <th>روز</th>
                <th>ساعات</th>
                <th>برنامه</th>
                <th>انجام‌شده</th>
                <th>انجام‌نشده</th>
                <th>اضافه</th>
                <th>وضعیت</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($history->days as $day)
                @php($jalali = app(\App\Services\JalaliDateFormatter::class))
                <tr>
                    <td><span class="num">{{ $jalali->date($day->date) }}</span></td>
                    <td>{{ $jalali->dayName($day->date) }}</td>
                    <td class="num-cell"><span class="num">{{ $day->hoursWorked }}</span></td>
                    <td class="num-cell"><span class="num">{{ $day->plannedCount }}</span></td>
                    <td class="num-cell"><span class="num">{{ $day->doneCount }}</span></td>
                    <td class="num-cell"><span class="num">{{ $day->notDoneCount }}</span></td>
                    <td class="num-cell"><span class="num">{{ $day->extraCount }}</span></td>
                    <td>{{ $day->status->label() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
