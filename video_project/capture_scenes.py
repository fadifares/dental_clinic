import os
from playwright.sync_api import sync_playwright

output_dir = os.path.abspath("video_project/scenes")
os.makedirs(output_dir, exist_ok=True)

CHROME_PATH = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
BASE_URL = "http://127.0.0.1:8000"

print("Starting browser for scene capture...")
with sync_playwright() as p:
    browser = p.chromium.launch(executable_path=CHROME_PATH, headless=True)
    context = browser.new_context(viewport={"width": 1920, "height": 1080}, locale="ar-SA")
    page = context.new_page()

    # Step 0: Login
    print("Navigating to login...")
    page.goto(f"{BASE_URL}/login")
    page.wait_for_load_state("networkidle")
    
    page.fill('input[name="email"]', "admin@dentalcare.com")
    page.fill('input[name="password"]', "Password123#")
    page.click('button[type="submit"]')
    page.wait_for_load_state("networkidle")
    print("Logged in successfully! Current URL:", page.url)

    # Scene 1: Dashboard overview
    print("Capturing Scene 1 (Dashboard)...")
    page.goto(f"{BASE_URL}/clinic")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(1500)
    page.screenshot(path=os.path.join(output_dir, "scene_1_dashboard.png"))

    # Scene 2: Patient EMR list & Patient profile
    print("Capturing Scene 2 (Patient EMR List)...")
    page.goto(f"{BASE_URL}/clinic/patients")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(1500)
    page.screenshot(path=os.path.join(output_dir, "scene_2_patients_list.png"))

    print("Capturing Scene 2 (Patient Profile)...")
    page.goto(f"{BASE_URL}/clinic/patients/1")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(1500)
    page.screenshot(path=os.path.join(output_dir, "scene_2_patient_profile.png"))

    # Scene 3: Odontogram interactive tab
    print("Capturing Scene 3 (Odontogram)...")
    odontogram_tab = page.query_selector('#odontogram-tab')
    if odontogram_tab:
        odontogram_tab.click()
        page.wait_for_timeout(2000)
    page.screenshot(path=os.path.join(output_dir, "scene_3_odontogram.png"))

    # Scene 4: Appointments tab on patient profile
    print("Capturing Scene 4 (Patient Appointments Tab)...")
    appointments_tab = page.query_selector('#appointments-tab')
    if appointments_tab:
        appointments_tab.click()
        page.wait_for_timeout(1500)
    page.screenshot(path=os.path.join(output_dir, "scene_4_appointments.png"))

    # Also capture main clinic appointments view
    print("Capturing Scene 4 (Main Appointments & Calendar)...")
    page.goto(f"{BASE_URL}/clinic/appointments")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(1500)
    page.screenshot(path=os.path.join(output_dir, "scene_4_appointments_main.png"))

    # Scene 5: Billing & Invoices
    print("Capturing Scene 5 (Billing)...")
    page.goto(f"{BASE_URL}/clinic/billing")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(1500)
    page.screenshot(path=os.path.join(output_dir, "scene_5_billing.png"))

    # Scene 6: Profile & Security settings
    print("Capturing Scene 6 (Security & Profile)...")
    page.goto(f"{BASE_URL}/clinic/profile")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(1500)
    page.screenshot(path=os.path.join(output_dir, "scene_6_security.png"))

    # Scene 7: Clinic Analytics / Reports
    print("Capturing Scene 7 (Analytics / Reports)...")
    page.goto(f"{BASE_URL}/clinic/reports")
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(1500)
    page.screenshot(path=os.path.join(output_dir, "scene_7_reports.png"))

    browser.close()
    print("ALL 7 SCENE SCREENSHOTS CAPTURED SUCCESSFULLY!")
