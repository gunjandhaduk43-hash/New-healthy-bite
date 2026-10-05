"""
Generate Healthy Bite Final Corrected Cross-Functional Process Flow Diagram
Preserves college-approved format:
- Cross-functional swimlane layout
- Swimlanes: Customer, Database (healthy_bite), Restaurant Owner, Kitchen Staff, Super Admin
- Critical architectural fix: Payment insertion moved exclusively to Customer Checkout
- Plural table names (D16 reviews, etc.) and complete data store representation
Outputs:
- diagrams/06_Process_Flow_Diagram.png
- diagrams/process flow diagram/process flow diagram.png
"""

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

def draw_step_box(draw, center, text, w=220, h=54, fill="#FFFFFF", outline="#0284C7", width=2):
    cx, cy = center
    x1, y1 = cx - w // 2, cy - h // 2
    x2, y2 = cx + w // 2, cy + h // 2
    draw.rounded_rectangle([x1, y1, x2, y2], radius=8, fill=fill, outline=outline, width=width)
    
    f = get_font(11, bold=False)
    lines = text.split("\n")
    line_h = 15
    total_h = len(lines) * line_h
    start_y = cy - total_h // 2 + 1
    for l in lines:
        bbox = draw.textbbox((0, 0), l, font=f)
        tw = bbox[2] - bbox[0]
        draw.text((cx - tw // 2, start_y), l, font=f, fill="#0F172A")
        start_y += line_h

def draw_db_store(draw, center, s_id, s_name, w=200, h=40, fill="#F1F5F9", outline="#334155", width=2):
    cx, cy = center
    x1, y1 = cx - w // 2, cy - h // 2
    x2, y2 = cx + w // 2, cy + h // 2
    draw.rectangle([x1, y1, x2, y2], fill=fill, outline=None)
    draw.line([(x1, y1), (x2, y1)], fill=outline, width=width)
    draw.line([(x1, y2), (x2, y2)], fill=outline, width=width)
    draw.line([(x1, y1), (x1, y2)], fill=outline, width=width)
    
    draw.line([(x1 + 38, y1), (x1 + 38, y2)], fill=outline, width=width)
    draw.text((x1 + 8, y1 + 10), s_id, font=get_font(11, bold=True), fill="#1E293B")
    draw.text((x1 + 46, y1 + 10), s_name, font=get_font(11, bold=True), fill="#0F172A")

def draw_connector(draw, p1, p2, label="", fill="#475569", width=2):
    x1, y1 = p1
    x2, y2 = p2
    draw.line([p1, p2], fill=fill, width=width)
    # terminal arrow at p2
    arrow_sz = 6
    if x1 == x2: # vertical
        if y2 > y1:
            draw.polygon([(x2, y2), (x2 - arrow_sz, y2 - arrow_sz * 1.5), (x2 + arrow_sz, y2 - arrow_sz * 1.5)], fill=fill)
        else:
            draw.polygon([(x2, y2), (x2 - arrow_sz, y2 + arrow_sz * 1.5), (x2 + arrow_sz, y2 + arrow_sz * 1.5)], fill=fill)
    elif y1 == y2: # horizontal
        if x2 > x1:
            draw.polygon([(x2, y2), (x2 - arrow_sz * 1.5, y2 - arrow_sz), (x2 - arrow_sz * 1.5, y2 + arrow_sz)], fill=fill)
        else:
            draw.polygon([(x2, y2), (x2 + arrow_sz * 1.5, y2 - arrow_sz), (x2 + arrow_sz * 1.5, y2 + arrow_sz)], fill=fill)
    if label:
        f = get_font(10, bold=True)
        draw.text(((x1 + x2) // 2 + 4, (y1 + y2) // 2 - 12), label, font=f, fill="#2563EB")

def generate():
    canvas_w = 2600
    canvas_h = 3200
    img = Image.new("RGB", (canvas_w, canvas_h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    # Banner
    draw.rectangle([0, 0, canvas_w, 90], fill="#0F172A")
    draw.text((40, 18), "HEALTHY BITE — CROSS-FUNCTIONAL PROCESS FLOW DIAGRAM", font=get_font(24, bold=True), fill="#FFFFFF")
    draw.text((40, 56), "End-to-End System Information Flow • 5 Swimlanes • Checkout Payment Binding • Plural Table Stores", font=get_font(13), fill="#94A3B8")

    # Swimlanes configuration
    # 1. Customer: [40, 540] (w = 500)
    # 2. Database: [540, 1080] (w = 540)
    # 3. Restaurant Owner: [1080, 1580] (w = 500)
    # 4. Kitchen Staff: [1580, 2080] (w = 500)
    # 5. Super Admin: [2080, 2560] (w = 480)
    lanes = [
        ("CUSTOMER", 40, 540, "#F0FDF4", "#16A34A"),
        ("DATABASE (healthy_bite)", 540, 1080, "#F8FAFC", "#475569"),
        ("RESTAURANT OWNER", 1080, 1580, "#EFF6FF", "#2563EB"),
        ("KITCHEN STAFF", 1580, 2080, "#FFF1F2", "#E11D48"),
        ("SUPER ADMIN", 2080, 2560, "#F5F3FF", "#7C3AED")
    ]

    header_y = 100
    lane_h = 44
    draw.rectangle([40, header_y, canvas_w - 40, canvas_h - 40], outline="#CBD5E1", width=2)

    for lname, lx1, lx2, lbg, lcolor in lanes:
        # Lane Header
        draw.rectangle([lx1, header_y, lx2, header_y + lane_h], fill=lbg, outline="#CBD5E1", width=1)
        draw.text(((lx1 + lx2) // 2 - len(lname) * 4, header_y + 12), lname, font=get_font(13, bold=True), fill=lcolor)
        # Vertical divider line
        draw.line([(lx2, header_y), (lx2, canvas_h - 40)], fill="#CBD5E1", width=1)

    # Lane Centers
    c_x = 290
    db_x = 810
    o_x = 1330
    k_x = 1830
    a_x = 2320

    # ================= 1. ONBOARDING & SETUP PHASE (Y: 180 to 450) =================
    draw_step_box(draw, (a_x, 200), "Review Restaurant Onboarding\nApplication & Documents", fill="#FFFFFF", outline="#7C3AED")
    draw_step_box(draw, (a_x, 280), "Approve Restaurant Status\n(status = 'approved')", fill="#F5F3FF", outline="#7C3AED")
    draw_connector(draw, (a_x, 227), (a_x, 253))
    
    draw_db_store(draw, (db_x, 280), "D2", "restaurants")
    draw_connector(draw, (a_x - 110, 280), (db_x + 100, 280), label="Update status")

    draw_step_box(draw, (o_x, 360), "Owner Sets Up Menu Categories,\nFood Items & 8 Macros", fill="#EFF6FF", outline="#2563EB")
    draw_db_store(draw, (db_x, 360), "D5", "categories / D6 food_items")
    draw_connector(draw, (o_x - 110, 360), (db_x + 100, 360), label="Insert Catalog")

    draw_step_box(draw, (o_x, 440), "Configure Dining Tables\n& Generate Active QR Codes", fill="#EFF6FF", outline="#2563EB")
    draw_db_store(draw, (db_x, 440), "D9", "tables / D10 qr_tokens")
    draw_connector(draw, (o_x - 110, 440), (db_x + 100, 440), label="Generate Tokens")

    # ================= 2. DINING CONTEXT & BROWSING PHASE (Y: 530 to 850) =================
    draw_step_box(draw, (c_x, 540), "Scan Dining Table QR Code\nwith Smartphone Camera", fill="#F0FDF4", outline="#16A34A")
    draw_connector(draw, (c_x, 567), (c_x, 620))
    
    draw_step_box(draw, (c_x, 647), "Resolve Table & Branch Context\nvia QR Token Validation", fill="#F0FDF4", outline="#16A34A")
    draw_db_store(draw, (db_x, 647), "D10", "qr_tokens (Validate)")
    draw_connector(draw, (c_x + 110, 647), (db_x - 100, 647), label="Query Token")

    draw_step_box(draw, (c_x, 750), "Browse Digital Menu & Filter\nby Dietary Type / Categories", fill="#F0FDF4", outline="#16A34A")
    draw_connector(draw, (c_x, 674), (c_x, 723))
    draw_db_store(draw, (db_x, 750), "D6", "food_items & variants")
    draw_connector(draw, (db_x - 100, 750), (c_x + 110, 750), label="Return Menu")

    draw_step_box(draw, (c_x, 860), "Configure Dish: Select Portion Size,\nCustomization Add-ons & Quantity", fill="#F0FDF4", outline="#16A34A")
    draw_connector(draw, (c_x, 777), (c_x, 833))

    draw_step_box(draw, (c_x, 970), "Calculate Scaled 8 Macros & Price\nAdd Item to LocalStorage Cart", fill="#F0FDF4", outline="#16A34A")
    draw_connector(draw, (c_x, 887), (c_x, 943))

    # ================= 3. CHECKOUT & PAYMENT PHASE (CRITICAL CORRECTION) (Y: 1080 to 1420) =================
    draw_step_box(draw, (c_x, 1080), "Review Cart, Apply Recommendations\n& Proceed to Checkout", fill="#F0FDF4", outline="#16A34A")
    draw_connector(draw, (c_x, 997), (c_x, 1053))

    draw_step_box(draw, (c_x, 1190), "Enter Customer Name & Mobile\nSelect Payment Method (Cash/UPI/Card)", fill="#F0FDF4", outline="#16A34A")
    draw_connector(draw, (c_x, 1107), (c_x, 1163))

    draw_step_box(draw, (c_x, 1310), "Server Cart Revalidation &\nExecute Payment Simulation", fill="#FEF3C7", outline="#D97706")
    draw_connector(draw, (c_x, 1217), (c_x, 1283))

    # Insert into Database: Customers, Orders, Items, Customizations, Payments!
    draw_db_store(draw, (db_x, 1260), "D11", "customers (Profile)")
    draw_db_store(draw, (db_x, 1310), "D12", "orders & D13 order_items")
    draw_db_store(draw, (db_x, 1360), "D15", "payments (Strict 1:1)")
    
    draw_connector(draw, (c_x + 110, 1310), (db_x - 100, 1310), label="Insert Order & Payment (Pre-Kitchen)")

    # ================= 4. KITCHEN FULFILLMENT & TRACKING PHASE (Y: 1470 to 2200) =================
    draw_step_box(draw, (k_x, 1470), "Live Orders Kanban Board\nReceives New Order Card", fill="#FFF1F2", outline="#E11D48")
    draw_connector(draw, (db_x + 100, 1310), (k_x - 110, 1470), label="Poll Placed Order")

    draw_step_box(draw, (k_x, 1580), "Kitchen Accepts Order\n(status moves to 'accepted')", fill="#FFF1F2", outline="#E11D48")
    draw_connector(draw, (k_x, 1497), (k_x, 1553))
    draw_connector(draw, (k_x - 110, 1580), (db_x + 100, 1580), label="Update status")
    draw_db_store(draw, (db_x, 1580), "D12", "orders (Status: accepted)")

    draw_step_box(draw, (c_x, 1580), "Customer Live Order Tracking\nPolls Status: 'Order Accepted'", fill="#F0FDF4", outline="#16A34A")
    draw_connector(draw, (db_x - 100, 1580), (c_x + 110, 1580), label="Status Poll")

    draw_step_box(draw, (k_x, 1720), "Kitchen Chef Starts Preparation\n(status moves to 'preparing')", fill="#FFF1F2", outline="#E11D48")
    draw_connector(draw, (k_x, 1607), (k_x, 1693))
    draw_connector(draw, (k_x - 110, 1720), (db_x + 100, 1720), label="Update status")
    draw_db_store(draw, (db_x, 1720), "D12", "orders (Status: preparing)")

    draw_step_box(draw, (c_x, 1720), "Live Tracker Progress Updates:\n'Preparing in Kitchen'", fill="#F0FDF4", outline="#16A34A")
    draw_connector(draw, (db_x - 100, 1720), (c_x + 110, 1720), label="Status Poll")

    draw_step_box(draw, (k_x, 1860), "Meal Ready & Bell Rings\n(status moves to 'ready')", fill="#FFF1F2", outline="#E11D48")
    draw_connector(draw, (k_x, 1747), (k_x, 1833))
    draw_connector(draw, (k_x - 110, 1860), (db_x + 100, 1860), label="Update status")
    draw_db_store(draw, (db_x, 1860), "D12", "orders (Status: ready)")

    draw_step_box(draw, (c_x, 1860), "Live Tracker Progress Updates:\n'Ready for Table Service'", fill="#F0FDF4", outline="#16A34A")
    draw_connector(draw, (db_x - 100, 1860), (c_x + 110, 1860), label="Status Poll")

    draw_step_box(draw, (k_x, 2000), "Service Staff Delivers to Table\n& Marks Order 'completed'", fill="#FFF1F2", outline="#E11D48")
    draw_connector(draw, (k_x, 1887), (k_x, 1973))
    draw_connector(draw, (k_x - 110, 2000), (db_x + 100, 2000), label="Update status")
    draw_db_store(draw, (db_x, 2000), "D12", "orders (Status: completed)")

    # ================= 5. REVIEWS & OWNER ANALYTICS PHASE (Y: 2120 to 2500) =================
    draw_step_box(draw, (c_x, 2140), "Customer Receives Meal at Table\nSubmits Rating (1-5) & Comments", fill="#F0FDF4", outline="#16A34A")
    draw_connector(draw, (c_x, 1887), (c_x, 2113))

    draw_db_store(draw, (db_x, 2140), "D16", "reviews (Ratings & Comments)")
    draw_connector(draw, (c_x + 110, 2140), (db_x - 100, 2140), label="Insert Review")

    draw_step_box(draw, (o_x, 2140), "Owner Reviews Customer Feedback\n& Submits Persisted Reply", fill="#EFF6FF", outline="#2563EB")
    draw_connector(draw, (db_x + 100, 2140), (o_x - 110, 2140), label="Read & Reply")

    draw_step_box(draw, (o_x, 2300), "View Sales KPIs, Hourly Demand\n& Top Macro-Nutrient Metrics", fill="#EFF6FF", outline="#2563EB")
    draw_db_store(draw, (db_x, 2300), "D12", "orders (Aggregated Analytics)")
    draw_connector(draw, (db_x + 100, 2300), (o_x - 110, 2300), label="Aggregate Sales")

    # ================= 6. SUPER ADMIN GOVERNANCE (Y: 2450 to 2800) =================
    draw_step_box(draw, (a_x, 2450), "Inspect Active Tenant Portals\nRead-Only Quality Audit", fill="#F5F3FF", outline="#7C3AED")
    draw_connector(draw, (a_x - 110, 2450), (o_x + 110, 2450), label="Portal Inspect")

    draw_step_box(draw, (a_x, 2600), "Manage Platform Users & Access\nEnforce Tenant Isolation", fill="#F5F3FF", outline="#7C3AED")
    draw_db_store(draw, (db_x, 2600), "D3", "users & D1 roles")
    draw_connector(draw, (a_x - 110, 2600), (db_x + 100, 2600), label="Audit Users")

    # Legend / Process Flow Compliance Box
    leg_x = 80
    leg_y = 2750
    draw.rounded_rectangle([leg_x, leg_y, leg_x + 1200, leg_y + 350], radius=8, fill="#F8FAFC", outline="#CBD5E1", width=2)
    draw.text((leg_x + 20, leg_y + 16), "PROCESS FLOW VERIFICATION & CRITICAL ARCHITECTURAL RECTIFICATIONS:", font=get_font(14, bold=True), fill="#0F172A")
    pf_notes = [
        "1. CRITICAL SEQUENCE RECTIFICATION: Kitchen Staff NEVER triggers payment insertion. Payment insertion",
        "   is strictly executed during Customer Checkout (CheckoutController -> PaymentService -> D15 payments).",
        "2. DATA STORE TYPO CORRECTED: Table store renamed from singular 'D13 review' to plural 'D16 reviews'.",
        "3. ADDED MISSING DATA STORES: qr_tokens, restaurant_tables, branches, categories, users, roles.",
        "4. KITCHEN LIFECYCLE EXTENDED: Complete 5-stage progression (placed -> accepted -> preparing -> ready -> completed).",
        "5. SUPER ADMIN SWIMLANE INTEGRATED: Covers tenant registration approval, portal inspection, and user RBAC governance.",
        "6. LIVE TRACKING CLARITY: Documented as table polling rather than WebSockets, exactly matching implementation."
    ]
    cy = leg_y + 46
    for pfn in pf_notes:
        draw.text((leg_x + 20, cy), pfn, font=get_font(11), fill="#334155")
        cy += 24

    out1 = r"d:\NEW healthy bite\diagrams\06_Process_Flow_Diagram.png"
    out2 = r"d:\NEW healthy bite\diagrams\process flow diagram\process flow diagram.png"
    img.save(out1, "PNG", dpi=(300, 300))
    img.save(out2, "PNG", dpi=(300, 300))
    print(f"Generated Process Flow Diagram: {out1} & {out2} ({canvas_w}x{canvas_h})")

if __name__ == "__main__":
    generate()
