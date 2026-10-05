"""
Generate Healthy Bite Final Corrected Data Flow Diagrams (Context, Level 1, Level 2)
Preserves college-approved format:
- Context Diagram (Level 0): Single central system process (0.0) with external actor entities
- Level 1 DFD: 8 core decomposed functional subsystems with real data stores
- Level 2 DFDs: 5 detailed sub-flow sheets for QR, Menu, Cart, Checkout, and Kitchen
Outputs:
- diagrams/08_DFD_Level_0.png & diagrams/dfd/Healthy Bite Context Diagram.png
- diagrams/09_DFD_Level_1.png & diagrams/dfd/Healthy Bite Level 1 Data Flow Diagram.png
- diagrams/10_DFD_Level_2_QR_Resolution.png & diagrams/dfd/Level 2 Scan and Session Resolution DFD.png
- diagrams/10_DFD_Level_2_Menu_Customization.png & diagrams/dfd/Level 2 Menu Customization DFD.png
- diagrams/10_DFD_Level_2_Cart_Management.png & diagrams/dfd/DFD Level 2_ Cart Management Flow.png
- diagrams/10_DFD_Level_2_Order_Checkout.png & diagrams/dfd/DFD Level 2_ Order Checkout Flow.png
- diagrams/10_DFD_Level_2_Kitchen_Fulfillment.png & diagrams/dfd/Kitchen Fulfillment Level 2 DFD.png
"""

import math
from PIL import Image, ImageDraw, ImageFont

FONT_PATH = r"C:\Windows\Fonts\segoeui.ttf"
FONT_BOLD_PATH = r"C:\Windows\Fonts\segoeuib.ttf"
FONT_MONO_PATH = r"C:\Windows\Fonts\consola.ttf"

def get_font(size=14, bold=False, mono=False):
    p = FONT_MONO_PATH if mono else (FONT_BOLD_PATH if bold else FONT_PATH)
    try:
        return ImageFont.truetype(p, size)
    except Exception:
        return ImageFont.load_default()

def draw_external_entity(draw, center, label, w=180, h=64, fill="#F8FAFC", outline="#0F172A", width=2):
    cx, cy = center
    x1, y1 = cx - w // 2, cy - h // 2
    x2, y2 = cx + w // 2, cy + h // 2
    draw.rectangle([x1, y1, x2, y2], fill=fill, outline=outline, width=width)
    f = get_font(12, bold=True)
    lines = label.split("\n")
    line_h = 16
    total_h = len(lines) * line_h
    start_y = cy - total_h // 2 + 1
    for l in lines:
        bbox = draw.textbbox((0, 0), l, font=f)
        tw = bbox[2] - bbox[0]
        draw.text((cx - tw // 2, start_y), l, font=f, fill="#0F172A")
        start_y += line_h

def draw_dfd_process(draw, center, r, p_num, p_name, fill="#FFFFFF", outline="#0284C7", width=2):
    cx, cy = center
    draw.ellipse([cx - r, cy - r, cx + r, cy + r], fill=fill, outline=outline, width=width)
    # Chord line for process number
    chord_y = cy - r // 3
    draw.line([(cx - int(r * 0.9), chord_y), (cx + int(r * 0.9), chord_y)], fill=outline, width=1)
    
    # Process number
    nf = get_font(12, bold=True)
    draw.text((cx - len(p_num) * 4, cy - r // 2 - 3), p_num, font=nf, fill="#0369A1")

    # Process text
    tf = get_font(11, bold=False)
    lines = p_name.split("\n")
    line_h = 15
    total_h = len(lines) * line_h
    start_y = cy + 2 - total_h // 4
    for l in lines:
        bbox = draw.textbbox((0, 0), l, font=tf)
        tw = bbox[2] - bbox[0]
        draw.text((cx - tw // 2, start_y), l, font=tf, fill="#0F172A")
        start_y += line_h

def draw_data_store(draw, center, store_id, store_name, w=220, h=44, fill="#F8FAFC", outline="#334155", width=2):
    cx, cy = center
    x1, y1 = cx - w // 2, cy - h // 2
    x2, y2 = cx + w // 2, cy + h // 2
    draw.rectangle([x1, y1, x2, y2], fill=fill, outline=None)
    # Open right side
    draw.line([(x1, y1), (x2, y1)], fill=outline, width=width)
    draw.line([(x1, y2), (x2, y2)], fill=outline, width=width)
    draw.line([(x1, y1), (x1, y2)], fill=outline, width=width)
    
    # id divider
    id_w = 40
    draw.line([(x1 + id_w, y1), (x1 + id_w, y2)], fill=outline, width=width)
    draw.text((x1 + 8, y1 + 12), store_id, font=get_font(11, bold=True), fill="#1E293B")
    draw.text((x1 + id_w + 10, y1 + 12), store_name, font=get_font(11, bold=True), fill="#0F172A")

def draw_flow_arrow(draw, p1, p2, label="", fill="#475569", width=2, label_color="#0F172A", label_offset=(0, -12)):
    x1, y1 = p1
    x2, y2 = p2
    draw.line([p1, p2], fill=fill, width=width)
    
    # Arrow head
    dist = math.hypot(x2 - x1, y2 - y1)
    if dist > 0:
        angle = math.atan2(y2 - y1, x2 - x1)
        sz = 8
        a1 = angle + math.pi * 5 / 6
        a2 = angle - math.pi * 5 / 6
        h1 = (x2 + sz * math.cos(a1), y2 + sz * math.sin(a1))
        h2 = (x2 + sz * math.cos(a2), y2 + sz * math.sin(a2))
        draw.polygon([p2, h1, h2], fill=fill)

    if label:
        f = get_font(10, bold=False)
        mx = (x1 + x2) // 2 + label_offset[0]
        my = (y1 + y2) // 2 + label_offset[1]
        bbox = draw.textbbox((0, 0), label, font=f)
        tw = bbox[2] - bbox[0]
        # Soft background badge for readability
        draw.rectangle([mx - tw // 2 - 2, my - 2, mx + tw // 2 + 2, my + 14], fill="#FFFFFF")
        draw.text((mx - tw // 2, my), label, font=f, fill=label_color)

# ==============================================================================
# 1. DFD LEVEL 0 (CONTEXT DIAGRAM)
# ==============================================================================
def generate_dfd_level_0():
    w, h = 2400, 1600
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    # Banner
    draw.rectangle([0, 0, w, 80], fill="#0F172A")
    draw.text((40, 16), "HEALTHY BITE — DATA FLOW DIAGRAM (DFD LEVEL 0 / CONTEXT DIAGRAM)", font=get_font(22, bold=True), fill="#FFFFFF")
    draw.text((40, 50), "Overall System Context • Boundary Definition • Major External Entities • Simulated Payment Processing", font=get_font(12), fill="#94A3B8")

    # Central System Process (0.0)
    sys_cx, sys_cy = 1200, 800
    sys_r = 180
    draw.ellipse([sys_cx - sys_r, sys_cy - sys_r, sys_cx + sys_r, sys_cy + sys_r], fill="#F0FDF4", outline="#16A34A", width=4)
    draw.line([(sys_cx - int(sys_r * 0.9), sys_cy - 50), (sys_cx + int(sys_r * 0.9), sys_cy - 50)], fill="#16A34A", width=2)
    draw.text((sys_cx - 16, sys_cy - 90), "0.0", font=get_font(18, bold=True), fill="#15803D")
    
    draw.text((sys_cx - 140, sys_cy - 10), "Healthy Bite", font=get_font(22, bold=True), fill="#0F172A")
    draw.text((sys_cx - 150, sys_cy + 25), "Digital Restaurant Menu &", font=get_font(14, bold=True), fill="#0F172A")
    draw.text((sys_cx - 130, sys_cy + 50), "Food Ordering System", font=get_font(14, bold=True), fill="#0F172A")

    # 4 External Entities
    # 1. Dining Customer (Left)
    cust_c = (300, 800)
    draw_external_entity(draw, cust_c, "Dining Customer", w=220, h=80, fill="#FFFFFF", outline="#0284C7", width=3)

    # 2. Restaurant Owner (Top)
    owner_c = (1200, 220)
    draw_external_entity(draw, owner_c, "Restaurant Owner", w=240, h=80, fill="#FFFFFF", outline="#2563EB", width=3)

    # 3. Kitchen Staff (Right)
    kitchen_c = (2100, 800)
    draw_external_entity(draw, kitchen_c, "Kitchen Staff", w=220, h=80, fill="#FFFFFF", outline="#E11D48", width=3)

    # 4. Super Admin (Bottom)
    admin_c = (1200, 1380)
    draw_external_entity(draw, admin_c, "Super Admin", w=240, h=80, fill="#FFFFFF", outline="#7C3AED", width=3)

    # Customer Flows
    # Inflows to system
    draw_flow_arrow(draw, (410, 740), (sys_cx - sys_r + 10, 740), label="Scan QR Token / Customer Details", label_offset=(0, -14))
    draw_flow_arrow(draw, (410, 780), (sys_cx - sys_r, 780), label="Dish Customization & Cart Requests", label_offset=(0, -14))
    draw_flow_arrow(draw, (410, 820), (sys_cx - sys_r, 820), label="Order Placement & Simulated Tender", label_offset=(0, -14))
    draw_flow_arrow(draw, (410, 860), (sys_cx - sys_r + 10, 860), label="Verified Order Rating & Review", label_offset=(0, -14))

    # Outflows to customer
    draw_flow_arrow(draw, (sys_cx - sys_r + 10, 920), (410, 920), label="Resolved Menu, Categories & 8 Macros", label_offset=(0, -14))
    draw_flow_arrow(draw, (sys_cx - sys_r + 10, 960), (410, 960), label="Order Confirmation & Tracking Updates", label_offset=(0, -14))

    # Owner Flows
    draw_flow_arrow(draw, (1140, 260), (1140, sys_cy - sys_r), label="Menu Dishes, Categories & Macro Updates", label_offset=(-120, 0))
    draw_flow_arrow(draw, (1180, 260), (1180, sys_cy - sys_r), label="Tables & QR Token Generation", label_offset=(-80, 0))
    draw_flow_arrow(draw, (1220, sys_cy - sys_r), (1220, 260), label="Sales, Order & Macro Analytics Reports", label_offset=(100, 0))
    draw_flow_arrow(draw, (1260, sys_cy - sys_r), (1260, 260), label="Customer Reviews & Feedback Stream", label_offset=(100, 0))

    # Kitchen Staff Flows
    draw_flow_arrow(draw, (sys_cx + sys_r, 760), (1990, 760), label="Incoming Orders Notification (Kanban Card)", label_offset=(0, -14))
    draw_flow_arrow(draw, (1990, 820), (sys_cx + sys_r, 820), label="Order Status Updates (accepted, preparing, ready, completed)", label_offset=(0, -14))
    draw_flow_arrow(draw, (1990, 860), (sys_cx + sys_r, 860), label="Order Cancellation / Out-of-Stock Alert", label_offset=(0, -14))

    # Super Admin Flows
    draw_flow_arrow(draw, (1160, 1340), (1160, sys_cy + sys_r), label="Restaurant Approval / Suspension Decisions", label_offset=(-120, 0))
    draw_flow_arrow(draw, (1240, sys_cy + sys_r), (1240, 1340), label="Platform KPIs, Tenant Status & Audits", label_offset=(100, 0))

    # Compliance Note
    draw.rounded_rectangle([60, 1350, 850, 1530], radius=8, fill="#F8FAFC", outline="#CBD5E1", width=2)
    draw.text((80, 1365), "CONTEXT LEVEL SPECIFICATION & RESOLUTIONS:", font=get_font(13, bold=True), fill="#0F172A")
    c_notes = [
        "1. SINGLE SYSTEM PROCESS (0.0): Encapsulates entire multi-tenant Healthy Bite platform.",
        "2. INTERNAL PAYMENT PROCESSING: Payment tenders are processed and simulated internally via",
        "   PaymentService (Cash/UPI/Card) without third-party external gateway dependencies.",
        "3. PROPER DFD CONVENTION: Exactly 4 external actors interface with central process 0.0.",
        "4. STRICT DATA BOUNDARY: Eliminates duplicate mislabeled Level 0 DFDs."
    ]
    ny = 1395
    for cn in c_notes:
        draw.text((80, ny), cn, font=get_font(11), fill="#334155")
        ny += 22

    out1 = r"d:\NEW healthy bite\diagrams\08_DFD_Level_0.png"
    out2 = r"d:\NEW healthy bite\diagrams\dfd\Healthy Bite Context Diagram.png"
    img.save(out1, "PNG", dpi=(300, 300))
    img.save(out2, "PNG", dpi=(300, 300))
    print(f"Generated DFD Level 0: {out1} & {out2}")

# ==============================================================================
# 2. DFD LEVEL 1 (DECOMPOSED SYSTEM PROCESSES)
# ==============================================================================
def generate_dfd_level_1():
    w, h = 3400, 2400
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    # Banner
    draw.rectangle([0, 0, w, 80], fill="#0F172A")
    draw.text((40, 16), "HEALTHY BITE — LEVEL 1 DATA FLOW DIAGRAM", font=get_font(22, bold=True), fill="#FFFFFF")
    draw.text((40, 50), "Complete Functional Decomposition • 8 Subsystems • 16 Physical Data Stores • Balanced Multi-Actor Information Flows", font=get_font(12), fill="#94A3B8")

    # 4 Actors
    cust_c = (200, 1000)
    draw_external_entity(draw, cust_c, "Dining Customer", w=200, h=70, outline="#0284C7")

    owner_c = (1700, 180)
    draw_external_entity(draw, owner_c, "Restaurant Owner", w=220, h=70, outline="#2563EB")

    kitchen_c = (3200, 1000)
    draw_external_entity(draw, kitchen_c, "Kitchen Staff", w=200, h=70, outline="#E11D48")

    admin_c = (1700, 2220)
    draw_external_entity(draw, admin_c, "Super Admin", w=220, h=70, outline="#7C3AED")

    # 8 Subsystem Processes (Circles)
    # 1.0 Authentication & RBAC (Top-Left / Center)
    # 2.0 QR Resolution & Table Context (Left-Top)
    # 3.0 Menu & Catalog Management (Top-Right)
    # 4.0 Customer Menu & 8 Macros (Left-Center)
    # 5.0 Cart & Order Checkout (Center)
    # 6.0 Simulated Payment (Center-Right)
    # 7.0 Kitchen Live Fulfillment & Tracking (Right-Center)
    # 8.0 Reviews & Platform Administration (Bottom-Center)
    processes = {
        "1.0": ((1200, 420), "User Auth &\nRBAC Security", "#F8FAFC"),
        "2.0": ((700, 600), "QR Resolution &\nTable Context", "#F0FDF4"),
        "3.0": ((2200, 420), "Menu & Catalog\nManagement", "#EFF6FF"),
        "4.0": ((700, 1000), "Menu Exploration &\n8 Macro Calc", "#F0FDF4"),
        "5.0": ((1350, 1000), "Cart & Order\nPlacement", "#FEF3C7"),
        "6.0": ((2000, 1000), "Simulated Payment\nSettlement", "#FEF3C7"),
        "7.0": ((2650, 1000), "Kitchen Fulfillment\n& Order Tracking", "#FFF1F2"),
        "8.0": ((1700, 1750), "Reviews, Ratings &\nPlatform Governance", "#F5F3FF")
    }

    for p_num, (pos, name, bg) in processes.items():
        draw_dfd_process(draw, pos, 84, p_num, name, fill=bg, outline="#0284C7" if bg=="#F8FAFC" else ("#16A34A" if bg=="#F0FDF4" else ("#2563EB" if bg=="#EFF6FF" else ("#D97706" if bg=="#FEF3C7" else ("#E11D48" if bg=="#FFF1F2" else "#7C3AED")))))

    # Real Data Stores
    stores = {
        "D1": ((1200, 260), "roles / D3 users"),
        "D2": ((700, 380), "qr_tokens / D9 tables"),
        "D5": ((2550, 320), "categories / D6 food_items"),
        "D7": ((1000, 780), "food_variants / D8 customs"),
        "D11": ((1350, 1300), "customers"),
        "D12": ((1350, 1420), "orders / D13 order_items"),
        "D15": ((2000, 1300), "payments (1:1 Unique)"),
        "D16": ((2150, 1750), "reviews")
    }

    for sid, (pos, sname) in stores.items():
        draw_data_store(draw, pos, sid, sname, w=240, h=42)

    # Actor -> Process Connections
    draw_flow_arrow(draw, (300, 970), (616, 600), label="Scan QR Token")
    draw_flow_arrow(draw, (616, 620), (300, 990), label="Dining Session Context")
    draw_flow_arrow(draw, (300, 1010), (616, 1000), label="Browse Menu & Select Add-ons")
    draw_flow_arrow(draw, (616, 1020), (300, 1030), label="Live Price & 8 Macros")
    
    draw_flow_arrow(draw, (784, 1000), (1266, 1000), label="Validated Cart Items")
    draw_flow_arrow(draw, (300, 1050), (1266, 1040), label="Checkout Details & Dine-In Table")
    draw_flow_arrow(draw, (1434, 1000), (1916, 1000), label="Order Total & Payment Method")
    draw_flow_arrow(draw, (1916, 1040), (1434, 1040), label="Payment Settlement Approved")
    
    draw_flow_arrow(draw, (1434, 980), (2566, 980), label="Order Placed Notification (Snapshots)")
    draw_flow_arrow(draw, (2734, 1000), (3100, 1000), label="Kitchen Order Card (Kanban)")
    draw_flow_arrow(draw, (3100, 1030), (2734, 1030), label="Status: accepted, preparing, ready, completed")
    draw_flow_arrow(draw, (2566, 1020), (300, 1070), label="Live Polling Status Updates")

    # Customer Review
    draw_flow_arrow(draw, (300, 1100), (1616, 1750), label="Verified Order Review & Rating")

    # Owner Flows
    draw_flow_arrow(draw, (1700, 215), (1284, 380), label="Owner Credentials")
    draw_flow_arrow(draw, (1700, 215), (2116, 380), label="Menu, Dishes & Nutrition Updates")
    draw_flow_arrow(draw, (2116, 440), (1700, 240), label="Catalog Status")
    draw_flow_arrow(draw, (1784, 1750), (1750, 250), label="Customer Review Stream & Reply Dispatch")

    # Super Admin Flows
    draw_flow_arrow(draw, (1700, 2185), (1284, 460), label="Admin Login")
    draw_flow_arrow(draw, (1700, 2185), (1700, 1834), label="Approve / Suspend Tenant")
    draw_flow_arrow(draw, (1700, 1834), (1700, 2185), label="Platform Audit Logs & Metrics")

    # Process <-> Data Store flows
    draw_flow_arrow(draw, (1200, 336), (1200, 281), label="Verify User")
    draw_flow_arrow(draw, (700, 516), (700, 401), label="Validate Token")
    draw_flow_arrow(draw, (2200, 336), (2430, 320), label="Store Dish")
    draw_flow_arrow(draw, (880, 780), (700, 916), label="Fetch Macros")
    draw_flow_arrow(draw, (1350, 1084), (1350, 1279), label="Save Customer")
    draw_flow_arrow(draw, (1350, 1084), (1350, 1399), label="Save Order & Items")
    draw_flow_arrow(draw, (2000, 1084), (2000, 1279), label="Record Payment (1:1)")
    draw_flow_arrow(draw, (1784, 1750), (2030, 1750), label="Persist Reviews")

    out1 = r"d:\NEW healthy bite\diagrams\09_DFD_Level_1.png"
    out2 = r"d:\NEW healthy bite\diagrams\dfd\Healthy Bite Level 1 Data Flow Diagram.png"
    out3 = r"d:\NEW healthy bite\diagrams\dfd\Healthy Bite Level 0 Data Flow Diagram.png" # Synchronize duplicate
    img.save(out1, "PNG", dpi=(300, 300))
    img.save(out2, "PNG", dpi=(300, 300))
    img.save(out3, "PNG", dpi=(300, 300))
    print(f"Generated DFD Level 1: {out1}, {out2}, & synchronized {out3}")

# ==============================================================================
# 3. DFD LEVEL 2: QR SCAN & SESSION RESOLUTION (PROCESS 2.0)
# ==============================================================================
def generate_dfd_l2_qr():
    w, h = 2400, 1600
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    draw.rectangle([0, 0, w, 80], fill="#0F172A")
    draw.text((40, 16), "HEALTHY BITE — DFD LEVEL 2 (PROCESS 2.0: QR SCAN & SESSION RESOLUTION)", font=get_font(22, bold=True), fill="#FFFFFF")
    draw.text((40, 50), "Sub-process Decomposition of Process 2.0 • Token Parsing • Context Verification • Active Session Establishment", font=get_font(12), fill="#94A3B8")

    cust_c = (200, 800)
    draw_external_entity(draw, cust_c, "Dining Customer", w=200, h=70, outline="#0284C7")

    # 6 Sub-processes
    l2_procs = [
        ("2.1", (550, 800), "Receive QR Token\nfrom Camera Scan"),
        ("2.2", (900, 800), "Validate Token &\nCheck Expiration"),
        ("2.3", (1250, 600), "Resolve Physical\nDining Table Details"),
        ("2.4", (1250, 1000), "Resolve Restaurant\nBranch Profile"),
        ("2.5", (1650, 800), "Verify Restaurant\nAccount Active Status"),
        ("2.6", (2050, 800), "Establish Active\nDining Context")
    ]

    for p_num, pos, pname in l2_procs:
        draw_dfd_process(draw, pos, 76, p_num, pname, fill="#F0FDF4", outline="#16A34A")

    # Stores
    draw_data_store(draw, (900, 480), "D10", "qr_tokens (Cryptographic)", w=240, h=42)
    draw_data_store(draw, (1250, 400), "D9", "restaurant_tables", w=220, h=42)
    draw_data_store(draw, (1250, 1200), "D4", "branches", w=200, h=42)
    draw_data_store(draw, (1650, 480), "D2", "restaurants (Status)", w=220, h=42)

    # Flows
    draw_flow_arrow(draw, (300, 800), (474, 800), label="Scan QR URL Token")
    draw_flow_arrow(draw, (626, 800), (824, 800), label="Extracted Token String")
    draw_flow_arrow(draw, (900, 724), (900, 501), label="Query Token Status")
    
    draw_flow_arrow(draw, (976, 760), (1174, 620), label="Valid Table ID")
    draw_flow_arrow(draw, (1250, 524), (1250, 421), label="Fetch Table Number")

    draw_flow_arrow(draw, (976, 840), (1174, 980), label="Valid Branch ID")
    draw_flow_arrow(draw, (1250, 1076), (1250, 1179), label="Fetch Branch Location")

    draw_flow_arrow(draw, (1326, 620), (1574, 760), label="Table Metadata")
    draw_flow_arrow(draw, (1326, 980), (1574, 840), label="Branch Metadata")
    draw_flow_arrow(draw, (1650, 724), (1650, 501), label="Verify Status == 'approved'")

    draw_flow_arrow(draw, (1726, 800), (1974, 800), label="Verified Tenant Context")
    draw_flow_arrow(draw, (2050, 876), (300, 840), label="Active Dining Session Established (Table Number, Branch, Restaurant)")

    out1 = r"d:\NEW healthy bite\diagrams\10_DFD_Level_2_QR_Resolution.png"
    out2 = r"d:\NEW healthy bite\diagrams\dfd\Level 2 Scan and Session Resolution DFD.png"
    img.save(out1, "PNG", dpi=(300, 300))
    img.save(out2, "PNG", dpi=(300, 300))
    print(f"Generated DFD Level 2 (QR): {out1} & {out2}")

# ==============================================================================
# 4. DFD LEVEL 2: MENU EXPLORATION & 8 MACROS (PROCESS 4.0)
# ==============================================================================
def generate_dfd_l2_menu():
    w, h = 2400, 1600
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    draw.rectangle([0, 0, w, 80], fill="#0F172A")
    draw.text((40, 16), "HEALTHY BITE — DFD LEVEL 2 (PROCESS 4.0: MENU EXPLORATION & MACRO CALCULATION)", font=get_font(22, bold=True), fill="#FFFFFF")
    draw.text((40, 50), "Sub-process Decomposition • Dietary Filters • Variant Scaling • All 8 Tracked Nutritional Macros", font=get_font(12), fill="#94A3B8")

    cust_c = (200, 800)
    draw_external_entity(draw, cust_c, "Dining Customer", w=200, h=70, outline="#0284C7")

    l2_procs = [
        ("4.1", (550, 800), "Load Categories &\nMenu Taxonomy"),
        ("4.2", (920, 800), "Search & Filter\nby Dietary Type"),
        ("4.3", (1300, 800), "Load Dish Details &\nBase 8 Macros"),
        ("4.4", (1680, 600), "Fetch Portion Variants\n& Price Adjustments"),
        ("4.5", (1680, 1000), "Fetch Customizations\n& Dietary Add-ons"),
        ("4.6", (2100, 800), "Compute Scaled 8 Macros\n& Live Dynamic Price")
    ]

    for p_num, pos, pname in l2_procs:
        draw_dfd_process(draw, pos, 76, p_num, pname, fill="#F0FDF4", outline="#16A34A")

    # Stores
    draw_data_store(draw, (550, 480), "D5", "categories (Sort Order)", w=240, h=42)
    draw_data_store(draw, (1300, 480), "D6", "food_items (8 Base Macros)", w=250, h=42)
    draw_data_store(draw, (1680, 400), "D7", "food_variants (Portions)", w=240, h=42)
    draw_data_store(draw, (1680, 1200), "D8", "food_customizations", w=240, h=42)

    # Flows
    draw_flow_arrow(draw, (300, 780), (474, 800), label="Request Menu Catalog")
    draw_flow_arrow(draw, (550, 724), (550, 501), label="Query Active Categories")
    draw_flow_arrow(draw, (626, 800), (844, 800), label="Categories Stream")
    draw_flow_arrow(draw, (300, 820), (844, 820), label="Dietary Preference Filter (Veg, Jain, Vegan)")
    
    draw_flow_arrow(draw, (996, 800), (1224, 800), label="Filtered Dish Selection")
    draw_flow_arrow(draw, (1300, 724), (1300, 501), label="Query Food Item & Base Macros")
    
    draw_flow_arrow(draw, (1376, 760), (1604, 620), label="Dish ID for Variants")
    draw_flow_arrow(draw, (1680, 524), (1680, 421), label="Query Portion Deltas")

    draw_flow_arrow(draw, (1376, 840), (1604, 980), label="Dish ID for Add-ons")
    draw_flow_arrow(draw, (1680, 1076), (1680, 1179), label="Query Custom Groups")

    draw_flow_arrow(draw, (1756, 620), (2024, 760), label="Variant Price & Macro Deltas")
    draw_flow_arrow(draw, (1756, 980), (2024, 840), label="Add-on Price & Macro Deltas")
    
    draw_flow_arrow(draw, (2100, 876), (300, 860), label="Live Scaled Summary: kcal, protein, carbs, fat, fiber, sugar, sodium, caffeine & Price")

    out1 = r"d:\NEW healthy bite\diagrams\10_DFD_Level_2_Menu_Customization.png"
    out2 = r"d:\NEW healthy bite\diagrams\dfd\Level 2 Menu Customization DFD.png"
    img.save(out1, "PNG", dpi=(300, 300))
    img.save(out2, "PNG", dpi=(300, 300))
    print(f"Generated DFD Level 2 (Menu): {out1} & {out2}")

# ==============================================================================
# 5. DFD LEVEL 2: CART MANAGEMENT FLOW (PROCESS 3.0 / CART)
# ==============================================================================
def generate_dfd_l2_cart():
    w, h = 2400, 1600
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    draw.rectangle([0, 0, w, 80], fill="#0F172A")
    draw.text((40, 16), "HEALTHY BITE — DFD LEVEL 2 (CART MANAGEMENT & MEAL RECOMMENDATION FLOW)", font=get_font(22, bold=True), fill="#FFFFFF")
    draw.text((40, 50), "Sub-process Decomposition • Client-Side LocalStorage Cart • Server Availability Validation • Pairings Engine", font=get_font(12), fill="#94A3B8")

    cust_c = (200, 800)
    draw_external_entity(draw, cust_c, "Dining Customer", w=200, h=70, outline="#0284C7")

    l2_procs = [
        ("3.1", (550, 800), "Add Configured Item\nto Client Cart"),
        ("3.2", (920, 600), "Update Item Quantity\n(1 to 50 Range)"),
        ("3.3", (920, 1000), "Remove Item from\nCart State"),
        ("3.4", (1350, 800), "Server Validation of\nPrices & Stock"),
        ("3.5", (1750, 800), "Calculate Cart Subtotal,\nTax & Macro Totals"),
        ("3.6", (2150, 800), "Generate 'Complete\nYour Meal' Pairings")
    ]

    for p_num, pos, pname in l2_procs:
        draw_dfd_process(draw, pos, 76, p_num, pname, fill="#FEF3C7", outline="#D97706")

    # Stores
    draw_data_store(draw, (1350, 480), "D6", "food_items (Availability)", w=240, h=42)
    draw_data_store(draw, (1350, 1150), "D8", "food_customizations", w=240, h=42)
    draw_data_store(draw, (2150, 480), "D5", "categories (Pairing Rules)", w=240, h=42)

    # Flows
    draw_flow_arrow(draw, (300, 780), (474, 800), label="Item Config + Selected Add-ons")
    draw_flow_arrow(draw, (300, 800), (844, 600), label="Quantity Change Event")
    draw_flow_arrow(draw, (300, 820), (844, 1000), label="Remove Item Request")
    
    draw_flow_arrow(draw, (626, 800), (1274, 800), label="Cart Payload")
    draw_flow_arrow(draw, (996, 620), (1274, 780), label="Updated Quantity")
    draw_flow_arrow(draw, (996, 980), (1274, 820), label="Filtered Cart Array")

    draw_flow_arrow(draw, (1350, 724), (1350, 501), label="Check is_available & live base_price")
    draw_flow_arrow(draw, (1350, 876), (1350, 1129), label="Validate Customization Active Flags")

    draw_flow_arrow(draw, (1426, 800), (1674, 800), label="Validated Item Manifest")
    draw_flow_arrow(draw, (1826, 800), (2074, 800), label="Active Category Hierarchy")
    draw_flow_arrow(draw, (2150, 724), (2150, 501), label="Query Complementary Categories")

    draw_flow_arrow(draw, (1750, 876), (300, 850), label="Cart Totals (Subtotal, GST Tax, 8 Cumulative Macros)")
    draw_flow_arrow(draw, (2150, 876), (300, 880), label="Recommended Beverages & Desserts Pairings")

    out1 = r"d:\NEW healthy bite\diagrams\10_DFD_Level_2_Cart_Management.png"
    out2 = r"d:\NEW healthy bite\diagrams\dfd\DFD Level 2_ Cart Management Flow.png"
    img.save(out1, "PNG", dpi=(300, 300))
    img.save(out2, "PNG", dpi=(300, 300))
    print(f"Generated DFD Level 2 (Cart): {out1} & {out2}")

# ==============================================================================
# 6. DFD LEVEL 2: ORDER CHECKOUT & SNAPSHOTS (PROCESS 5.0 / CHECKOUT)
# ==============================================================================
def generate_dfd_l2_checkout():
    w, h = 2600, 1800
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    draw.rectangle([0, 0, w, 80], fill="#0F172A")
    draw.text((40, 16), "HEALTHY BITE — DFD LEVEL 2 (PROCESS 5.0: ORDER CHECKOUT & PAYMENT SETTLEMENT)", font=get_font(22, bold=True), fill="#FFFFFF")
    draw.text((40, 50), "Sub-process Decomposition • Immutable Snapshots • Simulated Payment Tender • 1:1 Settlement Constraint", font=get_font(12), fill="#94A3B8")

    cust_c = (200, 900)
    draw_external_entity(draw, cust_c, "Dining Customer", w=200, h=70, outline="#0284C7")

    l2_procs = [
        ("5.1", (550, 900), "Receive Checkout Details\n& Customer Mobile"),
        ("5.2", (900, 900), "Server Revalidation of\nCart & Dine-In Table"),
        ("5.3", (1250, 650), "Create / Lookup\nCustomer Profile"),
        ("5.4", (1250, 1150), "Create Order Master\n(status = 'placed')"),
        ("5.5", (1650, 650), "Persist Frozen Order\nItem Price Snapshots"),
        ("5.6", (1650, 1150), "Persist Customization\nSnapshot Records"),
        ("5.7", (2050, 900), "Execute Simulated Payment\n(Cash, UPI, Card)"),
        ("5.8", (2400, 900), "Generate Confirmation &\nDispatch to Kitchen")
    ]

    for p_num, pos, pname in l2_procs:
        draw_dfd_process(draw, pos, 76, p_num, pname, fill="#FEF3C7", outline="#D97706")

    # Real Stores
    draw_data_store(draw, (1250, 420), "D11", "customers (Capture Profile)", w=250, h=42)
    draw_data_store(draw, (1250, 1380), "D12", "orders (Master Order)", w=240, h=42)
    draw_data_store(draw, (1650, 420), "D13", "order_items (Snapshots)", w=250, h=42)
    draw_data_store(draw, (1650, 1380), "D14", "order_item_customizations", w=260, h=42)
    draw_data_store(draw, (2050, 1380), "D15", "payments (Strict 1:1 Unique)", w=270, h=42)

    # Flows
    draw_flow_arrow(draw, (300, 900), (474, 900), label="Submit Checkout Form")
    draw_flow_arrow(draw, (626, 900), (824, 900), label="Customer & Cart Payload")
    
    draw_flow_arrow(draw, (976, 860), (1174, 680), label="Customer Mobile & Name")
    draw_flow_arrow(draw, (1250, 574), (1250, 441), label="Insert / Update customer_id")

    draw_flow_arrow(draw, (976, 940), (1174, 1120), label="Order Totals & Table ID")
    draw_flow_arrow(draw, (1250, 1226), (1250, 1359), label="Insert Order Record")

    draw_flow_arrow(draw, (1326, 1150), (1574, 690), label="order_id + Item Configurations")
    draw_flow_arrow(draw, (1650, 574), (1650, 441), label="Store food_name & base_price snapshots")

    draw_flow_arrow(draw, (1326, 1170), (1574, 1170), label="order_item_id + Add-on Deltas")
    draw_flow_arrow(draw, (1650, 1226), (1650, 1359), label="Store custom_name & price_adj snapshots")

    draw_flow_arrow(draw, (1726, 680), (1974, 860), label="order_id + Payable Amount")
    draw_flow_arrow(draw, (1726, 1150), (1974, 940), label="Payment Method Tender")
    draw_flow_arrow(draw, (2050, 976), (2050, 1359), label="Insert Payment Record (payments.order_id UNIQUE)")

    draw_flow_arrow(draw, (2126, 900), (2324, 900), label="Payment Settlement Approved")
    draw_flow_arrow(draw, (2400, 976), (300, 950), label="Order Confirmation Number (e.g. HB-1001) & Live Tracker URL")

    out1 = r"d:\NEW healthy bite\diagrams\10_DFD_Level_2_Order_Checkout.png"
    out2 = r"d:\NEW healthy bite\diagrams\dfd\DFD Level 2_ Order Checkout Flow.png"
    img.save(out1, "PNG", dpi=(300, 300))
    img.save(out2, "PNG", dpi=(300, 300))
    print(f"Generated DFD Level 2 (Checkout): {out1} & {out2}")

# ==============================================================================
# 7. DFD LEVEL 2: KITCHEN FULFILLMENT & TRACKING (PROCESS 7.0 / KITCHEN)
# ==============================================================================
def generate_dfd_l2_kitchen():
    w, h = 2400, 1600
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    draw.rectangle([0, 0, w, 80], fill="#0F172A")
    draw.text((40, 16), "HEALTHY BITE — DFD LEVEL 2 (PROCESS 7.0: KITCHEN FULFILLMENT & ORDER TRACKING)", font=get_font(22, bold=True), fill="#FFFFFF")
    draw.text((40, 50), "Sub-process Decomposition • 5-Column Kanban Lifecycle • Real-Time Table Polling • Final Completion Handoff", font=get_font(12), fill="#94A3B8")

    kitchen_c = (2200, 800)
    draw_external_entity(draw, kitchen_c, "Kitchen Staff", w=200, h=70, outline="#E11D48")

    cust_c = (200, 800)
    draw_external_entity(draw, cust_c, "Dining Customer", w=200, h=70, outline="#0284C7")

    l2_procs = [
        ("7.1", (550, 800), "Receive Placed Order\n& Line-Item Snapshots"),
        ("7.2", (900, 800), "Render Kitchen Kanban\nCard with Table Number"),
        ("7.3", (1250, 600), "Accept Placed Order\n(status = 'accepted')"),
        ("7.4", (1250, 1000), "Start Preparation\n(status = 'preparing')"),
        ("7.5", (1650, 800), "Mark Ready for Service\n(status = 'ready')"),
        ("7.6", (1950, 800), "Complete Served Order\n(status = 'completed')")
    ]

    for p_num, pos, pname in l2_procs:
        draw_dfd_process(draw, pos, 76, p_num, pname, fill="#FFF1F2", outline="#E11D48")

    # Stores
    draw_data_store(draw, (550, 480), "D12", "orders (status = 'placed')", w=240, h=42)
    draw_data_store(draw, (550, 1150), "D13", "order_items & D14 customs", w=260, h=42)
    draw_data_store(draw, (1250, 380), "D12", "orders (Status: accepted)", w=240, h=42)
    draw_data_store(draw, (1250, 1220), "D12", "orders (Status: preparing)", w=240, h=42)
    draw_data_store(draw, (1650, 480), "D12", "orders (Status: ready)", w=240, h=42)
    draw_data_store(draw, (1950, 1150), "D12", "orders (Status: completed)", w=250, h=42)

    # Flows
    draw_flow_arrow(draw, (550, 501), (550, 724), label="Poll New Orders")
    draw_flow_arrow(draw, (550, 1129), (550, 876), label="Read Recipe & Customizations")
    draw_flow_arrow(draw, (626, 800), (824, 800), label="Order Manifest")

    draw_flow_arrow(draw, (976, 760), (1174, 620), label="Card Assigned to Kitchen")
    draw_flow_arrow(draw, (2100, 780), (1326, 600), label="Staff Clicks 'Accept Order'")
    draw_flow_arrow(draw, (1250, 524), (1250, 401), label="Update order_status = 'accepted'")

    draw_flow_arrow(draw, (1250, 676), (1250, 924), label="Preparation Queue")
    draw_flow_arrow(draw, (2100, 800), (1326, 1000), label="Staff Clicks 'Start Preparing'")
    draw_flow_arrow(draw, (1250, 1076), (1250, 1199), label="Update order_status = 'preparing'")

    draw_flow_arrow(draw, (1326, 1000), (1574, 820), label="Chef Finishing Cooking")
    draw_flow_arrow(draw, (2100, 820), (1726, 800), label="Staff Clicks 'Mark Ready'")
    draw_flow_arrow(draw, (1650, 724), (1650, 501), label="Update order_status = 'ready'")

    draw_flow_arrow(draw, (1726, 820), (1874, 800), label="Food Delivered to Table")
    draw_flow_arrow(draw, (2100, 840), (2026, 800), label="Staff Clicks 'Complete Order'")
    draw_flow_arrow(draw, (1950, 876), (1950, 1129), label="Update order_status = 'completed'")

    # Polling flows to Customer
    draw_flow_arrow(draw, (1250, 380), (300, 760), label="Poll Status: 'Order Accepted'")
    draw_flow_arrow(draw, (1250, 1220), (300, 800), label="Poll Status: 'Preparing in Kitchen'")
    draw_flow_arrow(draw, (1650, 480), (300, 840), label="Poll Status: 'Ready for Service'")
    draw_flow_arrow(draw, (1950, 1150), (300, 880), label="Poll Status: 'Order Completed' -> Prompt Review")

    out1 = r"d:\NEW healthy bite\diagrams\10_DFD_Level_2_Kitchen_Fulfillment.png"
    out2 = r"d:\NEW healthy bite\diagrams\dfd\Kitchen Fulfillment Level 2 DFD.png"
    out3 = r"d:\NEW healthy bite\diagrams\dfd\Level 2 Kitchen Fulfillment Flow.png" # Clean up duplicate
    img.save(out1, "PNG", dpi=(300, 300))
    img.save(out2, "PNG", dpi=(300, 300))
    img.save(out3, "PNG", dpi=(300, 300))
    print(f"Generated DFD Level 2 (Kitchen): {out1}, {out2}, & clean duplicate {out3}")

def generate_all_dfds():
    generate_dfd_level_0()
    generate_dfd_level_1()
    generate_dfd_l2_qr()
    generate_dfd_l2_menu()
    generate_dfd_l2_cart()
    generate_dfd_l2_checkout()
    generate_dfd_l2_kitchen()

if __name__ == "__main__":
    generate_all_dfds()
