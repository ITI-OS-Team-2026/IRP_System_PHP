<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>شهادة اعتماد نهائي - IRB</title>
    <style>
        :root {
            --brand: #1a146b;
            --gold: #b89146;
            --ink: #1f2937;
            --muted: #6b7280;
            --paper: #ffffff;
            --bg: #eef2f8;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: "Times New Roman", "Georgia", serif;
        }

        .page-wrap {
            min-height: 100vh;
            padding: 28px 16px;
            display: grid;
            place-items: center;
        }

        .certificate {
            width: 210mm;
            min-height: 297mm;
            background: var(--paper);
            border: 2px solid var(--brand);
            outline: 8px solid rgba(26, 20, 107, 0.08);
            outline-offset: -18px;
            position: relative;
            padding: 22mm 20mm 20mm;
        }

        .certificate::before,
        .certificate::after {
            content: "";
            position: absolute;
            width: 28px;
            height: 28px;
            border: 2px solid var(--gold);
        }

        .certificate::before {
            top: 12px;
            right: 12px;
            border-left: 0;
            border-bottom: 0;
        }

        .certificate::after {
            bottom: 12px;
            left: 12px;
            border-right: 0;
            border-top: 0;
        }

        .top {
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10mm;
            margin-bottom: 10mm;
        }

        .org-ar {
            margin: 0;
            font-size: 20px;
            letter-spacing: 0.3px;
            color: var(--brand);
            font-weight: 700;
        }

        .org-en {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 14px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .title-ar {
            margin: 20px 0 6px;
            color: var(--brand);
            font-size: 35px;
            font-weight: 700;
        }

        .title-en {
            margin: 0;
            color: #374151;
            font-size: 18px;
            letter-spacing: 0.6px;
        }

        .cert-no {
            margin-top: 14px;
            color: var(--muted);
            font-size: 14px;
        }

        .statement {
            text-align: center;
            line-height: 1.9;
            font-size: 19px;
            margin: 0 0 10mm;
        }

        .statement strong {
            color: var(--brand);
            border-bottom: 1px solid #cbd5e1;
            padding: 0 2px;
        }

        .details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6mm;
            font-size: 15px;
        }

        .details td {
            border: 1px solid #d1d5db;
            padding: 10px 12px;
            vertical-align: top;
        }

        .details .label {
            width: 30%;
            font-weight: 700;
            color: #111827;
            background: #f8fafc;
        }

        .details .value {
            width: 70%;
            color: #1f2937;
        }

        .official-marks {
            margin-top: 14mm;
            display: none;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-end;
        }

        .seal-print {
            width: 135px;
            height: 135px;
            border: 3px solid rgba(26, 20, 107, 0.32);
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: rgba(26, 20, 107, 0.72);
            font-size: 12px;
            text-align: center;
            line-height: 1.5;
            font-weight: 700;
            transform: rotate(-14deg);
            position: relative;
        }

        .seal-print::before {
            content: "";
            position: absolute;
            inset: 9px;
            border: 1.5px solid rgba(26, 20, 107, 0.4);
            border-radius: 50%;
        }

        .signature-block {
            text-align: center;
            min-width: 260px;
        }

        .signature-script {
            font-family: "Brush Script MT", "Segoe Script", cursive;
            font-size: 34px;
            color: rgba(17, 24, 39, 0.9);
            margin-bottom: 2px;
            line-height: 1;
        }

        .signature-line {
            border-top: 1px solid #111827;
            margin-bottom: 6px;
            width: 100%;
        }

        .signature-name {
            font-weight: 700;
            color: #111827;
            font-size: 15px;
        }

        .signature-role {
            color: var(--muted);
            font-size: 13px;
            margin-top: 4px;
        }

        .print-actions {
            text-align: center;
            margin-top: 14px;
        }

        .print-actions button {
            border: 0;
            background: var(--brand);
            color: #fff;
            padding: 12px 22px;
            font-size: 14px;
            border-radius: 8px;
            cursor: pointer;
        }

        .print-actions button:hover {
            background: #241c7d;
        }

        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        @media print {
            body { background: #fff; }
            .page-wrap { padding: 0; min-height: auto; }
            .certificate {
                width: auto;
                min-height: auto;
                outline: none;
                border: 2px solid var(--brand);
                page-break-inside: avoid;
            }
            .official-marks { display: flex; }
            .print-actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="page-wrap">
        <section class="certificate">
            <header class="top">
                <p class="org-ar">اللجنة المؤسسية لأخلاقيات البحث العلمي (IRB)</p>
                <p class="org-en">Institutional Review Board</p>
                <h1 class="title-ar">شهادة اعتماد نهائي</h1>
                <p class="title-en">Certificate of Final Ethical Approval</p>
                <div class="cert-no">
                    رقم الشهادة: <?= htmlspecialchars($certificate['certificate_number'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            </header>

            <p class="statement">
                تشهد لجنة أخلاقيات البحث العلمي بأن هذا المقترح البحثي قد استوفى متطلبات
                <strong>الاعتماد النهائي</strong> وفق اللوائح المؤسسية المعتمدة.
            </p>

            <table class="details">
                <tr>
                    <td class="label">تاريخ الإصدار</td>
                    <td class="value"><?= htmlspecialchars(date('Y/m/d', strtotime($certificate['issued_at'])), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <td class="label">الرقم التسلسلي للبحث</td>
                    <td class="value"><?= htmlspecialchars($certificate['serial_number'] ?: ('SUB-' . (int) $certificate['id']), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <td class="label">عنوان البحث</td>
                    <td class="value"><?= htmlspecialchars($certificate['title'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <td class="label">اسم الباحث</td>
                    <td class="value"><?= htmlspecialchars($certificate['student_name'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <td class="label">الباحث الرئيسي</td>
                    <td class="value"><?= htmlspecialchars($certificate['principal_investigator'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <td class="label">القسم / التخصص</td>
                    <td class="value"><?= htmlspecialchars(($certificate['department'] ?: '-') . ' / ' . ($certificate['specialty'] ?: '-'), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            </table>

            <footer class="official-marks">
                <div class="seal-print">
                    ختم اللجنة<br/>IRB<br/>Approved
                </div>
                <div class="signature-block">
                    <div class="signature-script"><?= htmlspecialchars($_SESSION['user_name'] ?? 'IRB Manager', ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="signature-line"></div>
                    <div class="signature-name">مدير لجنة الاعتماد</div>
                    <div class="signature-role">Institutional Review Board Manager</div>
                </div>
            </footer>
        </section>
    </div>

    <div class="print-actions">
        <button onclick="window.print()">طباعة / حفظ PDF</button>
    </div>
</body>
</html>
