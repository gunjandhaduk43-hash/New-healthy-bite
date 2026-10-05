"""
Generate Healthy Bite Final Corrected UML Use Case Diagram
Preserves college-approved format:
- Boundary box: "Healthy Bite — Digital Restaurant Menu and Food Ordering System"
- 5 Stick-figure actors: Dining Customer, Restaurant Owner, Restaurant Manager, Kitchen Staff, Super Admin
- Elliptical use cases with <<include>> stereotypes
- Consolidates duplicate 'Authenticate User' into 1 central use case
- Extends Kitchen Staff lifecycle to 'Complete Order' & 'Cancel Order'
- Includes full Owner & Super Admin management capabilities
Outputs:
- diagrams/04_Use_Case_Diagram.png
- diagrams/use case diagaram/Healthy Bite Use Case Diagram.png
"""

from PIL import Image, ImageDraw, ImageFont
import math

FONT_PATH = r"C:\Windows\Fonts\segoeui.ttf"
FONT_BOLD_PATH = r"C:\Windows\Fonts\segoeuib.ttf"

def get_font(size=14, bold=False):
    p = FONT_BOLD_PATH if bold else FONT_PATH
    try:
        return ImageFont.truetype(p, size)
    except Exception:
        return ImageFont.load_default()

def draw_actor(draw, center, label, color="#0F172A"):
    cx, cy = center
    # Head
    hr = 18
    hcy = cy - 35
    draw.ellipse([cx - hr, hcy - hr, cx + hr, hcy + hr], outline=color, width=3, fill="#FFFFFF")
    
    # Torso
    neck_y = hcy + hr
    pelvis_y = neck_y + 40
    draw.line([(cx, neck_y), (cx, pelvis_y)], fill=color, width=3)
    
    # Arms
    arm_y = neck_y + 14
    draw.line([(cx - 28, arm_y + 8), (cx, arm_y), (cx + 28, arm_y + 8)], fill=color, width=3)
    
    # Legs
    leg_len = 36
    draw.line([(cx, pelvis_y), (cx - 22, pelvis_y + leg_len)], fill=color, width=3)
    draw.line([(cx, pelvis_y), (cx + 22, pelvis_y + leg_len)], fill=color, width=3)
    
    # Label
    f = get_font(15, bold=True)
    bbox = draw.textbbox((0, 0), label, font=f)
    tw = bbox[2] - bbox[0]
    th = bbox[3] - bbox[1]
    draw.text((cx - tw // 2, pelvis_y + leg_len + 12), label, font=f, fill=color)

def draw_use_case(draw, center, text, w=280, h=64, fill="#F8FAFC", outline="#0284C7", width=2):
    cx, cy = center
    hw, hh = w // 2, h // 2
    draw.ellipse([cx - hw, cy - hh, cx + hw, cy + hh], fill=fill, outline=outline, width=width)
    
    f = get_font(12, bold=False)
    words = text.split(" ")
    lines = []
    curr = ""
    for w in words:
        if len(curr + " " + w) > 22:
            lines.append(curr.strip())
            curr = w
        else:
            curr += " " + w
    if curr.strip():
        lines.append(curr.strip())
        
    line_h = 16
    total_h = len(lines) * line_h
    start_y = cy - total_h // 2 + 2
    for l in lines:
        bbox = draw.textbbox((0, 0), l, font=f)
        tw = bbox[2] - bbox[0]
        draw.text((cx - tw // 2, start_y), l, font=f, fill="#0F172A")
        start_y += line_h

def draw_include(draw, p1, p2, stereotype="<<include>>"):
    # Dashed arrow from p1 to p2
    x1, y1 = p1
    x2, y2 = p2
    dist = math.hypot(x2 - x1, y2 - y1)
    if dist == 0:
        return
    dash_len = 6
    num_dashes = int(dist // (dash_len * 2))
    dx = (x2 - x1) / dist
    dy = (y2 - y1) / dist
    for i in range(num_dashes):
        sx = x1 + (2 * i * dash_len) * dx
        sy = y1 + (2 * i * dash_len) * dy
        ex = x1 + ((2 * i + 1) * dash_len) * dx
        ey = y1 + ((2 * i + 1) * dash_len) * dy
        draw.line([(sx, sy), (ex, ey)], fill="#64748B", width=2)
        
    # Arrow head at p2
    angle = math.atan2(y2 - y1, x2 - x1)
    a1 = angle + math.pi * 5 / 6
    a2 = angle - math.pi * 5 / 6
    sz = 8
    h1 = (x2 + sz * math.cos(a1), y2 + sz * math.sin(a1))
    h2 = (x2 + sz * math.cos(a2), y2 + sz * math.sin(a2))
    draw.polygon([p2, h1, h2], fill="#64748B")
    
    # Stereotype text
    mx = (x1 + x2) // 2
    my = (y1 + y2) // 2
    f = get_font(10, bold=True)
    bbox = draw.textbbox((0, 0), stereotype, font=f)
    tw = bbox[2] - bbox[0]
    draw.rectangle([mx - tw // 2 - 2, my - 8, mx + tw // 2 + 2, my + 8], fill="#FFFFFF")
    draw.text((mx - tw // 2, my - 7), stereotype, font=f, fill="#0369A1")

def generate():
    canvas_w = 3600
    canvas_h = 2400
    img = Image.new("RGB", (canvas_w, canvas_h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    # Banner
    draw.rectangle([0, 0, canvas_w, 90], fill="#0F172A")
    draw.text((50, 18), "HEALTHY BITE — UML USE CASE DIAGRAM", font=get_font(24, bold=True), fill="#FFFFFF")
    draw.text((50, 56), "Role-Based System Interactions • Consolidated Authentication • Complete Kitchen Lifecycle • Clean OMG UML 2.5 Compliance", font=get_font(13), fill="#94A3B8")

    # System Boundary Box
    bx1, by1 = 450, 130
    bx2, by2 = 3050, 2300
    draw.rounded_rectangle([bx1, by1, bx2, by2], radius=16, fill="#FFFFFF", outline="#0F172A", width=3)
    
    # Boundary Title
    draw.text((bx1 + 30, by1 + 20), "Healthy Bite — Digital Restaurant Menu & Food Ordering System", font=get_font(18, bold=True), fill="#0F172A")

    # Actors Position
    # Customer on left
    cust_pos = (220, 1100)
    draw_actor(draw, cust_pos, "Dining Customer")

    # Internal actors on right
    owner_pos = (3300, 360)
    draw_actor(draw, owner_pos, "Restaurant Owner")

    manager_pos = (3300, 880)
    draw_actor(draw, manager_pos, "Restaurant Manager")

    kitchen_pos = (3300, 1400)
    draw_actor(draw, kitchen_pos, "Kitchen Staff")

    admin_pos = (3300, 1950)
    draw_actor(draw, admin_pos, "Super Admin")

    # Central Authenticate User Use Case (SHARED by all 4 internal actors)
    auth_center = (2500, 1150)
    draw_use_case(draw, auth_center, "Authenticate System User (Login / Session)", w=320, h=66, fill="#FEF3C7", outline="#D97706", width=2)
    # Connect internal actors to single Authenticate User
    for act_pos in [owner_pos, manager_pos, kitchen_pos, admin_pos]:
        draw.line([act_pos, auth_center], fill="#D97706", width=2)

    # ================= CUSTOMER USE CASES (Left Column: cx = 800, 1250) =================
    cust_cases = [
        ("UC-C01", "Scan Table QR Code", (750, 240)),
        ("UC-C02", "Resolve Table & Dining Context", (1250, 240)), # included in Scan QR
        ("UC-C03", "Browse & Search Menu Catalog", (750, 380)),
        ("UC-C04", "Filter by Category & Dietary Type", (750, 520)),
        ("UC-C05", "View Food Details & Macro Breakdown", (750, 660)),
        ("UC-C06", "Customize Dish & Select Variants", (750, 800)),
        ("UC-C07", "Calculate Live Price & 8 Macros", (1250, 800)), # included in Customize
        ("UC-C08", "Manage Cart & Meal Pairings", (750, 940)),
        ("UC-C09", "Proceed to Checkout", (750, 1100)),
        ("UC-C10", "Submit Dine-In Order", (750, 1280)),
        ("UC-C11", "Simulate Payment Settlement (Cash/UPI/Card)", (1250, 1280)), # included in Submit Order
        ("UC-C12", "Track Live Order Status (Polling)", (750, 1460)),
        ("UC-C13", "Submit Verified Customer Review", (750, 1640)),
    ]

    cust_case_dict = {}
    for cid, ctext, ccenter in cust_cases:
        cust_case_dict[cid] = ccenter
        draw_use_case(draw, ccenter, f"{cid}: {ctext}", w=320, h=60, fill="#F0FDF4", outline="#16A34A", width=2)
        # Connect to customer if direct
        if cid not in ["UC-C02", "UC-C07", "UC-C11"]:
            draw.line([cust_pos, ccenter], fill="#16A34A", width=1)

    # Draw Customer Includes
    draw_include(draw, cust_case_dict["UC-C01"], cust_case_dict["UC-C02"], "<<include>>")
    draw_include(draw, cust_case_dict["UC-C06"], cust_case_dict["UC-C07"], "<<include>>")
    draw_include(draw, cust_case_dict["UC-C10"], cust_case_dict["UC-C11"], "<<include>>")

    # ================= RESTAURANT OWNER USE CASES (Right-Top: cx = 2000, 2400) =================
    owner_cases = [
        ("UC-O01", "Manage Menu Categories (CRUD)", (2150, 220)),
        ("UC-O02", "Manage Food Dishes & 8 Nutrition Macros", (2150, 320)),
        ("UC-O03", "Configure Portion Variants & Add-ons", (2150, 420)),
        ("UC-O04", "Toggle Instant Food Availability", (2150, 520)),
        ("UC-O05", "Manage Dining Tables & Generate QR Codes", (2150, 620)),
        ("UC-O06", "Monitor Live Orders (Kanban Board)", (2150, 720)),
        ("UC-O07", "Manage Staff Accounts & Permissions", (2150, 820)),
        ("UC-O08", "Manage Reviews & Submit Replies", (2150, 920)),
        ("UC-O09", "View Sales & Macro Nutrition Analytics", (2150, 1020)),
        ("UC-O10", "Export Analytics CSV Reports", (1700, 1020)), # extend/include
    ]

    for oid, otext, ocenter in owner_cases:
        draw_use_case(draw, ocenter, f"{oid}: {otext}", w=340, h=56, fill="#EFF6FF", outline="#2563EB", width=2)
        if oid != "UC-O10":
            draw.line([owner_pos, ocenter], fill="#2563EB", width=1)
            # Manager shares operational cases
            if oid in ["UC-O02", "UC-O04", "UC-O05", "UC-O06", "UC-O09"]:
                draw.line([manager_pos, ocenter], fill="#0284C7", width=1)

    draw_include(draw, (2150, 1020), (1700, 1020), "<<extend>>")

    # ================= KITCHEN STAFF USE CASES (Right-Middle: cx = 2150) =================
    kitchen_cases = [
        ("UC-K01", "View Live Kitchen Kanban Board", (2150, 1340)),
        ("UC-K02", "Accept Placed Order (Status -> accepted)", (2150, 1440)),
        ("UC-K03", "Start Preparation (Status -> preparing)", (2150, 1540)),
        ("UC-K04", "Mark Ready for Pickup (Status -> ready)", (2150, 1640)),
        ("UC-K05", "Complete Served Order (Status -> completed)", (2150, 1740)), # CRITICAL CORRECTION
        ("UC-K06", "Cancel / Reject Order (Status -> cancelled)", (1700, 1440)), # CRITICAL CORRECTION
    ]

    for kid, ktext, kcenter in kitchen_cases:
        draw_use_case(draw, kcenter, f"{kid}: {ktext}", w=350, h=56, fill="#FFF1F2", outline="#E11D48", width=2)
        draw.line([kitchen_pos, kcenter], fill="#E11D48", width=1)

    # ================= SUPER ADMIN USE CASES (Bottom Right: cx = 2150) =================
    admin_cases = [
        ("UC-A01", "View Platform Overview & KPI Dashboard", (2150, 1920)),
        ("UC-A02", "Manage Registered Restaurants (Onboarding)", (2150, 2020)),
        ("UC-A03", "Approve / Pending / Suspend Restaurant Status", (2150, 2120)),
        ("UC-A04", "Inspect Tenant Portals (Read-Only Audit)", (1700, 2020)),
        ("UC-A05", "Manage Platform Users & Roles", (2150, 2220)),
    ]

    for aid, atext, acenter in admin_cases:
        draw_use_case(draw, acenter, f"{aid}: {atext}", w=360, h=56, fill="#F5F3FF", outline="#7C3AED", width=2)
        draw.line([admin_pos, acenter], fill="#7C3AED", width=1)

    draw_include(draw, (2150, 2020), (1700, 2020), "<<extend>>")

    # Legend / Verification Box (Bottom Left inside Boundary)
    leg_x = bx1 + 30
    leg_y = by1 + 1720
    draw.rounded_rectangle([leg_x, leg_y, leg_x + 620, leg_y + 400], radius=8, fill="#F8FAFC", outline="#CBD5E1", width=2)
    draw.text((leg_x + 20, leg_y + 16), "USE CASE AUDIT & RECTIFICATIONS APPLIED:", font=get_font(14, bold=True), fill="#0F172A")
    fixes = [
        "1. Consolidated 4 duplicate 'Authenticate User' ellipses into 1 shared use case",
        "2. Fixed <<extend>> inversion: Table resolution is an <<include>> in Dine-In",
        "3. Extended Kitchen Staff: Added 'Complete Order' (completed) & 'Cancel Order'",
        "4. Fully represented Owner features: Categories, Macros, Tables, QR, Staff, Reviews",
        "5. Fully represented Super Admin: Tenant Onboarding, Suspension, Portal Inspect",
        "6. Customer: Clean separation of Browse, Filter, 8-Macro Customization, and Reviews",
        "7. PERMANENTLY EXCLUDED: Call Servant, Table Assistance, and Admin Orders (/admin/orders)"
    ]
    cy = leg_y + 46
    for f_txt in fixes:
        draw.text((leg_x + 20, cy), f_txt, font=get_font(11), fill="#334155")
        cy += 24

    out1 = r"d:\NEW healthy bite\diagrams\04_Use_Case_Diagram.png"
    out2 = r"d:\NEW healthy bite\diagrams\use case diagaram\Healthy Bite Use Case Diagram.png"
    img.save(out1, "PNG", dpi=(300, 300))
    img.save(out2, "PNG", dpi=(300, 300))
    print(f"Generated Use Case Diagram: {out1} & {out2} ({canvas_w}x{canvas_h})")

if __name__ == "__main__":
    generate()
