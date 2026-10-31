# -*- coding: utf-8 -*-
import csv, io, json

with open('tools/generate_wa_data.py', 'r', encoding='utf-8') as f:
    code = f.read()

start = code.find('csv_data = """') + len('csv_data = """')
end = code.find('"""', start)
csv_text = code[start:end]

reader = csv.DictReader(io.StringIO(csv_text))
social_links = []
official_websites = []
directory_links = []

for row in reader:
    name = row['اسم العيادة / المركز'].strip()
    website = row['الموقع الإلكتروني'].strip()
    category = row['التصنيف'].strip()
    city = row['المدينة / المصدر'].strip()
    phone = row['رقم الهاتف'].strip()
    if not website:
        continue
    
    w_lower = website.lower()
    is_social = any(s in w_lower for s in ['facebook.com', 'fb.com', 'instagram.com', 'youtube.com', 'tiktok.com', 'linkedin.com', 'twitter.com', 'x.com'])
    is_dir = any(d in w_lower for d in ['yellowpages.com.eg', 'vezeeta.com', 'healtheg.com', 'lovable.app'])
    
    item = {
        'name': name,
        'category': category,
        'city': city,
        'phone': phone,
        'url': website
    }
    
    if is_social:
        social_links.append(item)
    elif is_dir:
        directory_links.append(item)
    else:
        official_websites.append(item)

def dedup(lst):
    seen = set()
    res = []
    for x in lst:
        key = (x['name'], x['url'])
        if key not in seen:
            seen.add(key)
            res.append(x)
    return res

social_links = dedup(social_links)
official_websites = dedup(official_websites)
directory_links = dedup(directory_links)

res_data = {
    'social': social_links,
    'websites': official_websites,
    'directories': directory_links
}

with open('public/clinic_links.json', 'w', encoding='utf-8') as f:
    json.dump(res_data, f, ensure_ascii=False, indent=2)

print(f"Social: {len(social_links)}, Websites: {len(official_websites)}, Directories: {len(directory_links)}")
