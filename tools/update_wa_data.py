# -*- coding: utf-8 -*-
import csv
import io
import json
import re

with open('tools/generate_wa_data.py', 'r', encoding='utf-8') as f:
    orig_code = f.read()

start = orig_code.find('csv_data = """') + len('csv_data = """')
end = orig_code.find('"""\n\nreader =', start)
csv_data = orig_code[start:end]

reader = csv.DictReader(io.StringIO(csv_data))
items = []
seen = set()

for row in reader:
    name = row['اسم العيادة / المركز'].strip()
    category = row['التصنيف'].strip()
    city = row['المدينة / المصدر'].strip()
    phone_raw = row['رقم الهاتف'].strip()
    rating = row['التقييم'].strip()
    reviews = row['عدد المراجعات'].strip()
    address = row['العنوان'].strip()
    wa_msg = row['نص رسالة الواتساب'].strip()
    website = row['الموقع الإلكتروني'].strip() if 'الموقع الإلكتروني' in row else ''

    key = (name, phone_raw)
    if key in seen:
        continue
    seen.add(key)

    # Format phone for WhatsApp
    clean_digits = re.sub(r'\D', '', phone_raw)
    wa_phone = ""
    is_mobile = False

    if clean_digits.startswith('01') and len(clean_digits) == 11:
        wa_phone = '2' + clean_digits
        is_mobile = True
    elif clean_digits.startswith('1') and len(clean_digits) == 10:
        wa_phone = '20' + clean_digits
        is_mobile = True
    elif clean_digits.startswith('201') and len(clean_digits) == 12:
        wa_phone = clean_digits
        is_mobile = True
    elif clean_digits:
        if clean_digits.startswith('0'):
            wa_phone = '2' + clean_digits
        else:
            wa_phone = '20' + clean_digits
        is_mobile = False

    # Determine website type
    website_type = 'none'
    if website:
        w_lower = website.lower()
        if 'facebook.com' in w_lower or 'fb.com' in w_lower:
            website_type = 'facebook'
        elif 'youtube.com' in w_lower or 'youtu.be' in w_lower:
            website_type = 'youtube'
        elif 'instagram.com' in w_lower:
            website_type = 'instagram'
        elif any(d in w_lower for d in ['yellowpages.com.eg', 'vezeeta.com', 'healtheg.com', 'lovable.app']):
            website_type = 'directory'
        else:
            website_type = 'website'

    items.append({
        'id': len(items) + 1,
        'name': name,
        'category': category,
        'city': city,
        'phone_raw': phone_raw,
        'wa_phone': wa_phone,
        'is_mobile': is_mobile,
        'rating': rating,
        'reviews': reviews,
        'address': address,
        'website': website,
        'website_type': website_type,
        'wa_msg': wa_msg
    })

print(f"Total processed clinics: {len(items)}")
mobile_count = sum(1 for x in items if x['is_mobile'])
social_count = sum(1 for x in items if x['website_type'] in ['facebook', 'youtube', 'instagram'])
print(f"Clinics with valid mobile/WhatsApp: {mobile_count}")
print(f"Clinics with direct social media: {social_count}")

with open('public/whatsapp-clinics.json', 'w', encoding='utf-8') as f:
    json.dump(items, f, ensure_ascii=False, indent=2)
print("Updated public/whatsapp-clinics.json")
