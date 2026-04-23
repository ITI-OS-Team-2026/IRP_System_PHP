<?php
$pageTitle = $pageTitle ?? 'IRB Portal';
$includeTailwind = $includeTailwind ?? true;
$extraHeadContent = $extraHeadContent ?? '';
?>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&amp;family=Tajawal:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<?php if ($includeTailwind): ?>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script>
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "outline-variant": "#c8c5d3",
                    "surface-variant": "#e2e2e5",
                    "on-error": "#ffffff",
                    "surface-container-lowest": "#ffffff",
                    "on-primary-fixed-variant": "#3e3c8f",
                    "secondary": "#5c5f61",
                    "on-secondary-fixed-variant": "#444749",
                    "outline": "#777682",
                    "on-error-container": "#93000a",
                    "tertiary-container": "#5f2b00",
                    "on-surface-variant": "#474651",
                    "error-container": "#ffdad6",
                    "royal-indigo": "#312E81",
                    "background": "#f9f9fc",
                    "on-secondary-container": "#626567",
                    "primary-fixed-dim": "#c3c0ff",
                    "inverse-primary": "#c3c0ff",
                    "surface-dim": "#dadadc",
                    "inverse-on-surface": "#f0f0f3",
                    "on-tertiary-fixed-variant": "#70380b",
                    "tertiary-fixed": "#ffdbc7",
                    "secondary-fixed": "#e0e3e5",
                    "tertiary-fixed-dim": "#ffb688",
                    "primary-fixed": "#e2dfff",
                    "secondary-container": "#e0e3e5",
                    "on-tertiary-fixed": "#311300",
                    "on-secondary-fixed": "#191c1e",
                    "cool-slate": "#F8FAFC",
                    "surface-container": "#eeeef0",
                    "slate-gray": "#64748B",
                    "on-background": "#1a1c1e",
                    "surface-tint": "#5654a8",
                    "surface-container-high": "#e8e8ea",
                    "on-secondary": "#ffffff",
                    "error": "#ba1a1a",
                    "on-primary-fixed": "#100563",
                    "secondary-fixed-dim": "#c4c7c9",
                    "on-surface": "#1a1c1e",
                    "paper-white": "#FDFDFC",
                    "primary-container": "#312e81",
                    "on-primary-container": "#9c9af4",
                    "tertiary": "#3e1a00",
                    "surface-bright": "#f9f9fc",
                    "surface-container-highest": "#e2e2e5",
                    "primary": "#1a146b",
                    "surface-container-low": "#f3f3f6",
                    "crimson": "#991B1B",
                    "on-primary": "#ffffff",
                    "surface": "#f9f9fc",
                    "forest": "#166534",
                    "on-tertiary": "#ffffff",
                    "charcoal": "#1A1C1E",
                    "inverse-surface": "#2f3133",
                    "on-tertiary-container": "#de915e"
                },
                borderRadius: {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
                },
                spacing: {
                    "section-stack": "3rem",
                    "form-gap": "1.25rem",
                    "container-max": "1200px",
                    "edge-margin": "2rem",
                    "gutter": "1.5rem"
                },
                fontFamily: {
                    "numeral": ["Tajawal", "sans-serif"],
                    "display-md": ["Amiri", "serif"],
                    "button": ["Tajawal", "sans-serif"],
                    "h1": ["Amiri", "serif"],
                    "display-lg": ["Amiri", "serif"],
                    "body-sm": ["Tajawal", "sans-serif"],
                    "body-lg": ["Tajawal", "sans-serif"]
                },
                fontSize: {
                    "numeral": ["14px", { lineHeight: "1", letterSpacing: "0.02em", fontWeight: "500" }],
                    "display-md": ["32px", { lineHeight: "1.2", fontWeight: "700" }],
                    "button": ["15px", { lineHeight: "1", fontWeight: "700" }],
                    "h1": ["24px", { lineHeight: "1.4", fontWeight: "700" }],
                    "display-lg": ["40px", { lineHeight: "1.2", fontWeight: "700" }],
                    "body-sm": ["13px", { lineHeight: "1.5", fontWeight: "400" }],
                    "body-lg": ["16px", { lineHeight: "1.6", fontWeight: "500" }]
                }
            }
        }
    }
</script>
<?php endif; ?>
<?= $extraHeadContent ?>