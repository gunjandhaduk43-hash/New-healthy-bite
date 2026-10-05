"""
Generate Healthy Bite Final Corrected UML Activity Diagrams (AD-01 to AD-05)
Preserves college-approved format:
- Clean vertical flow orientation
- UML standard rounded action states (pill/stadium shape)
- Decision diamonds with bracketed guard conditions [condition]
- Solid initial nodes and encircled bullseye terminal nodes
- Full alternate paths, error branches, and 8-macro calculations
Outputs:
- diagrams/activity diagram/AD-01 — Customer Ordering Workflow.png & diagrams/05_Activity_Diagram_01.png
- diagrams/activity diagram/AD-02 — Food Customization & Price_Nutrition Calculation.png & diagrams/05_Activity_Diagram_02.png
- diagrams/activity diagram/AD-03 — Kitchen Live Order Management.drawio.png & diagrams/05_Activity_Diagram_03.png
- diagrams/activity diagram/AD-04 — Restaurant Owner Food_Menu Management.drawio.png & diagrams/05_Activity_Diagram_04.png
- diagrams/activity diagram/AD-05 — Super Admin Restaurant Management.drawio.png & diagrams/05_Activity_Diagram_05.png
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

def draw_start_node(draw, center, r=16, fill="#0F172A"):
    cx, cy = center
    draw.ellipse([cx - r, cy - r, cx + r, cy + r], fill=fill, outline=None)

def draw_bullseye(draw, center, r=18, ring="#0F172A", dot="#0F172A"):
    cx, cy = center
    draw.ellipse([cx - r, cy - r, cx + r, cy + r], fill="#FFFFFF", outline=ring, width=2)
    ir = r - 5
    draw.ellipse([cx - ir, cy - ir, cx + ir, cy + ir], fill=dot, outline=None)

def draw_action_state(draw, center, text, w=320, h=54, fill="#F8FAFC", outline="#0284C7", width=2):
    cx, cy = center
    x1, y1 = cx - w // 2, cy - h // 2
    x2, y2 = cx + w // 2, cy + h // 2
    draw.rounded_rectangle([x1, y1, x2, y2], radius=16, fill=fill, outline=outline, width=width)
    
    f = get_font(12, bold=False)
    lines = text.split("\n")
    line_h = 16
    total_h = len(lines) * line_h
    curr_y = cy - total_h // 2 + 2
    for l in lines:
        bbox = draw.textbbox((0, 0), l, font=f)
        tw = bbox[2] - bbox[0]
        draw.text((cx - tw // 2, curr_y), l, font=f, fill="#0F172A")
        curr_y += line_h

def draw_decision(draw, center, text="", w=140, h=60, fill="#FEF3C7", outline="#D97706", width=2):
    cx, cy = center
    hw, hh = w // 2, h // 2
    pts = [(cx, cy - hh), (cx + hw, cy), (cx, cy + hh), (cx - hw, cy)]
    draw.polygon(pts, fill=fill, outline=outline)
    draw.line(pts + [pts[0]], fill=outline, width=width)
    
    if text:
        f = get_font(10, bold=True)
        lines = text.split("\n")
        line_h = 13
        total_h = len(lines) * line_h
        curr_y = cy - total_h // 2 + 1
        for l in lines:
            bbox = draw.textbbox((0, 0), l, font=f)
            tw = bbox[2] - bbox[0]
            draw.text((cx - tw // 2, curr_y), l, font=f, fill="#92400E")
            curr_y += line_h

def draw_down_arrow(draw, p1, p2, label="", fill="#334155", width=2, arrow_sz=8):
    x1, y1 = p1
    x2, y2 = p2
    draw.line([p1, p2], fill=fill, width=width)
    # Down arrow head at p2
    draw.polygon([(x2, y2), (x2 - arrow_sz, y2 - arrow_sz * 1.5), (x2 + arrow_sz, y2 - arrow_sz * 1.5)], fill=fill)
    if label:
        f = get_font(11, bold=True)
        draw.text((x2 + 8, (y1 + y2) // 2 - 8), label, font=f, fill="#2563EB")

# ==============================================================================
# AD-01: Customer Ordering Workflow
# ==============================================================================
def generate_ad01():
    w, h = 1000, 2400
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    # Header
    draw.rectangle([0, 0, w, 80], fill="#0F172A")
    draw.text((30, 16), "AD-01: CUSTOMER ORDERING WORKFLOW", font=get_font(20, bold=True), fill="#FFFFFF")
    draw.text((30, 48), "UML Activity Diagram • End-to-End Table QR Dining, Cart Validation, Tracking & Review", font=get_font(12), fill="#94A3B8")

    cx = 400
    cy = 130
    draw_start_node(draw, (cx, cy))

    steps = [
        ("Scan Table QR Code with Smartphone Camera", 80),
        ("Resolve Dining Context via QrService", 80),
    ]
    curr_y = cy
    for s, dy in steps:
        draw_down_arrow(draw, (cx, curr_y), (cx, curr_y + dy - 27))
        curr_y += dy
        draw_action_state(draw, (cx, curr_y), s, w=380, h=54)

    # Decision 1: QR Valid?
    draw_down_arrow(draw, (cx, curr_y + 27), (cx, curr_y + 80 - 30))
    curr_y += 80
    draw_decision(draw, (cx, curr_y), "QR Token\nValid & Active?", w=150, h=60)

    # Branch: [No] -> Error Page -> Bullseye
    err_x = 750
    draw.line([(cx + 75, curr_y), (err_x, curr_y)], fill="#DC2626", width=2)
    draw.text((cx + 85, curr_y - 18), "[Invalid / Expired]", font=get_font(11, bold=True), fill="#DC2626")
    draw_action_state(draw, (err_x, curr_y + 60), "Display Invalid QR Error\nRequest Server Assistance", w=220, h=50, fill="#FEF2F2", outline="#EF4444")
    draw_down_arrow(draw, (err_x, curr_y), (err_x, curr_y + 35), fill="#DC2626")
    draw_down_arrow(draw, (err_x, curr_y + 85), (err_x, curr_y + 130), fill="#DC2626")
    draw_bullseye(draw, (err_x, curr_y + 148))

    # Branch: [Yes]
    draw.text((cx + 10, curr_y + 36), "[Valid]", font=get_font(11, bold=True), fill="#16A34A")

    main_steps = [
        ("Load Active Restaurant Digital Menu & Categories", 80),
        ("Browse Dishes & Filter by Dietary Preference (Veg, Jain, etc.)", 80),
        ("Select Food Dish, Choose Portion Variant & Add-ons", 80),
        ("Calculate Live Item Price & Scaled 8 Nutrition Macros", 80),
        ("Add Configured Dish to Client LocalStorage Cart", 80),
        ("Review Cart Items, Subtotal, Tax & Total Price", 80),
    ]

    for s, dy in main_steps:
        draw_down_arrow(draw, (cx, curr_y + (27 if s != main_steps[0][0] else 30)), (cx, curr_y + dy - 27))
        curr_y += dy
        draw_action_state(draw, (cx, curr_y), s, w=400, h=54)

    # Decision 2: Cart Empty?
    draw_down_arrow(draw, (cx, curr_y + 27), (cx, curr_y + 80 - 30))
    curr_y += 80
    draw_decision(draw, (cx, curr_y), "Cart Empty?", w=140, h=60)
    # [Yes] Loop back to browse
    draw.line([(cx - 70, curr_y), (140, curr_y)], fill="#D97706", width=2)
    draw.line([(140, curr_y), (140, 450)], fill="#D97706", width=2)
    draw.line([(140, 450), (cx - 200, 450)], fill="#D97706", width=2)
    draw.polygon([(cx - 200, 450), (cx - 212, 444), (cx - 212, 456)], fill="#D97706")
    draw.text((150, curr_y - 18), "[Yes: Empty]", font=get_font(11, bold=True), fill="#D97706")

    draw.text((cx + 10, curr_y + 36), "[No: Has Items]", font=get_font(11, bold=True), fill="#16A34A")

    checkout_steps = [
        ("Proceed to Checkout & Enter Customer Name/Mobile", 80),
        ("Select Dining Type (Dine-In) & Payment Method (Cash/UPI/Card)", 80),
        ("Server Revalidation of Cart, Live Prices & Availability", 80),
        ("Process Simulated Payment Tender via PaymentService", 80),
    ]

    for s, dy in checkout_steps:
        draw_down_arrow(draw, (cx, curr_y + (27 if s != checkout_steps[0][0] else 30)), (cx, curr_y + dy - 27))
        curr_y += dy
        draw_action_state(draw, (cx, curr_y), s, w=410, h=54)

    # Decision 3: Payment Success?
    draw_down_arrow(draw, (cx, curr_y + 27), (cx, curr_y + 80 - 30))
    curr_y += 80
    draw_decision(draw, (cx, curr_y), "Payment\nSucceeded?", w=140, h=60)

    # [No] Retry payment
    draw.line([(cx + 70, curr_y), (720, curr_y)], fill="#DC2626", width=2)
    draw.line([(720, curr_y), (720, 1370)], fill="#DC2626", width=2)
    draw.line([(720, 1370), (cx + 205, 1370)], fill="#DC2626", width=2)
    draw.polygon([(cx + 205, 1370), (cx + 217, 1364), (cx + 217, 1376)], fill="#DC2626")
    draw.text((cx + 75, curr_y - 18), "[No: Failed]", font=get_font(11, bold=True), fill="#DC2626")

    draw.text((cx + 10, curr_y + 36), "[Yes: Approved]", font=get_font(11, bold=True), fill="#16A34A")

    post_steps = [
        ("Create Order, Order Items & Customization Snapshots in DB", 80),
        ("Display Order Confirmation & Order Number (e.g. HB-1001)", 80),
        ("Redirect to Live Order Tracking Page (/menu/tracking/{orderNumber})", 80),
        ("Poll Order Status: Placed -> Accepted -> Preparing -> Ready -> Completed", 80),
        ("Food Served at Table & Order Marked 'completed'", 80),
        ("Submit Verified Order Review & Rating (1-5 Stars)", 80),
    ]

    for s, dy in post_steps:
        draw_down_arrow(draw, (cx, curr_y + (27 if s != post_steps[0][0] else 30)), (cx, curr_y + dy - 27))
        curr_y += dy
        draw_action_state(draw, (cx, curr_y), s, w=420, h=54, fill="#F0FDF4" if "Review" in s or "Completed" in s else "#F8FAFC")

    # End Bullseye
    draw_down_arrow(draw, (cx, curr_y + 27), (cx, curr_y + 70 - 18))
    curr_y += 70
    draw_bullseye(draw, (cx, curr_y))

    p1 = r"d:\NEW healthy bite\diagrams\activity diagram\AD-01 — Customer Ordering Workflow.png"
    p2 = r"d:\NEW healthy bite\diagrams\05_Activity_Diagram_01.png"
    img.save(p1, "PNG", dpi=(300, 300))
    img.save(p2, "PNG", dpi=(300, 300))
    print(f"Generated AD-01: {p1} & {p2}")

# ==============================================================================
# AD-02: Food Customization & Price / Nutrition Calculation
# ==============================================================================
def generate_ad02():
    w, h = 1000, 1900
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    # Header
    draw.rectangle([0, 0, w, 80], fill="#0F172A")
    draw.text((30, 16), "AD-02: FOOD CUSTOMIZATION & PRICE/NUTRITION CALCULATION", font=get_font(20, bold=True), fill="#FFFFFF")
    draw.text((30, 48), "UML Activity Diagram • Variant Scaling, Customization Deltas & All 8 Nutritional Macros", font=get_font(12), fill="#94A3B8")

    cx = 500
    cy = 130
    draw_start_node(draw, (cx, cy))

    steps_top = [
        ("Customer Clicks on Dish Card in Menu View", 80),
        ("Fetch Dish Master Record (Base Price & 8 Nutritional Macros)", 80),
        ("Open Interactive Dish Customization Modal", 80),
        ("Select Portion Size Variant (e.g. Regular, Large, Double)", 80),
        ("Select Customization Add-ons / Ingredients / Dietary Choices", 80),
        ("Select Item Quantity (Default = 1, min = 1, max = 50)", 80), # MOVED BEFORE CALCULATION!
    ]

    curr_y = cy
    for s, dy in steps_top:
        draw_down_arrow(draw, (cx, curr_y), (cx, curr_y + dy - 27))
        curr_y += dy
        draw_action_state(draw, (cx, curr_y), s, w=440, h=54)

    # Decision: Required Customization Constraints Met?
    draw_down_arrow(draw, (cx, curr_y + 27), (cx, curr_y + 80 - 30))
    curr_y += 80
    draw_decision(draw, (cx, curr_y), "Required Groups\nMin/Max Met?", w=150, h=60)

    # [No] Loop back
    draw.line([(cx - 75, curr_y), (160, curr_y)], fill="#DC2626", width=2)
    draw.line([(160, curr_y), (160, 450)], fill="#DC2626", width=2)
    draw.line([(160, 450), (cx - 220, 450)], fill="#DC2626", width=2)
    draw.polygon([(cx - 220, 450), (cx - 232, 444), (cx - 232, 456)], fill="#DC2626")
    draw.text((170, curr_y - 18), "[No: Selection Incomplete]", font=get_font(11, bold=True), fill="#DC2626")

    draw.text((cx + 10, curr_y + 36), "[Yes: Valid]", font=get_font(11, bold=True), fill="#16A34A")

    calc_steps = [
        ("Calculate Scaled Nutrition per Item:\nMacro = (Base Macro + Variant Adj + Sum(Add-on Adjs)) * Quantity\n[8 Tracked: Calories, Protein, Carbs, Fat, Fiber, Sugar, Sodium, Caffeine]", 95),
        ("Calculate Scaled Unit Price:\nPrice = (Base Price + Variant Price Adj + Sum(Add-on Price Adjs)) * Quantity", 85),
        ("Render Live Dynamic Price & Macro Breakdown Bar in UI Modal", 80),
        ("Customer Clicks 'Add to Cart' Button", 80),
        ("Store Configured Item Record in LocalStorage Cart with Snapshot Meta", 80),
        ("Close Customization Modal & Display Floating Cart Notification Badge", 80),
    ]

    for s, dy in calc_steps:
        draw_down_arrow(draw, (cx, curr_y + (27 if s != calc_steps[0][0] else 30)), (cx, curr_y + dy - (34 if "\n" in s else 27)))
        curr_y += dy
        h_box = 68 if "\n\n" in s or "[8" in s else 54
        draw_action_state(draw, (cx, curr_y), s, w=480, h=h_box, fill="#EFF6FF" if "Calculate" in s else "#F8FAFC", outline="#2563EB" if "Calculate" in s else "#0284C7")

    draw_down_arrow(draw, (cx, curr_y + 27), (cx, curr_y + 70 - 18))
    curr_y += 70
    draw_bullseye(draw, (cx, curr_y))

    p1 = r"d:\NEW healthy bite\diagrams\activity diagram\AD-02 — Food Customization & Price_Nutrition Calculation.png"
    p2 = r"d:\NEW healthy bite\diagrams\05_Activity_Diagram_02.png"
    img.save(p1, "PNG", dpi=(300, 300))
    img.save(p2, "PNG", dpi=(300, 300))
    print(f"Generated AD-02: {p1} & {p2}")

# ==============================================================================
# AD-03: Kitchen Live Order Management
# ==============================================================================
def generate_ad03():
    w, h = 1000, 1800
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    # Header
    draw.rectangle([0, 0, w, 80], fill="#0F172A")
    draw.text((30, 16), "AD-03: KITCHEN LIVE ORDER MANAGEMENT", font=get_font(20, bold=True), fill="#FFFFFF")
    draw.text((30, 48), "UML Activity Diagram • Kitchen Kanban Transitions (placed -> accepted -> preparing -> ready -> completed)", font=get_font(12), fill="#94A3B8")

    cx = 400
    cy = 130
    draw_start_node(draw, (cx, cy))

    steps_top = [
        ("Kitchen Staff Logs In & Navigates to Kitchen Kanban Board (/owner/orders)", 80),
        ("Poll Orders API for Active Branch (Auto-Refresh every 10 seconds)", 80),
        ("New Order Card Appears in 'New Orders' Column (status = 'placed')", 80),
        ("Staff Inspects Table Number, Line-Items, Portions & Customizations", 80),
    ]

    curr_y = cy
    for s, dy in steps_top:
        draw_down_arrow(draw, (cx, curr_y), (cx, curr_y + dy - 27))
        curr_y += dy
        draw_action_state(draw, (cx, curr_y), s, w=440, h=54)

    # Decision: Can Fulfill?
    draw_down_arrow(draw, (cx, curr_y + 27), (cx, curr_y + 80 - 30))
    curr_y += 80
    draw_decision(draw, (cx, curr_y), "Can Kitchen\nFulfill Order?", w=140, h=60)

    # [No] Cancellation path
    canc_x = 750
    draw.line([(cx + 70, curr_y), (canc_x, curr_y)], fill="#DC2626", width=2)
    draw.text((cx + 80, curr_y - 18), "[Out of Stock / Reject]", font=get_font(11, bold=True), fill="#DC2626")
    draw_action_state(draw, (canc_x, curr_y + 60), "Update Order Status to 'cancelled'\nRecord Rejection Reason", w=220, h=50, fill="#FEF2F2", outline="#EF4444")
    draw_down_arrow(draw, (canc_x, curr_y), (canc_x, curr_y + 35), fill="#DC2626")
    draw_down_arrow(draw, (canc_x, curr_y + 85), (canc_x, curr_y + 130), fill="#DC2626")
    draw_bullseye(draw, (canc_x, curr_y + 148))

    draw.text((cx + 10, curr_y + 36), "[Yes: Accept]", font=get_font(11, bold=True), fill="#16A34A")

    k_steps = [
        ("Click 'Accept Order' Button (Status moves to 'accepted')", 80),
        ("Order Card Shifts to 'Accepted' Column on Kanban Board", 80),
        ("Click 'Start Preparing' Button (Status moves to 'preparing')", 80),
        ("Kitchen Chef Prepares Dishes according to Customization Notes", 80),
        ("Click 'Mark Ready' Button (Status moves to 'ready')", 80),
        ("Audible Alert & Order Card Moves to 'Ready' Column", 80),
        ("Service Staff Delivers Food Items to Customer's Dining Table", 80),
        ("Staff Clicks 'Complete Order' (Status moves to 'completed')", 80), # CRITICAL CORRECTION
        ("Order Archived to Completed Sales History in Database", 80),
    ]

    for s, dy in k_steps:
        draw_down_arrow(draw, (cx, curr_y + (27 if s != k_steps[0][0] else 30)), (cx, curr_y + dy - 27))
        curr_y += dy
        draw_action_state(draw, (cx, curr_y), s, w=440, h=54, fill="#FFF1F2" if "moves to 'ready'" in s or "moves to 'completed'" in s else "#F8FAFC")

    draw_down_arrow(draw, (cx, curr_y + 27), (cx, curr_y + 70 - 18))
    curr_y += 70
    draw_bullseye(draw, (cx, curr_y))

    p1 = r"d:\NEW healthy bite\diagrams\activity diagram\AD-03 — Kitchen Live Order Management.drawio.png"
    p2 = r"d:\NEW healthy bite\diagrams\05_Activity_Diagram_03.png"
    img.save(p1, "PNG", dpi=(300, 300))
    img.save(p2, "PNG", dpi=(300, 300))
    print(f"Generated AD-03: {p1} & {p2}")

# ==============================================================================
# AD-04: Restaurant Owner Food / Menu Management
# ==============================================================================
def generate_ad04():
    w, h = 1200, 1800
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    # Header
    draw.rectangle([0, 0, w, 80], fill="#0F172A")
    draw.text((30, 16), "AD-04: RESTAURANT OWNER FOOD & MENU MANAGEMENT", font=get_font(20, bold=True), fill="#FFFFFF")
    draw.text((30, 48), "UML Activity Diagram • Category CRUD, Dish Creation, 8 Macros, Portion Variants & Availability Toggles", font=get_font(12), fill="#94A3B8")

    cx = 600
    cy = 130
    draw_start_node(draw, (cx, cy))

    draw_down_arrow(draw, (cx, cy), (cx, 185))
    draw_action_state(draw, (cx, 210), "Owner Authenticates & Accesses Menu Management Portal (/owner/menu)", w=480, h=50)

    # Decision: Action Type
    draw_down_arrow(draw, (cx, 235), (cx, 290))
    draw_decision(draw, (cx, 320), "Select Management\nAction", w=160, h=60)

    # 4 Parallel Branches:
    # 1. Category Management (x = 180)
    # 2. Add New Food Dish (x = 460)
    # 3. Edit Dish & Macros (x = 740)
    # 4. Toggle Availability / Delete (x = 1020)
    branches = [
        (180, "[Manage Categories]", [
            "Create New Menu Category\n(Name, Slug, Description, Image)",
            "Validate Category Uniqueness\n& Assign Sort Order",
            "Persist to `categories` Table"
        ]),
        (460, "[Add New Dish]", [
            "Enter Dish Details & Price\n(Name, Category, Food Type)",
            "Enter 8 Base Nutrition Macros\n(kcal, protein, carbs, fat, fiber, etc.)",
            "Configure Portion Variants\n& Customization Add-on Groups",
            "Save Dish to `food_items` & Child Tables"
        ]),
        (740, "[Edit Dish & Macros]", [
            "Select Existing Dish from Catalog View",
            "Update Recipe, Price or 8 Nutritional Values",
            "Update Portion Adjustments\n& Min/Max Selection Rules",
            "Save Updates to Database"
        ]),
        (1020, "[Toggle / Delete]", [
            "Select Dish from Catalog List",
            "Click Instant Availability Switch\n(is_available = 1 / 0)",
            "Dispatch PATCH API Request\n& Update Cache Instantly"
        ])
    ]

    merge_y = 780
    for bx, blabel, bsteps in branches:
        # Route from decision
        draw.line([(cx, 350 if bx in [460, 740] else (320 if bx < cx else 320)), (bx, 320)], fill="#334155", width=2)
        draw.line([(bx, 320), (bx, 380)], fill="#334155", width=2)
        draw.polygon([(bx, 380), (bx - 6, 370), (bx + 6, 370)], fill="#334155")
        draw.text((bx - 50, 340), blabel, font=get_font(10, bold=True), fill="#2563EB")

        by = 410
        for s in bsteps:
            draw_action_state(draw, (bx, by), s, w=230, h=54)
            draw_down_arrow(draw, (bx, by + 27), (bx, by + 70 - 27))
            by += 70

        # Line to merge bar
        draw.line([(bx, by - 43), (bx, merge_y)], fill="#334155", width=2)

    # Merge Bar
    draw.rectangle([120, merge_y, 1080, merge_y + 8], fill="#0F172A")
    draw_down_arrow(draw, (cx, merge_y + 8), (cx, merge_y + 70 - 27))

    curr_y = merge_y + 70
    draw_action_state(draw, (cx, curr_y), "Refresh Menu Catalog View with Updated Dishes & Real-Time Availability", w=520, h=54)
    draw_down_arrow(draw, (cx, curr_y + 27), (cx, curr_y + 80 - 27))
    curr_y += 80
    draw_action_state(draw, (cx, curr_y), "Updated Menu Instantly Synchronized to Customer QR Menu View", w=520, h=54)

    draw_down_arrow(draw, (cx, curr_y + 27), (cx, curr_y + 70 - 18))
    curr_y += 70
    draw_bullseye(draw, (cx, curr_y))

    p1 = r"d:\NEW healthy bite\diagrams\activity diagram\AD-04 — Restaurant Owner Food_Menu Management.drawio.png"
    p2 = r"d:\NEW healthy bite\diagrams\05_Activity_Diagram_04.png"
    img.save(p1, "PNG", dpi=(300, 300))
    img.save(p2, "PNG", dpi=(300, 300))
    print(f"Generated AD-04: {p1} & {p2}")

# ==============================================================================
# AD-05: Super Admin Restaurant Management
# ==============================================================================
def generate_ad05():
    w, h = 1200, 1700
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    # Header
    draw.rectangle([0, 0, w, 80], fill="#0F172A")
    draw.text((30, 16), "AD-05: SUPER ADMIN RESTAURANT & PLATFORM MANAGEMENT", font=get_font(20, bold=True), fill="#FFFFFF")
    draw.text((30, 48), "UML Activity Diagram • Multi-Tenant Onboarding, Approval, Portal Inspection & Suspension Lifecycle", font=get_font(12), fill="#94A3B8")

    cx = 600
    cy = 130
    draw_start_node(draw, (cx, cy))

    draw_down_arrow(draw, (cx, cy), (cx, 185))
    draw_action_state(draw, (cx, 210), "Super Admin Logs In to Platform Administration Portal (/admin/login)", w=480, h=50)

    draw_down_arrow(draw, (cx, 235), (cx, 290))
    draw_action_state(draw, (cx, 315), "View Platform KPIs & Registered Restaurant Applications (/admin/restaurants)", w=480, h=50)

    draw_down_arrow(draw, (cx, 340), (cx, 395))
    draw_action_state(draw, (cx, 420), "Select Target Restaurant Profile & Review Documentation", w=480, h=50)

    # Decision: Action
    draw_down_arrow(draw, (cx, 445), (cx, 500))
    draw_decision(draw, (cx, 530), "Administrative\nDecision", w=160, h=60)

    # 4 Branches:
    # 1. Inspect Portal (x = 180)
    # 2. Approve (x = 460)
    # 3. Keep Pending (x = 740)
    # 4. Suspend (x = 1020)
    branches = [
        (180, "[Inspect Portal]", [
            "Click 'Inspect Portal' Button\n(/admin/portal-inspect)",
            "Assume Read-Only View of\nTenant Live Menu, Orders & Tables",
            "Audit Tenant Compliance"
        ]),
        (460, "[Approve Tenant]", [
            "Update Restaurant Status\nin Database to 'approved'",
            "Activate Owner User Account\n& Primary Branch Record",
            "Send Approval Notification"
        ]),
        (740, "[Keep Pending]", [
            "Request Additional Onboarding\nDocuments from Owner",
            "Status Remains 'pending'\n(NO Database Mutation)",
            "Log Administrative Note"
        ]),
        (1020, "[Suspend Tenant]", [
            "Update Restaurant Status\nin Database to 'suspended'",
            "Revoke Owner & Staff Portal Access\n& Invalidate Sessions",
            "Display Suspension Notice"
        ])
    ]

    merge_y = 960
    for bx, blabel, bsteps in branches:
        draw.line([(cx, 530), (bx, 530)], fill="#334155", width=2)
        draw.line([(bx, 530), (bx, 590)], fill="#334155", width=2)
        draw.polygon([(bx, 590), (bx - 6, 580), (bx + 6, 580)], fill="#334155")
        draw.text((bx - 45, 550), blabel, font=get_font(10, bold=True), fill="#2563EB" if "Approve" in blabel else ("#DC2626" if "Suspend" in blabel else "#475569"))

        by = 620
        for s in bsteps:
            draw_action_state(draw, (bx, by), s, w=230, h=54)
            draw_down_arrow(draw, (bx, by + 27), (bx, by + 70 - 27))
            by += 70

        draw.line([(bx, by - 43), (bx, merge_y)], fill="#334155", width=2)

    # Merge Bar
    draw.rectangle([120, merge_y, 1080, merge_y + 8], fill="#0F172A")
    draw_down_arrow(draw, (cx, merge_y + 8), (cx, merge_y + 70 - 27))

    curr_y = merge_y + 70
    draw_action_state(draw, (cx, curr_y), "Refresh Platform Tenant Management Dashboard with Synchronized State", w=520, h=54)

    draw_down_arrow(draw, (cx, curr_y + 27), (cx, curr_y + 70 - 18))
    curr_y += 70
    draw_bullseye(draw, (cx, curr_y))

    p1 = r"d:\NEW healthy bite\diagrams\activity diagram\AD-05 — Super Admin Restaurant Management.drawio.png"
    p2 = r"d:\NEW healthy bite\diagrams\05_Activity_Diagram_05.png"
    img.save(p1, "PNG", dpi=(300, 300))
    img.save(p2, "PNG", dpi=(300, 300))
    print(f"Generated AD-05: {p1} & {p2}")

def generate_all():
    generate_ad01()
    generate_ad02()
    generate_ad03()
    generate_ad04()
    generate_ad05()

if __name__ == "__main__":
    generate_all()
