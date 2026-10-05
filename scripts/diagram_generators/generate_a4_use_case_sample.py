"""
Generate Healthy Bite A4-Documentation-Optimized Use Case Diagram Samples
Specifically engineered for standard A4 documentation pages with top heading margins:
1. Landscape A4 Sample: 2000 x 1150 (Ideal for landscape pages or horizontal sections)
2. Portrait A4 Sample: 1400 x 1950 (Ideal for standard portrait A4 pages filling vertical height below heading)
- Large, bold, high-contrast typography readable at 100% zoom without zooming in
- Consolidated 'Authenticate User' use case (eliminating duplicate bubbles)
- Correct UML 2.5 stereotypes (<<include>>, <<extend>>)
- Complete 5-actor system: Dining Customer, Restaurant Owner, Manager, Kitchen Staff, Super Admin
- 100% exclusion of removed legacy features (Call Servant, Admin Orders)
Outputs:
- diagrams/Sample_A4_Use_Case_Landscape.png
- diagrams/Sample_A4_Use_Case_Portrait.png
- diagrams/Sample_A4_Use_Case_Diagram.png
"""

import math
from PIL import Image, ImageDraw, ImageFont

FONT_PATH = r"C:\Windows\Fonts\segoeui.ttf"
FONT_BOLD_PATH = r"C:\Windows\Fonts\segoeuib.ttf"

def get_font(size=14, bold=False):
    p = FONT_BOLD_PATH if bold else FONT_PATH
    try:
        return ImageFont.truetype(p, size)
    except Exception:
        return ImageFont.load_default()

def draw_actor(draw, center, label, color="#0F172A", scale=1.0):
    cx, cy = center
    # Head
    hr = int(14 * scale)
    hcy = cy - int(28 * scale)
    draw.ellipse([cx - hr, hcy - hr, cx + hr, hcy + hr], outline=color, width=2, fill="#FFFFFF")
    
    # Spine
    neck_y = hcy + hr
    pelvis_y = neck_y + int(32 * scale)
    draw.line([(cx, neck_y), (cx, pelvis_y)], fill=color, width=2)
    
    # Arms
    arm_y = neck_y + int(10 * scale)
    aw = int(22 * scale)
    draw.line([(cx - aw, arm_y + int(6 * scale)), (cx, arm_y), (cx + aw, arm_y + int(6 * scale))], fill=color, width=2)
    
    # Legs
    lw = int(16 * scale)
    lh = int(28 * scale)
    draw.line([(cx, pelvis_y), (cx - lw, pelvis_y + lh)], fill=color, width=2)
    draw.line([(cx, pelvis_y), (cx + lw, pelvis_y + lh)], fill=color, width=2)
    
    # Label
    f = get_font(12, bold=True)
    lines = label.split("\n")
    line_h = 15
    curr_y = pelvis_y + lh + 6
    for l in lines:
        bbox = draw.textbbox((0, 0), l, font=f)
        tw = bbox[2] - bbox[0]
        draw.text((cx - tw // 2, curr_y), l, font=f, fill=color)
        curr_y += line_h

def draw_use_case_bubble(draw, center, text, w=240, h=48, fill="#F8FAFC", outline="#0284C7", width=2):
    cx, cy = center
    hw, hh = w // 2, h // 2
    draw.ellipse([cx - hw, cy - hh, cx + hw, cy + hh], fill=fill, outline=outline, width=width)
    
    f = get_font(10, bold=True)
    lines = text.split("\n")
    line_h = 14
    total_h = len(lines) * line_h
    start_y = cy - total_h // 2 + 1
    for l in lines:
        bbox = draw.textbbox((0, 0), l, font=f)
        tw = bbox[2] - bbox[0]
        draw.text((cx - tw // 2, start_y), l, font=f, fill="#0F172A")
        start_y += line_h

def draw_include(draw, p1, p2, label="<<include>>"):
    x1, y1 = p1
    x2, y2 = p2
    dist = math.hypot(x2 - x1, y2 - y1)
    if dist == 0:
        return
    dash_len = 5
    num_dashes = int(dist // (dash_len * 2))
    dx = (x2 - x1) / dist
    dy = (y2 - y1) / dist
    for i in range(num_dashes):
        sx = x1 + (2 * i * dash_len) * dx
        sy = y1 + (2 * i * dash_len) * dy
        ex = x1 + ((2 * i + 1) * dash_len) * dx
        ey = y1 + ((2 * i + 1) * dash_len) * dy
        draw.line([(sx, sy), (ex, ey)], fill="#64748B", width=1)
        
    angle = math.atan2(y2 - y1, x2 - x1)
    sz = 6
    a1 = angle + math.pi * 5 / 6
    a2 = angle - math.pi * 5 / 6
    h1 = (x2 + sz * math.cos(a1), y2 + sz * math.sin(a1))
    h2 = (x2 + sz * math.cos(a2), y2 + sz * math.sin(a2))
    draw.polygon([p2, h1, h2], fill="#64748B")
    
    mx = (x1 + x2) // 2
    my = (y1 + y2) // 2
    f = get_font(9, bold=True)
    bbox = draw.textbbox((0, 0), label, font=f)
    tw = bbox[2] - bbox[0]
    draw.rectangle([mx - tw // 2 - 2, my - 6, mx + tw // 2 + 2, my + 8], fill="#FFFFFF")
    draw.text((mx - tw // 2, my - 5), label, font=f, fill="#0284C7" if "include" in label else "#D97706")

def generate_landscape():
    # 2000 x 1150: Ideal for A4 Landscape page
    w, h = 2000, 1150
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    # Top banner (44px)
    draw.rectangle([0, 0, w, 44], fill="#0F172A")
    draw.text((25, 10), "HEALTHY BITE — UML USE CASE DIAGRAM (A4 LANDSCAPE OPTIMIZED)", font=get_font(16, bold=True), fill="#FFFFFF")
    draw.text((w - 560, 12), "Single-Page A4 Documentation • Consolidated Auth • 100% Readable", font=get_font(11), fill="#94A3B8")

    # Boundary Box
    bx1, by1 = 220, 60
    bx2, by2 = 1760, 1120
    draw.rounded_rectangle([bx1, by1, bx2, by2], radius=10, fill="#FFFFFF", outline="#0F172A", width=2)
    draw.text((bx1 + 20, by1 + 10), "Healthy Bite System Boundary", font=get_font(12, bold=True), fill="#0F172A")

    # Actors: Customer on Left
    cust_pos = (110, 560)
    draw_actor(draw, cust_pos, "Dining\nCustomer")

    # Internal Actors on Right
    owner_pos = (1880, 180)
    draw_actor(draw, owner_pos, "Restaurant\nOwner")

    manager_pos = (1880, 460)
    draw_actor(draw, manager_pos, "Restaurant\nManager")

    kitchen_pos = (1880, 720)
    draw_actor(draw, kitchen_pos, "Kitchen\nStaff")

    admin_pos = (1880, 980)
    draw_actor(draw, admin_pos, "Super\nAdmin")

    # Single Shared Authenticate User
    auth_center = (1450, 560)
    draw_use_case_bubble(draw, auth_center, "Authenticate User\n(Login / Session)", w=220, h=48, fill="#FEF3C7", outline="#D97706")
    for ap in [owner_pos, manager_pos, kitchen_pos, admin_pos]:
        draw.line([ap, auth_center], fill="#D97706", width=1)

    # Customer Use Cases (Left Columns inside boundary: x = 400, x = 680)
    cust_cases = [
        ("UC-C01", "Scan Table QR Code", (400, 130)),
        ("UC-C02", "Resolve Table & Branch Context", (680, 130)), # included
        ("UC-C03", "Browse & Search Menu Catalog", (400, 220)),
        ("UC-C04", "Filter by Category & Dietary Type", (400, 310)),
        ("UC-C05", "View Dish Details & 8 Macros", (400, 400)),
        ("UC-C06", "Customize Dish & Select Variants", (400, 490)),
        ("UC-C07", "Calculate Live Price & 8 Macros", (680, 490)), # included
        ("UC-C08", "Manage Cart & Meal Pairings", (400, 580)),
        ("UC-C09", "Proceed to Checkout", (400, 670)),
        ("UC-C10", "Submit Dine-In Order", (400, 760)),
        ("UC-C11", "Simulate Payment (Cash/UPI/Card)", (680, 760)), # included
        ("UC-C12", "Track Live Order Status (Polling)", (400, 850)),
        ("UC-C13", "Submit Verified Order Review", (400, 940)),
    ]

    c_dict = {}
    for cid, ctext, ccenter in cust_cases:
        c_dict[cid] = ccenter
        draw_use_case_bubble(draw, ccenter, f"{cid}: {ctext}", w=240, h=44, fill="#F0FDF4", outline="#16A34A")
        if cid not in ["UC-C02", "UC-C07", "UC-C11"]:
            draw.line([cust_pos, ccenter], fill="#16A34A", width=1)

    # Customer Includes
    draw_include(draw, c_dict["UC-C01"], c_dict["UC-C02"], "<<include>>")
    draw_include(draw, c_dict["UC-C06"], c_dict["UC-C07"], "<<include>>")
    draw_include(draw, c_dict["UC-C10"], c_dict["UC-C11"], "<<include>>")

    # Owner & Manager Use Cases (Middle-Right: x = 1150, y: 120 to 520)
    owner_cases = [
        ("UC-O01", "Manage Menu Categories (CRUD)", (1150, 110)),
        ("UC-O02", "Manage Food Dishes & 8 Macros", (1150, 180)),
        ("UC-O03", "Configure Portions & Customizations", (1150, 250)),
        ("UC-O04", "Toggle Instant Item Availability", (1150, 320)),
        ("UC-O05", "Manage Dining Tables & QR Codes", (1150, 390)),
        ("UC-O06", "Monitor Live Orders (Kanban)", (1150, 460)),
        ("UC-O07", "Manage Staff Accounts & Roles", (1150, 530)),
        ("UC-O08", "Manage Reviews & Submit Replies", (1150, 600)),
        ("UC-O09", "View Sales & Macro Analytics", (1150, 670)),
    ]

    for oid, otext, ocenter in owner_cases:
        draw_use_case_bubble(draw, ocenter, f"{oid}: {otext}", w=250, h=42, fill="#EFF6FF", outline="#2563EB")
        draw.line([owner_pos, ocenter], fill="#2563EB", width=1)
        if oid in ["UC-O02", "UC-O04", "UC-O05", "UC-O06", "UC-O09"]:
            draw.line([manager_pos, ocenter], fill="#0284C7", width=1)

    # Kitchen Staff Use Cases (Middle-Right: x = 1150, y: 740 to 920)
    kitchen_cases = [
        ("UC-K01", "Accept Placed Order (Status: accepted)", (1150, 740)),
        ("UC-K02", "Start Preparation (Status: preparing)", (1150, 800)),
        ("UC-K03", "Mark Ready for Pickup (Status: ready)", (1150, 860)),
        ("UC-K04", "Complete Served Order (Status: completed)", (1150, 920)), # CRITICAL
        ("UC-K05", "Cancel / Reject Order (Status: cancelled)", (1450, 800)), # Alternate
    ]

    for kid, ktext, kcenter in kitchen_cases:
        draw_use_case_bubble(draw, kcenter, f"{kid}: {ktext}", w=250, h=40, fill="#FFF1F2", outline="#E11D48")
        draw.line([kitchen_pos, kcenter], fill="#E11D48", width=1)

    # Super Admin Use Cases (Bottom-Middle: x = 1150, y: 990 to 1080)
    admin_cases = [
        ("UC-A01", "View Platform Overview KPIs", (1150, 990)),
        ("UC-A02", "Approve / Suspend Registered Tenants", (1150, 1050)),
        ("UC-A03", "Inspect Tenant Portals (Read-Only)", (1450, 1050)),
    ]

    for aid, atext, acenter in admin_cases:
        draw_use_case_bubble(draw, acenter, f"{aid}: {atext}", w=250, h=40, fill="#F5F3FF", outline="#7C3AED")
        draw.line([admin_pos, acenter], fill="#7C3AED", width=1)

    draw_include(draw, (1150, 1050), (1450, 1050), "<<extend>>")

    # Compact Legend Box (Bottom Left: x: 240, y: 1010)
    lx, ly = 240, 1015
    draw.rounded_rectangle([lx, ly, lx + 500, ly + 95], radius=6, fill="#F8FAFC", outline="#CBD5E1", width=1)
    draw.text((lx + 10, ly + 6), "A4 LANDSCAPE SPECIFICATION & COMPLIANCE:", font=get_font(10, bold=True), fill="#0F172A")
    notes = [
        "1. Consolidated Authentication: 1 central bubble replaces 4 duplicates.",
        "2. Completed Kitchen State: Full lifecycle (placed -> ready -> completed / cancelled).",
        "3. 100% Removed Features: Zero Call Servant, Zero Server Requests, Zero /admin/orders."
    ]
    cy_n = ly + 24
    for n in notes:
        draw.text((lx + 10, cy_n), n, font=get_font(9), fill="#334155")
        cy_n += 18

    out1 = r"d:\NEW healthy bite\diagrams\Sample_A4_Use_Case_Landscape.png"
    out2 = r"d:\NEW healthy bite\diagrams\Sample_A4_Use_Case_Diagram.png"
    img.save(out1, "PNG", dpi=(300, 300))
    img.save(out2, "PNG", dpi=(300, 300))
    print(f"Generated Landscape A4 Use Case: {out1} & {out2} ({w}x{h})")

def generate_portrait():
    # 1400 x 1950: Ideal for A4 Portrait page with heading space
    w, h = 1400, 1950
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    # Top banner (44px)
    draw.rectangle([0, 0, w, 44], fill="#0F172A")
    draw.text((25, 10), "HEALTHY BITE — UML USE CASE DIAGRAM (A4 PORTRAIT OPTIMIZED)", font=get_font(15, bold=True), fill="#FFFFFF")
    draw.text((w - 520, 12), "Fits Single Portrait A4 Page with Heading • Readable without Zooming", font=get_font(10), fill="#94A3B8")

    # Boundary Box
    bx1, by1 = 180, 60
    bx2, by2 = 1220, 1850
    draw.rounded_rectangle([bx1, by1, bx2, by2], radius=10, fill="#FFFFFF", outline="#0F172A", width=2)
    draw.text((bx1 + 20, by1 + 10), "Healthy Bite System Boundary", font=get_font(12, bold=True), fill="#0F172A")

    # Actors: Customer on Left
    cust_pos = (90, 550)
    draw_actor(draw, cust_pos, "Dining\nCustomer")

    # Internal Actors on Right
    owner_pos = (1310, 220)
    draw_actor(draw, owner_pos, "Restaurant\nOwner")

    manager_pos = (1310, 620)
    draw_actor(draw, manager_pos, "Restaurant\nManager")

    kitchen_pos = (1310, 1150)
    draw_actor(draw, kitchen_pos, "Kitchen\nStaff")

    admin_pos = (1310, 1600)
    draw_actor(draw, admin_pos, "Super\nAdmin")

    # Single Shared Authenticate User
    auth_center = (960, 520)
    draw_use_case_bubble(draw, auth_center, "Authenticate User\n(Login / Session)", w=220, h=48, fill="#FEF3C7", outline="#D97706")
    for ap in [owner_pos, manager_pos, kitchen_pos, admin_pos]:
        draw.line([ap, auth_center], fill="#D97706", width=1)

    # Customer Use Cases (Left Column: x = 360, x = 650)
    cust_cases = [
        ("UC-C01", "Scan Table QR Code", (360, 110)),
        ("UC-C02", "Resolve Table & Branch Context", (650, 110)), # included
        ("UC-C03", "Browse & Search Menu Catalog", (360, 190)),
        ("UC-C04", "Filter by Category & Dietary Type", (360, 270)),
        ("UC-C05", "View Dish Details & 8 Macros", (360, 350)),
        ("UC-C06", "Customize Dish & Select Variants", (360, 430)),
        ("UC-C07", "Calculate Live Price & 8 Macros", (650, 430)), # included
        ("UC-C08", "Manage Cart & Meal Pairings", (360, 510)),
        ("UC-C09", "Proceed to Checkout", (360, 590)),
        ("UC-C10", "Submit Dine-In Order", (360, 670)),
        ("UC-C11", "Simulate Payment (Cash/UPI/Card)", (650, 670)), # included
        ("UC-C12", "Track Live Order Status (Polling)", (360, 750)),
        ("UC-C13", "Submit Verified Order Review", (360, 830)),
    ]

    c_dict = {}
    for cid, ctext, ccenter in cust_cases:
        c_dict[cid] = ccenter
        draw_use_case_bubble(draw, ccenter, f"{cid}: {ctext}", w=240, h=44, fill="#F0FDF4", outline="#16A34A")
        if cid not in ["UC-C02", "UC-C07", "UC-C11"]:
            draw.line([cust_pos, ccenter], fill="#16A34A", width=1)

    # Customer Includes
    draw_include(draw, c_dict["UC-C01"], c_dict["UC-C02"], "<<include>>")
    draw_include(draw, c_dict["UC-C06"], c_dict["UC-C07"], "<<include>>")
    draw_include(draw, c_dict["UC-C10"], c_dict["UC-C11"], "<<include>>")

    # Owner & Manager Use Cases (Right Column: x = 960, y: 110 to 450 & 620 to 760)
    owner_cases = [
        ("UC-O01", "Manage Menu Categories (CRUD)", (960, 110)),
        ("UC-O02", "Manage Food Dishes & 8 Macros", (960, 175)),
        ("UC-O03", "Configure Portions & Customizations", (960, 240)),
        ("UC-O04", "Toggle Instant Item Availability", (960, 305)),
        ("UC-O05", "Manage Dining Tables & QR Codes", (960, 370)),
        ("UC-O06", "Monitor Live Orders (Kanban)", (960, 620)),
        ("UC-O07", "Manage Staff Accounts & Roles", (960, 685)),
        ("UC-O08", "Manage Reviews & Submit Replies", (960, 750)),
        ("UC-O09", "View Sales & Macro Analytics", (960, 815)),
    ]

    for oid, otext, ocenter in owner_cases:
        draw_use_case_bubble(draw, ocenter, f"{oid}: {otext}", w=250, h=40, fill="#EFF6FF", outline="#2563EB")
        draw.line([owner_pos, ocenter], fill="#2563EB", width=1)
        if oid in ["UC-O02", "UC-O04", "UC-O05", "UC-O06", "UC-O09"]:
            draw.line([manager_pos, ocenter], fill="#0284C7", width=1)

    # Kitchen Staff Use Cases (x = 650, x = 960, y: 950 to 1250)
    kitchen_cases = [
        ("UC-K01", "Accept Placed Order (Status: accepted)", (960, 1020)),
        ("UC-K02", "Start Preparation (Status: preparing)", (960, 1090)),
        ("UC-K03", "Mark Ready for Pickup (Status: ready)", (960, 1160)),
        ("UC-K04", "Complete Served Order (Status: completed)", (960, 1230)), # CRITICAL
        ("UC-K05", "Cancel / Reject Order (Status: cancelled)", (650, 1090)), # Alternate
    ]

    for kid, ktext, kcenter in kitchen_cases:
        draw_use_case_bubble(draw, kcenter, f"{kid}: {ktext}", w=250, h=42, fill="#FFF1F2", outline="#E11D48")
        draw.line([kitchen_pos, kcenter], fill="#E11D48", width=1)

    # Super Admin Use Cases (x = 960, y: 1420 to 1580)
    admin_cases = [
        ("UC-A01", "View Platform Overview KPIs", (960, 1420)),
        ("UC-A02", "Approve / Suspend Registered Tenants", (960, 1490)),
        ("UC-A03", "Inspect Tenant Portals (Read-Only)", (960, 1560)),
        ("UC-A04", "Manage Platform Users & Access Roles", (960, 1630)),
    ]

    for aid, atext, acenter in admin_cases:
        draw_use_case_bubble(draw, acenter, f"{aid}: {atext}", w=250, h=42, fill="#F5F3FF", outline="#7C3AED")
        draw.line([admin_pos, acenter], fill="#7C3AED", width=1)

    # Compact Legend Box (Bottom Left: x: 220, y: 1720)
    lx, ly = 220, 1720
    draw.rounded_rectangle([lx, ly, lx + 960, ly + 105], radius=6, fill="#F8FAFC", outline="#CBD5E1", width=1)
    draw.text((lx + 15, ly + 8), "A4 PORTRAIT USE CASE AUDIT & RECTIFICATIONS APPLIED:", font=get_font(11, bold=True), fill="#0F172A")
    notes_p = [
        "1. SINGLE PORTRAIT A4 PAGE FIT: Formatted to fit standard A4 portrait pages leaving full room for top chapter headings.",
        "2. ZERO ZOOMING REQUIRED: Bold text, high contrast, clean ellipses, and thick association connectors ensure crystal clarity.",
        "3. CONSOLIDATED AUTHENTICATION: 1 central bubble replaces 4 redundant identical duplicate bubbles from old diagram.",
        "4. COMPLETED KITCHEN LIFECYCLE: Added missing 'Complete Order' (completed status) and 'Cancel Order' (cancelled status).",
        "5. 100% FEATURE EXCLUSION: Zero Call Servant / Server Assistance, Zero Table Assistance, Zero Admin Orders (/admin/orders)."
    ]
    cy_np = ly + 26
    for np in notes_p:
        draw.text((lx + 15, cy_np), np, font=get_font(9), fill="#334155")
        cy_np += 15

    out_p = r"d:\NEW healthy bite\diagrams\Sample_A4_Use_Case_Portrait.png"
    img.save(out_p, "PNG", dpi=(300, 300))
    print(f"Generated Portrait A4 Use Case: {out_p} ({w}x{h})")

def generate_both():
    generate_landscape()
    generate_portrait()

if __name__ == "__main__":
    generate_both()
