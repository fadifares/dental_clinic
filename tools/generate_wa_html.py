# -*- coding: utf-8 -*-
import json

with open('public/whatsapp-clinics.json', 'r', encoding='utf-8') as f:
    clinics = json.load(f)

clinics_json_str = json.dumps(clinics, ensure_ascii=False)

html_content = f"""<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>حملة واتساب والتواصل - Dental Pro ERP | Go-2Web</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    body {{
      font-family: 'Cairo', sans-serif;
    }}
    .custom-scrollbar::-webkit-scrollbar {{
      width: 6px;
      height: 6px;
    }}
    .custom-scrollbar::-webkit-scrollbar-thumb {{
      background-color: #cbd5e1;
      border-radius: 9999px;
    }}
  </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen">

  <!-- Header -->
  <header class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-700 text-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-2xl">🦷</span>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight">Dental Pro ERP | منصة المراسلة والصفحات الرقمية</h1>
          </div>
          <p class="text-emerald-100 text-xs sm:text-sm mt-1">
            إرسال مباشر عبر واتساب (Click-to-Chat) + استعراض صفحات التواصل الاجتماعي والمواقع الرسمية
          </p>
        </div>
        <div class="flex items-center gap-3">
          <a href="https://dental.go-2web.com" target="_blank" class="text-xs bg-white/10 hover:bg-white/20 transition px-3 py-1.5 rounded-lg border border-white/20 flex items-center gap-1.5 font-semibold">
            🌐 زيارة المنصة
          </a>
          <button onclick="exportProgress()" class="text-xs bg-emerald-500 hover:bg-emerald-400 text-white font-bold px-3 py-1.5 rounded-lg shadow transition flex items-center gap-1.5">
            📥 تصدير تقرير الإنجاز
          </button>
        </div>
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
      <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <div class="text-xs font-semibold text-slate-500 mb-1">إجمالي العيادات</div>
        <div class="text-2xl font-black text-slate-800" id="statTotal">0</div>
        <div class="text-[11px] text-slate-400 mt-1">عيادات ومراكز بالملف</div>
      </div>
      <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <div class="text-xs font-semibold text-slate-500 mb-1">أرقام واتساب جاهزة</div>
        <div class="text-2xl font-black text-emerald-600" id="statMobile">0</div>
        <div class="text-[11px] text-emerald-700 mt-1">أرقام محمول مصرية</div>
      </div>
      <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <div class="text-xs font-semibold text-slate-500 mb-1">صفحات تواصل ومواقع</div>
        <div class="text-2xl font-black text-indigo-600" id="statSocial">0</div>
        <div class="text-[11px] text-indigo-700 mt-1">فيسبوك، يوتيوب، مواقع</div>
      </div>
      <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <div class="text-xs font-semibold text-slate-500 mb-1">تم التواصل معهم</div>
        <div class="text-2xl font-black text-blue-600" id="statSent">0</div>
        <div class="text-[11px] text-blue-700 mt-1 font-semibold" id="statPercent">0% مكتمل</div>
      </div>
      <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 col-span-2 md:col-span-1">
        <div class="text-xs font-semibold text-slate-500 mb-1">المتبقي للإرسال</div>
        <div class="text-2xl font-black text-amber-600" id="statRemaining">0</div>
        <div class="text-[11px] text-amber-700 mt-1">عيادة بانتظار المراسلة</div>
      </div>
    </div>

    <!-- Progress Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6">
      <div class="flex justify-between items-center text-xs font-bold text-slate-600 mb-2">
        <span>مؤشر إنجاز الحملة</span>
        <span id="progressText">0 / 0</span>
      </div>
      <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
        <div id="progressBar" class="bg-gradient-to-r from-emerald-500 to-teal-500 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
      </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6 flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
      <div class="relative flex-1">
        <input 
          type="text" 
          id="searchInput" 
          placeholder="ابحث باسم العيادة، الطبيب، المحافظة، أو الهاتف..." 
          oninput="applyFilters()"
          class="w-full pr-10 pl-4 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm"
        >
        <span class="absolute right-3 top-3 text-slate-400">🔍</span>
      </div>

      <div class="flex flex-wrap gap-2">
        <select id="categoryFilter" onchange="applyFilters()" class="px-3 py-2 rounded-lg border border-slate-300 text-xs font-semibold bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
          <option value="all">كل الفئات</option>
          <option value="الفئة A">الفئة A (مراكز كبرى)</option>
          <option value="الفئة B">الفئة B (عيادات نشطة)</option>
          <option value="الفئة C">الفئة C (عيادات ناشئة)</option>
          <option value="شراكة">جهات ومؤسسات شراكة</option>
        </select>

        <select id="socialFilter" onchange="applyFilters()" class="px-3 py-2 rounded-lg border border-slate-300 text-xs font-semibold bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
          <option value="all">كل الروابط</option>
          <option value="social">صفحات فيسبوك / سوشيال ميديا فقط (20)</option>
          <option value="website">مواقع إلكترونية رسمية (25)</option>
          <option value="has_any">لديها أي رابط (سوشيال أو موقع)</option>
        </select>

        <select id="statusFilter" onchange="applyFilters()" class="px-3 py-2 rounded-lg border border-slate-300 text-xs font-semibold bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
          <option value="all">كل الحالات</option>
          <option value="pending">لم يتم الإرسال (المتبقي)</option>
          <option value="sent">تم التواصل معهم</option>
        </select>

        <select id="phoneTypeFilter" onchange="applyFilters()" class="px-3 py-2 rounded-lg border border-slate-300 text-xs font-semibold bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
          <option value="all">كل الأرقام</option>
          <option value="mobile" selected>أرقام واتساب فقط (محمول)</option>
        </select>
      </div>
    </div>

    <!-- Results Counter -->
    <div class="flex justify-between items-center mb-4 text-xs font-bold text-slate-500">
      <div>المعروض حالياً: <span id="displayedCount" class="text-slate-900">0</span> عيادة</div>
      <div class="text-[11px] text-slate-400">اضغط على زر السوشيال أو الموقع لاستعراض الصفحة الرسمية للعيادة</div>
    </div>

    <!-- Clinics Grid -->
    <div id="clinicsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <!-- Generated by JS -->
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="hidden text-center py-16 bg-white rounded-xl border border-slate-200">
      <span class="text-4xl block mb-2">🔍</span>
      <h3 class="text-base font-bold text-slate-700">لا توجد عيادات مطابقة للبحث</h3>
      <p class="text-xs text-slate-400 mt-1">جرّب تغيير خيارات التصفية أو مسح عبارة البحث</p>
    </div>

  </main>

  <footer class="bg-white border-t border-slate-200 py-6 mt-12 text-center text-xs text-slate-400">
    Dental Pro ERP - تطوير <a href="https://go-2web.com" target="_blank" class="text-emerald-600 font-bold hover:underline">Go-2Web</a> مصر | جميع الحقوق محفوظة
  </footer>

  <!-- Toast Notification -->
  <div id="toast" class="fixed bottom-5 left-5 bg-slate-900 text-white px-4 py-2.5 rounded-lg shadow-xl text-xs font-bold transition-all duration-300 opacity-0 pointer-events-none z-50 flex items-center gap-2">
    <span id="toastIcon">📋</span>
    <span id="toastMsg">تم النسخ بنجاح</span>
  </div>

  <script>
    const clinicsData = {clinics_json_str};
    const STORAGE_KEY = 'dental_wa_sent_status_v1';

    function getSentState() {{
      try {{
        const stored = localStorage.getItem(STORAGE_KEY);
        return stored ? JSON.parse(stored) : {{}};
      }} catch (e) {{
        return {{}};
      }}
    }}

    function saveSentState(state) {{
      try {{
        localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
      }} catch (e) {{}}
    }}

    function toggleSent(id) {{
      const state = getSentState();
      state[id] = !state[id];
      saveSentState(state);
      updateStats();
      applyFilters();
      showToast(state[id] ? 'تم تحديد العيادة كـ "تم التواصل" ✅' : 'تمت إعادة العيادة إلى قائمة الانتظار 🔄');
    }}

    function handleSendClick(id, waUrl) {{
      const state = getSentState();
      state[id] = true;
      saveSentState(state);
      updateStats();
      applyFilters();
      window.open(waUrl, '_blank');
    }}

    function copyText(text, label = 'النص') {{
      navigator.clipboard.writeText(text).then(() => {{
        showToast(`تم نسخ ${{label}} بنجاح! 📋`);
      }}).catch(() => {{
        showToast('تعذر النسخ التلقائي');
      }});
    }}

    function showToast(msg) {{
      const toast = document.getElementById('toast');
      document.getElementById('toastMsg').textContent = msg;
      toast.classList.remove('opacity-0', 'pointer-events-none');
      toast.classList.add('opacity-100');
      setTimeout(() => {{
        toast.classList.remove('opacity-100');
        toast.classList.add('opacity-0', 'pointer-events-none');
      }}, 2500);
    }}

    function getCategoryBadge(cat) {{
      if (cat.includes('الفئة A')) {{
        return '<span class="bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-bold px-2 py-0.5 rounded-md">الفئة A (كبرى)</span>';
      }} else if (cat.includes('الفئة B')) {{
        return '<span class="bg-amber-50 text-amber-700 border border-amber-200 text-[11px] font-bold px-2 py-0.5 rounded-md">الفئة B (نشطة)</span>';
      }} else if (cat.includes('الفئة C')) {{
        return '<span class="bg-blue-50 text-blue-700 border border-blue-200 text-[11px] font-bold px-2 py-0.5 rounded-md">الفئة C (فردية)</span>';
      }} else {{
        return '<span class="bg-purple-50 text-purple-700 border border-purple-200 text-[11px] font-bold px-2 py-0.5 rounded-md">مؤسسة / شراكة</span>';
      }}
    }}

    function getSocialLinkButton(c) {{
      if (!c.website) return '';
      
      if (c.website_type === 'facebook') {{
        return `
          <a href="${{c.website}}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-[11px] font-bold px-2.5 py-1 rounded-md transition" title="فتح صفحة فيسبوك للعيادة">
            <span>📘</span> فيسبوك العيادة
          </a>
        `;
      }} else if (c.website_type === 'youtube') {{
        return `
          <a href="${{c.website}}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-[11px] font-bold px-2.5 py-1 rounded-md transition" title="قناة يوتيوب">
            <span>📺</span> يوتيوب
          </a>
        `;
      }} else if (c.website_type === 'directory') {{
        return `
          <a href="${{c.website}}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-[11px] font-bold px-2.5 py-1 rounded-md transition" title="صفحة الدليل الطبي">
            <span>📖</span> صفحة الدليل
          </a>
        `;
      }} else {{
        return `
          <a href="${{c.website}}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 bg-cyan-50 hover:bg-cyan-100 text-cyan-800 border border-cyan-200 text-[11px] font-bold px-2.5 py-1 rounded-md transition" title="الموقع الرسمي للعيادة">
            <span>🌐</span> الموقع الرسمي
          </a>
        `;
      }}
    }}

    function updateStats() {{
      const state = getSentState();
      const total = clinicsData.length;
      const mobileCount = clinicsData.filter(c => c.is_mobile).length;
      const socialCount = clinicsData.filter(c => c.website).length;
      const sentCount = clinicsData.filter(c => state[c.id]).length;
      const remaining = mobileCount - clinicsData.filter(c => c.is_mobile && state[c.id]).length;
      const percent = mobileCount > 0 ? Math.round((clinicsData.filter(c => c.is_mobile && state[c.id]).length / mobileCount) * 100) : 0;

      document.getElementById('statTotal').textContent = total;
      document.getElementById('statMobile').textContent = mobileCount;
      document.getElementById('statSocial').textContent = socialCount;
      document.getElementById('statSent').textContent = sentCount;
      document.getElementById('statRemaining').textContent = Math.max(0, remaining);
      document.getElementById('statPercent').textContent = percent + '% مكتمل';

      document.getElementById('progressBar').style.width = percent + '%';
      document.getElementById('progressText').textContent = `${{clinicsData.filter(c => c.is_mobile && state[c.id]).length}} من أصل ${{mobileCount}} رقم واتساب`;
    }}

    function applyFilters() {{
      const query = document.getElementById('searchInput').value.trim().toLowerCase();
      const cat = document.getElementById('categoryFilter').value;
      const status = document.getElementById('statusFilter').value;
      const phoneType = document.getElementById('phoneTypeFilter').value;
      const socialFilter = document.getElementById('socialFilter').value;
      const state = getSentState();

      const filtered = clinicsData.filter(c => {{
        const isSent = !!state[c.id];

        // Search Query
        if (query) {{
          const matchName = c.name.toLowerCase().includes(query);
          const matchCity = c.city.toLowerCase().includes(query);
          const matchPhone = (c.phone_raw || '').includes(query);
          const matchAddress = (c.address || '').toLowerCase().includes(query);
          if (!matchName && !matchCity && !matchPhone && !matchAddress) return false;
        }}

        // Category Filter
        if (cat !== 'all') {{
          if (cat === 'شراكة' && !c.category.includes('شراكة') && !c.category.includes('مؤسسات')) return false;
          if (cat !== 'شراكة' && !c.category.includes(cat)) return false;
        }}

        // Social Filter
        if (socialFilter === 'social' && !['facebook', 'youtube', 'instagram'].includes(c.website_type)) return false;
        if (socialFilter === 'website' && c.website_type !== 'website') return false;
        if (socialFilter === 'has_any' && !c.website) return false;

        // Status Filter
        if (status === 'pending' && isSent) return false;
        if (status === 'sent' && !isSent) return false;

        // Phone Type
        if (phoneType === 'mobile' && !c.is_mobile) return false;

        return true;
      }});

      renderClinics(filtered);
    }}

    function renderClinics(list) {{
      const container = document.getElementById('clinicsContainer');
      const empty = document.getElementById('emptyState');
      const counter = document.getElementById('displayedCount');
      const state = getSentState();

      counter.textContent = list.length;

      if (list.length === 0) {{
        container.innerHTML = '';
        empty.classList.remove('hidden');
        return;
      }}

      empty.classList.add('hidden');

      let html = '';
      list.forEach(c => {{
        const isSent = !!state[c.id];
        const waLink = c.wa_phone ? `https://wa.me/${{c.wa_phone}}?text=${{encodeURIComponent(c.wa_msg)}}` : '#';
        const cardBg = isSent ? 'bg-emerald-50/50 border-emerald-300' : 'bg-white border-slate-200';

        html += `
          <div class="rounded-xl border p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between ${{cardBg}}">
            <div>
              <div class="flex items-start justify-between gap-2 mb-2">
                ${{getCategoryBadge(c.category)}}
                <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">📍 ${{c.city || 'مصر'}}</span>
              </div>

              <h3 class="text-sm font-extrabold text-slate-900 mb-1 leading-snug">
                ${{c.name}}
              </h3>

              <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 mb-3">
                ${{c.rating ? `<span class="flex items-center gap-1 font-bold text-amber-500">⭐ ${{c.rating}}</span>` : ''}}
                ${{c.reviews ? `<span class="text-slate-400">(${{c.reviews}} تقييم)</span>` : ''}}
                <span class="font-mono text-slate-600 font-semibold dir-ltr">${{c.phone_raw || 'بدون هاتف'}}</span>
              </div>

              <!-- Social link button if present -->
              <div class="mb-3">
                ${{getSocialLinkButton(c)}}
              </div>

              <div class="bg-slate-50 border border-slate-200 rounded-lg p-2.5 mb-3 text-[12px] text-slate-600 leading-relaxed max-h-24 overflow-y-auto custom-scrollbar">
                ${{c.wa_msg.replace(/\\n/g, '<br>')}}
              </div>
            </div>

            <div>
              <div class="flex items-center gap-2 mb-2">
                ${{c.is_mobile ? `
                  <button 
                    onclick="handleSendClick(${{c.id}}, '${{waLink}}')" 
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-3 rounded-lg text-xs shadow flex items-center justify-center gap-1.5 transition active:scale-95"
                  >
                    <span>💬</span> مراسلة واتساب
                  </button>
                ` : `
                  <div class="flex-1 bg-slate-200 text-slate-500 font-bold py-2 px-3 rounded-lg text-xs text-center cursor-not-allowed">
                    هاتف أرضي
                  </div>
                `}}

                <button 
                  onclick="copyText(\`${{c.wa_msg.replace(/`/g, '\\\\`').replace(/\\$/g, '\\\\$')}}\`, 'نص الرسالة')" 
                  title="نسخ نص الرسالة"
                  class="bg-slate-100 hover:bg-slate-200 text-slate-700 p-2 rounded-lg text-xs transition font-semibold"
                >
                  📋 نسخ
                </button>
              </div>

              <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                <label class="flex items-center gap-2 cursor-pointer font-semibold text-slate-600 text-[11px] select-none">
                  <input 
                    type="checkbox" 
                    ${{isSent ? 'checked' : ''}} 
                    onchange="toggleSent(${{c.id}})"
                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer"
                  >
                  <span>${{isSent ? '✅ تم التواصل' : 'لم يتم التواصل بعد'}}</span>
                </label>

                ${{c.phone_raw ? `
                  <button onclick="copyText('${{c.phone_raw}}', 'رقم الهاتف')" class="text-[11px] text-slate-400 hover:text-slate-600 underline">
                    نسخ الرقم
                  </button>
                ` : ''}}
              </div>
            </div>
          </div>
        `;
      }});

      container.innerHTML = html;
    }}

    function exportProgress() {{
      const state = getSentState();
      const rows = [
        ['ID', 'اسم العيادة', 'الفئة', 'المحافظة', 'الهاتف', 'الموقع أو صفحة التواصل', 'حالة التواصل']
      ];

      clinicsData.forEach(c => {{
        rows.push([
          c.id,
          `"${{c.name.replace(/"/g, '""')}}"`,
          `"${{c.category}}"`,
          `"${{c.city}}"`,
          `"${{c.phone_raw}}"`,
          `"${{c.website || ''}}"`,
          state[c.id] ? 'تم التواصل' : 'لم يتم'
        ]);
      }});

      const csvContent = '\\uFEFF' + rows.map(e => e.join(',')).join('\\n');
      const blob = new Blob([csvContent], {{ type: 'text/csv;charset=utf-8;' }});
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.setAttribute('href', url);
      link.setAttribute('download', `dental_whatsapp_campaign_${{new Date().toISOString().slice(0,10)}}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }}

    // Init
    updateStats();
    applyFilters();
  </script>
</body>
</html>
"""

with open('public/whatsapp.html', 'w', encoding='utf-8') as f:
    f.write(html_content)

print("Generated public/whatsapp.html with social links successfully!")
