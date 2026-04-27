<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>واجهة المراجعة العمياء - IRB Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&amp;family=Tajawal:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
          theme: {
            extend: {
              colors: {
                  "royal-indigo": "#312E81",
                  "primary": "#1a146b",
                  "charcoal": "#1A1C1E",
                  "secondary": "#64748B",
                  "crimson": "#991B1B",
                  "forest": "#166534"
              },
              fontFamily: {
                  "h1": ["Amiri"],
                  "body-lg": ["Tajawal"],
                  "numeral": ["Tajawal"],
                  "button": ["Tajawal"]
              }
            }
          }
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        body { font-family: 'Tajawal', sans-serif; background-color: #F8FAFC; }
    </style>
</head>
<body class="text-gray-900 font-body-lg min-h-screen flex flex-col pr-64 bg-gray-50">

<aside class="fixed inset-y-0 right-0 w-64 bg-white border-l border-gray-200 flex flex-col z-50 shadow-sm">
    <div class="p-6 border-b border-gray-200 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
            <span class="material-symbols-outlined text-indigo-700">account_balance</span>
        </div>
        <div>
            <h2 class="text-lg font-bold text-gray-900">مساحة المراجع</h2>
            <p class="text-xs text-gray-600">المراجعة العلمية</p>
        </div>
    </div>
    <nav class="flex-1 py-4 flex flex-col gap-1">
        <a class="bg-indigo-700 text-white font-bold px-6 py-3 flex items-center gap-3 rounded-lg transition-all" href="<?php echo BASE_URL; ?>/reviewer/dashboard">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">assignment</span>
            الأبحاث المعينة
        </a>
    </nav>
</aside>

<main class="flex-1 flex flex-col min-h-screen">
    <header class="flex justify-between items-center px-8 h-16 w-full border-b border-gray-200 bg-white z-40 sticky top-0 shadow-sm">
        <h1 class="text-xl font-bold text-gray-900">المراجعة السرية</h1>
        <div class="flex items-center gap-6 text-gray-600">
            <a href="<?php echo BASE_URL; ?>/reviewer/dashboard" class="flex items-center gap-2 hover:text-indigo-700 font-bold transition-colors">
                <span class="material-symbols-outlined">arrow_forward</span> العودة
            </a>
        </div>
    </header>

    <div class="flex-1 max-w-[1200px] w-full mx-auto p-8 pb-32">
        <?php if (isset($_SESSION['review_error'])): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-600 text-red-700 rounded-lg font-bold flex items-center gap-3">
                <span class="material-symbols-outlined">error</span>
                <?= htmlspecialchars($_SESSION['review_error']) ?>
                <?php unset($_SESSION['review_error']); ?>
            </div>
        <?php endif; ?>

        <header class="mb-8 border-b border-gray-200 pb-6">
            <div class="flex items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-gray-200 text-gray-600 text-xs border border-gray-300 uppercase font-bold rounded">Blind Review</span>
                <span class="px-2 py-1 bg-indigo-50 text-indigo-700 text-xs border border-indigo-200 font-bold rounded">رقم البحث: <?= htmlspecialchars($submission['id']) ?></span>
            </div>
            <h1 class="font-h1 text-3xl md:text-4xl text-gray-900 mb-4 leading-tight"><?= htmlspecialchars($submission['title']) ?></h1>
            <p class="text-gray-600 font-numeral">
                الرقم المرجعي (Serial): <span class="font-bold text-gray-900"><?= htmlspecialchars($submission['serial_number'] ?: 'قيد المعالجة') ?></span> |
                تاريخ التقديم: <span class="font-bold text-gray-900"><?= date('d/m/Y', strtotime($submission['created_at'])) ?></span>
            </p>
        </header>

        <section class="mb-8">
            <h2 class="font-h1 text-2xl text-gray-900 mb-2 flex items-center gap-2 font-bold">
                <span class="material-symbols-outlined">folder_open</span>
                الوثائق المرفقة
            </h2>
            <p class="text-sm text-gray-600 mb-6">في حالة إعادة المراجعة، يتم عرض الملفات التي تم تعديلها في آخر جولة فقط.</p>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <?php if(empty($documents)): ?>
                    <div class="col-span-2 p-8 border border-dashed border-gray-300 text-center text-gray-600 font-bold rounded-xl bg-gray-50">
                        لا توجد وثائق مرفقة مع هذا البحث.
                    </div>
                <?php else: ?>
                    <?php foreach ($documents as $doc): ?>
                        <div class="flex items-center p-4 bg-white border border-gray-200 hover:border-indigo-500 transition-all group rounded-xl shadow-sm hover:shadow-lg">
                            <div class="ml-4 w-12 h-12 bg-gray-100 flex items-center justify-center rounded-lg group-hover:bg-indigo-50 transition-colors text-gray-500 group-hover:text-indigo-700">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900"><?= htmlspecialchars($doc['document_type']) ?><?php if (isset($doc['version'])): ?> - V<?= (int) $doc['version'] ?><?php endif; ?></h3>
                                <p class="text-xs text-gray-600 mt-1 uppercase"><?= pathinfo($doc['file_path'], PATHINFO_EXTENSION) ?> FILE</p>
                            </div>
                            <a href="<?php echo BASE_URL; ?>/storage/<?php echo ltrim($doc['file_path'], '/'); ?>" target="_blank" class="text-gray-600 hover:text-indigo-700 p-2 border border-transparent hover:border-indigo-500 bg-gray-50 hover:bg-white transition-all rounded-lg">
                                <span class="material-symbols-outlined">download</span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Final Evaluation Form -->
        <section class="bg-white border border-gray-200 p-8 relative shadow-sm rounded-xl">
            <div class="absolute -top-3 right-6 bg-white px-3 font-bold text-indigo-700 border border-gray-200 uppercase tracking-widest text-xs py-1 rounded">
                Final Decision // التقييم النهائي
            </div>

            <form action="<?php echo BASE_URL; ?>/reviewer/submit-evaluation" method="POST" class="space-y-8 mt-4">
                <input type="hidden" name="submission_id" value="<?= (int)$submission['id'] ?>">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="flex flex-col items-center justify-center p-6 border border-gray-300 hover:bg-green-50 cursor-pointer transition-all group has-[:checked]:border-green-600 has-[:checked]:border-2 has-[:checked]:bg-green-50 rounded-xl">
                        <input class="sr-only" name="decision" type="radio" value="approve" required/>
                        <span class="material-symbols-outlined text-4xl mb-2 text-gray-400 group-has-[:checked]:text-green-600">check_circle</span>
                        <span class="font-bold text-gray-600 group-has-[:checked]:text-green-600">موافقة (Approve)</span>
                    </label>

                    <label class="flex flex-col items-center justify-center p-6 border border-gray-300 hover:bg-blue-50 cursor-pointer transition-all group has-[:checked]:border-indigo-700 has-[:checked]:border-2 has-[:checked]:bg-blue-50 rounded-xl">
                        <input class="sr-only" name="decision" type="radio" value="modify" required/>
                        <span class="material-symbols-outlined text-4xl mb-2 text-gray-400 group-has-[:checked]:text-indigo-700">edit_document</span>
                        <span class="font-bold text-gray-600 group-has-[:checked]:text-indigo-700">طلب تعديل (Modify)</span>
                    </label>

                    <label class="flex flex-col items-center justify-center p-6 border border-gray-300 hover:bg-red-50 cursor-pointer transition-all group has-[:checked]:border-red-600 has-[:checked]:border-2 has-[:checked]:bg-red-50 rounded-xl">
                        <input class="sr-only" name="decision" type="radio" value="reject" required/>
                        <span class="material-symbols-outlined text-4xl mb-2 text-gray-400 group-has-[:checked]:text-red-600">cancel</span>
                        <span class="font-bold text-gray-600 group-has-[:checked]:text-red-600">رفض (Reject)</span>
                    </label>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-bold text-gray-900 text-lg" for="reviewer_comments">
                        الملاحظات العلمية <span id="reviewer-comments-required" class="text-red-600 hidden">*</span>
                    </label>
                    <p class="text-sm text-gray-600 mb-2">الملاحظات اختيارية في الموافقة/الرفض، وإجبارية عند طلب التعديل.</p>
                    <textarea class="w-full bg-gray-50 border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 p-4 min-h-[180px] leading-relaxed rounded-lg transition-all"
                              id="reviewer_comments" name="reviewer_comments" placeholder="اكتب أسباب القرار والتعديلات المطلوبة (إن وجدت) بدقة هنا..."></textarea>
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-200">
                    <button class="bg-indigo-700 text-white font-bold px-12 py-4 hover:bg-indigo-800 transition-all flex items-center gap-3 border border-indigo-700 rounded-lg shadow-sm hover:shadow-lg hover:-translate-y-0.5" type="submit">
                        حفظ التقييم وإرساله
                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">send</span>
                    </button>
                </div>
            </form>
        </section>
    </div>
</main>
<script>
    (function () {
        const decisionInputs = document.querySelectorAll('input[name="decision"]');
        const commentsField = document.getElementById('reviewer_comments');
        const requiredMark = document.getElementById('reviewer-comments-required');

        const syncCommentsRequirement = () => {
            const selected = document.querySelector('input[name="decision"]:checked');
            const isModify = selected && selected.value === 'modify';
            commentsField.required = !!isModify;
            if (isModify) {
                requiredMark.classList.remove('hidden');
            } else {
                requiredMark.classList.add('hidden');
            }
        };

        decisionInputs.forEach((input) => {
            input.addEventListener('change', syncCommentsRequirement);
        });

        syncCommentsRequirement();
    })();
</script>
</body>
</html>
