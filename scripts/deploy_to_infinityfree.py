import os
import ftplib
import re
import sys

ftp_host = "ftpupload.net"
ftp_user = "if0_43060466"
ftp_pass = "Gunjan1230"

# Directories and files to synchronize to htdocs/
DEPLOY_TARGETS = [
    # Server & Performance configurations
    ".htaccess",
    
    # Core app files
    "app/Controllers/HomeController.php",
    "app/Core/App.php",
    
    # Views
    "resources/views/customer/menu.php",
    "resources/views/customer/welcome.php",
    "resources/views/customer/tracking.php",
    "resources/views/layouts/customer.php",
    "resources/views/partials/food-detail.php",
    
    # Routes
    "routes/web.php",
    
    # Public assets & rewrite rules
    "public/.htaccess",
    "public/index.php",
    "public/assets/js/state.js",
    "public/assets/js/utils.js",
    "public/assets/js/api.js",
    "public/assets/js/pricing.js",
    "public/assets/js/nutrition.js",
    "public/assets/js/food-details.js",
    "public/assets/js/cart.js",
    "public/assets/js/checkout.js",
    "public/assets/js/live-kitchen.js",
    "public/assets/js/menu.js",
    "public/assets/js/app.js",
    "public/assets/js/order.js",
    "public/assets/js/dashboard.js",
    "public/assets/css/customer.css",
    "public/assets/css/responsive.css",
    "public/assets/css/checkout.css",
    "public/assets/css/food-details.css",
    "public/assets/css/dashboard.css",
    "public/assets/css/variables.css",
    
    # Database dumps & seeders
    "database/healthy_bite_dump.sql",
    "database/healthy_bite_infinityfree_dump.sql",
    "database/infinityfree_healthy_bite_import.sql",
    "database/seeders/sample_data.sql",
    
    # New Basil Valley assets
    "storage/basil_valley_menu_import.json",
    "scripts/import_basil_valley.php"
]

def main():
    base_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
    print(f"Starting InfinityFree Deployment from: {base_dir}")
    print(f"Connecting to {ftp_host}...")
    
    ftp = ftplib.FTP(ftp_host, timeout=30)
    msg = ftp.login(user=ftp_user, passwd=ftp_pass)
    print("FTP Login Success:", msg.splitlines()[0] if msg else "OK")
    
    def smart_makepasv():
        resp = ftp.sendcmd('PASV')
        if resp.startswith('229'):
            m = re.search(r'\(\|\|\|(\d+)\|\)', resp)
            if m:
                port = int(m.group(1))
                host = ftp.sock.getpeername()[0]
                return host, port
            return ftplib.parse229(resp, ftp.sock.getpeername())
        elif resp.startswith('227'):
            return ftplib.parse227(resp)
        else:
            raise ftplib.error_reply(resp)
    
    ftp.makepasv = smart_makepasv
    
    def ensure_remote_dir(remote_dir_path):
        ftp.cwd("/")
        parts = [p for p in remote_dir_path.replace("\\", "/").split("/") if p]
        for p in parts:
            try:
                ftp.cwd(p)
            except ftplib.error_perm:
                try:
                    ftp.mkd(p)
                    ftp.cwd(p)
                except Exception as e:
                    pass

    success_count = 0
    fail_count = 0
    
    for rel_path in DEPLOY_TARGETS:
        local_path = os.path.join(base_dir, rel_path)
        if not os.path.exists(local_path):
            print(f"Skipping (local file missing): {rel_path}")
            continue
            
        remote_rel = "htdocs/" + rel_path.replace("\\", "/")
        remote_dir = os.path.dirname(remote_rel)
        remote_filename = os.path.basename(remote_rel)
        
        try:
            ensure_remote_dir(remote_dir)
            ftp.cwd("/" + remote_dir)
            file_size = os.path.getsize(local_path)
            with open(local_path, "rb") as f:
                ftp.storbinary(f"STOR {remote_filename}", f)
            print(f"  [UPLOADED] {remote_rel} ({file_size:,} bytes)")
            success_count += 1
        except Exception as e:
            print(f"  [FAILED] {remote_rel}: {e}")
            fail_count += 1

    ftp.quit()
    print("-" * 50)
    print(f"Deployment Finished: {success_count} files uploaded, {fail_count} failed.")

if __name__ == "__main__":
    main()
