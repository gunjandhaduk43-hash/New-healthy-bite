import os
import subprocess
import time

CHROME_PATH = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
OUTPUT_DIR = r"D:\NEW healthy bite\Healthy_Bite_Screenshots"
TEMP_DIR = r"D:\temp_shots"

os.makedirs(OUTPUT_DIR, exist_ok=True)
os.makedirs(TEMP_DIR, exist_ok=True)

TARGETS = [
    # Customer Views
    ("fig_7_01_customer_welcome.png", "http://127.0.0.1:8000/menu/welcome"),
    ("fig_7_02_customer_menu.png", "http://127.0.0.1:8000/menu"),
    ("fig_7_03_customer_checkout.png", "http://127.0.0.1:8000/menu/checkout"),
    ("fig_7_04_customer_confirmation.png", "http://127.0.0.1:8000/menu/confirmation/HB-1001"),
    ("fig_7_05_customer_tracking.png", "http://127.0.0.1:8000/menu/tracking/HB-1001"),
    
    # Owner Views
    ("fig_7_06_owner_login.png", "http://127.0.0.1:8000/owner/login"),
    ("fig_7_07_owner_dashboard.png", "http://127.0.0.1:8000/owner/dashboard?dev_auth=owner"),
    ("fig_7_08_owner_live_orders.png", "http://127.0.0.1:8000/owner/orders?dev_auth=owner"),
    ("fig_7_09_owner_kitchen_kanban.png", "http://127.0.0.1:8000/owner/kitchen-orders?dev_auth=owner"),
    ("fig_7_10_owner_menu_management.png", "http://127.0.0.1:8000/owner/menu?dev_auth=owner"),
    ("fig_7_11_owner_tables_qr.png", "http://127.0.0.1:8000/owner/tables?dev_auth=owner"),
    ("fig_7_12_owner_reviews.png", "http://127.0.0.1:8000/owner/reviews?dev_auth=owner"),
    ("fig_7_13_owner_staff.png", "http://127.0.0.1:8000/owner/staff?dev_auth=owner"),
    ("fig_7_14_owner_analytics.png", "http://127.0.0.1:8000/owner/analytics?dev_auth=owner"),
    ("fig_7_15_owner_live_menu.png", "http://127.0.0.1:8000/owner/live-menu?dev_auth=owner"),
    ("fig_7_16_owner_profile.png", "http://127.0.0.1:8000/owner/profile?dev_auth=owner"),
    ("fig_7_17_owner_settings.png", "http://127.0.0.1:8000/owner/settings?dev_auth=owner"),
    
    # Admin Views
    ("fig_7_18_admin_login.png", "http://127.0.0.1:8000/admin/login"),
    ("fig_7_19_admin_dashboard.png", "http://127.0.0.1:8000/admin/dashboard?dev_auth=admin"),
    ("fig_7_20_admin_restaurants.png", "http://127.0.0.1:8000/admin/restaurants?dev_auth=admin"),
    ("fig_7_21_admin_portal_inspect.png", "http://127.0.0.1:8000/admin/portal-inspect?dev_auth=admin"),
    ("fig_7_22_admin_users.png", "http://127.0.0.1:8000/admin/users?dev_auth=admin"),
]

for filename, url in TARGETS:
    temp_file = os.path.join(TEMP_DIR, filename)
    dest_file = os.path.join(OUTPUT_DIR, filename)
    cmd = [
        CHROME_PATH,
        "--headless=new",
        "--no-sandbox",
        "--disable-gpu",
        "--window-size=1280,900",
        f"--screenshot={temp_file}",
        url
    ]
    print(f"Capturing: {filename} from {url} ...")
    try:
        subprocess.run(cmd, check=True, timeout=15)
        if os.path.exists(temp_file):
            # move to destination
            if os.path.exists(dest_file):
                os.remove(dest_file)
            os.rename(temp_file, dest_file)
            print(f"  ✓ Saved {filename} ({os.path.getsize(dest_file):,} bytes)")
        else:
            print(f"  ✗ Failed: {temp_file} not found")
    except Exception as e:
        print(f"  ✗ Error: {e}")

print("\nScreenshot capture process complete!")
