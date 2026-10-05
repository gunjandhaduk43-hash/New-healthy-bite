r"""
Generate Healthy Bite Clean Black & White UML Use Case Diagram
Matches exact college visual format from d:\NEW healthy bite\old format diagram:
- Pure Black & White (No colors, no pastel fills)
- Classic typography: Serif Header + Pill Subtitle
- Exact stick figure actor placement (Customer & Kitchen on left; Owner, Manager, Super Admin on right)
- Clean ellipses with radiating solid lines and dashed <<include>> arrows
- Corrected business logic: proper <<include>> on table assignment, complete kitchen states
Outputs:
- diagrams/Healthy_Bite_Use_Case_BW.png
- diagrams/use case diagaram/Healthy Bite Use Case Diagram.png
"""

import math
from PIL import Image, ImageDraw, ImageFont

FONT_SERIF_BOLD = r"C:\Windows\Fonts\timesbd.ttf"
FONT_SANS_BOLD = r"C:\Windows\Fonts\arialbd.ttf"
FONT_SANS_REG = r"C:\Windows\Fonts\arial.ttf"

def get_serif_font(size=14):
    try:
        return ImageFont.truetype(FONT_SERIF_BOLD, size)
    except Exception:
        return ImageFont.load_default()

def get_sans_font(size=14, bold=False):
    p = FONT_SANS_BOLD if bold else FONT_SANS_REG
    try:
        return ImageFont.truetype(p, size)
    except Exception:
        return ImageFont.load_default()

def draw_stickman(draw, center, label, scale=1.0):
    cx, cy = center
    # Head
    hr = int(22 * scale)
    hcy = cy - int(38 * scale)
    draw.ellipse([cx - hr, hcy - hr, cx + hr, hcy + hr], outline="#000000", width=3, fill="#FFFFFF")
    
    # Spine
    neck_y = hcy + hr
    pelvis_y = neck_y + int(46 * scale)
    draw.line([(cx, neck_y), (cx, pelvis_y)], fill="#000000", width=3)
    
    # Arms
    arm_y = neck_y + int(14 * scale)
    aw = int(32 * scale)
    draw.line([(cx - aw, arm_y + int(14 * scale)), (cx, arm_y), (cx + aw, arm_y + int(14 * scale))], fill="#000000", width=3)
    
    # Legs
    lw = int(24 * scale)
    lh = int(44 * scale)
    draw.line([(cx, pelvis_y), (cx - lw, pelvis_y + lh)], fill="#000000", width=3)
    draw.line([(cx, pelvis_y), (cx + lw, pelvis_y + lh)], fill="#000000", width=3)
    
    # Label
    f = get_sans_font(int(17 * scale), bold=True)
    lines = label.split("\n")
    line_h = int(21 * scale)
    curr_y = pelvis_y + lh + int(8 * scale)
    for l in lines:
        bbox = draw.textbbox((0, 0), l, font=f)
        tw = bbox[2] - bbox[0]
        draw.text((cx - tw // 2, curr_y), l, font=f, fill="#000000")
        curr_y += line_h

def draw_ellipse_uc(draw, center, text, w=280, h=52, font_size=14):
    cx, cy = center
    hw, hh = w // 2, h // 2
    # Draw pure black outline with pure white fill
    draw.ellipse([cx - hw, cy - hh, cx + hw, cy + hh], fill="#FFFFFF", outline="#000000", width=2)
    
    f = get_sans_font(font_size, bold=False)
    lines = text.split("\n")
    line_h = font_size + 4
    total_h = len(lines) * line_h
    start_y = cy - total_h // 2 + 1
    for l in lines:
        bbox = draw.textbbox((0, 0), l, font=f)
        tw = bbox[2] - bbox[0]
        draw.text((cx - tw // 2, start_y), l, font=f, fill="#000000")
        start_y += line_h

def draw_dashed_include(draw, p1, p2, label="<<include>>"):
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
        draw.line([(sx, sy), (ex, ey)], fill="#000000", width=2)
        
    # Arrow head at p2
    angle = math.atan2(y2 - y1, x2 - x1)
    sz = 8
    a1 = angle + math.pi * 5 / 6
    a2 = angle - math.pi * 5 / 6
    h1 = (x2 + sz * math.cos(a1), y2 + sz * math.sin(a1))
    h2 = (x2 + sz * math.cos(a2), y2 + sz * math.sin(a2))
    # Open or closed arrowhead
    draw.line([h1, p2, h2], fill="#000000", width=2)
    
    # Stereotype text
    mx = (x1 + x2) // 2
    my = (y1 + y2) // 2
    f = get_sans_font(12, bold=False)
    bbox = draw.textbbox((0, 0), label, font=f)
    tw = bbox[2] - bbox[0]
    draw.rectangle([mx - tw // 2 - 2, my - 16, mx + tw // 2 + 2, my], fill="#FFFFFF")
    draw.text((mx - tw // 2, my - 16), label, font=f, fill="#000000")

def generate():
    w, h = 2200, 1600
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    # 1. Main Titles (Centered, Pure Black and White)
    f_title1 = get_serif_font(38)
    bbox1 = draw.textbbox((0, 0), "HEALTHY BITE", font=f_title1)
    draw.text((w // 2 - (bbox1[2] - bbox1[0]) // 2, 20), "HEALTHY BITE", font=f_title1, fill="#000000")

    f_title2 = get_serif_font(34)
    bbox2 = draw.textbbox((0, 0), "USE CASE DIAGRAM", font=f_title2)
    draw.text((w // 2 - (bbox2[2] - bbox2[0]) // 2, 68), "USE CASE DIAGRAM", font=f_title2, fill="#000000")

    # Pill Capsule Subtitle
    pill_w = 460
    pill_h = 36
    pill_x1 = w // 2 - pill_w // 2
    pill_y1 = 118
    draw.rounded_rectangle([pill_x1, pill_y1, pill_x1 + pill_w, pill_y1 + pill_h], radius=18, fill="#FFFFFF", outline="#000000", width=2)
    f_sub = get_sans_font(16, bold=True)
    bbox_sub = draw.textbbox((0, 0), "Digital Restaurant & Food Ordering System", font=f_sub)
    draw.text((w // 2 - (bbox_sub[2] - bbox_sub[0]) // 2, pill_y1 + 8), "Digital Restaurant & Food Ordering System", font=f_sub, fill="#000000")

    # 2. Main Boundary Box (Thick Black Border with Rounded Corners)
    bx1, by1 = 70, 136
    bx2, by2 = 2130, 1565
    draw.rounded_rectangle([bx1, by1, bx2, by2], radius=24, fill=None, outline="#000000", width=3)
    # Redraw pill over the top border line so it overlaps cleanly like the original
    draw.rounded_rectangle([pill_x1, pill_y1, pill_x1 + pill_w, pill_y1 + pill_h], radius=18, fill="#FFFFFF", outline="#000000", width=2)
    draw.text((w // 2 - (bbox_sub[2] - bbox_sub[0]) // 2, pill_y1 + 8), "Digital Restaurant & Food Ordering System", font=f_sub, fill="#000000")

    # 3. Actors Position
    # Customer: Center Left (crossing boundary)
    cust_actor_pos = (70, 560)
    draw_stickman(draw, cust_actor_pos, "Customer", scale=1.1)

    # Kitchen Staff: Bottom Left
    kitchen_actor_pos = (120, 1260)
    draw_stickman(draw, kitchen_actor_pos, "Kitchen\nStaff", scale=1.1)

    # Restaurant Owner: Top Right
    owner_actor_pos = (2050, 420)
    draw_stickman(draw, owner_actor_pos, "Restaurant\nOwner", scale=1.1)

    # Manager: Middle Right
    manager_actor_pos = (2050, 960)
    draw_stickman(draw, manager_actor_pos, "Manager", scale=1.1)

    # Super Admin: Bottom Right
    admin_actor_pos = (2050, 1340)
    draw_stickman(draw, admin_actor_pos, "Super\nAdmin", scale=1.1)

    # Connection origin points from actors
    cust_pt = (70, 560)
    kitchen_pt = (120, 1260)
    owner_pt = (2050, 420)
    manager_pt = (2050, 960)
    admin_pt = (2050, 1340)

    # ================= 4. CUSTOMER USE CASES (Left Column) =================
    # Column 1 X = 360, width = 280
    # Include Column X = 690, width = 200
    cust_bubbles = [
        ("Scan Table QR Code", 190, None),
        ("Browse Categorized Menu", 242, None),
        ("Filter Menu by Diet", 294, None),
        ("Search Food Dishes", 346, None),
        ("Customize Dish\nPortions & Add-ons", 408, ("View Nutrition\nInformation", 408)),
        ("Add Item to Slide-Out Cart", 472, ("View Cart Details", 472)),
        ("Add Recommended Meal Pairing", 526, None),
        ("Review Cart & Modify Quantities", 580, None),
        ("Checkout Order Review", 636, ("Select Payment Method", 636)),
        ("Submit Dine-In Order", 694, ("Table Assignment", 694)),
        ("Submit Takeaway Order", 750, None),
        ("Track Live Kitchen Order Status", 804, None),
        ("Submit Dining Review", 858, None),
    ]

    c_x = 360
    inc_x = 690

    for text, y_pos, inc_info in cust_bubbles:
        h_box = 48 if "\n" in text else 40
        draw.line([cust_pt, (c_x - 140, y_pos)], fill="#000000", width=2)
        draw_ellipse_uc(draw, (c_x, y_pos), text, w=280, h=h_box, font_size=13)
        
        if inc_info:
            inc_text, inc_y = inc_info
            draw_dashed_include(draw, (c_x + 140, y_pos), (inc_x - 100, inc_y), label="<<include>>")
            inc_h = 44 if "\n" in inc_text else 38
            draw_ellipse_uc(draw, (inc_x, inc_y), inc_text, w=200, h=inc_h, font_size=12)

    # ================= 5. KITCHEN STAFF USE CASES (Bottom Left) =================
    kitchen_bubbles = [
        ("Kitchen Staff Login", 1080, "Authenticate User"),
        ("View Kitchen Live Orders", 1140, None),
        ("View Order Item & Customization Details", 1200, None),
        ("Advance Order Status\n(Placed -> Accepted -> Preparing)", 1265, None),
        ("Mark Order Ready for Pickup", 1330, None),
        ("Complete Served Order (status = 'completed')", 1390, None), # Cleanly added
    ]

    k_x = 420
    k_auth_x = 730
    for text, y_pos, auth_target in kitchen_bubbles:
        h_box = 50 if "\n" in text else 42
        draw.line([kitchen_pt, (k_x - 180, y_pos)], fill="#000000", width=2)
        draw_ellipse_uc(draw, (k_x, y_pos), text, w=360, h=h_box, font_size=13)
        if auth_target:
            draw_dashed_include(draw, (k_x + 180, y_pos), (k_auth_x - 85, y_pos), label="<<include>>")
            draw_ellipse_uc(draw, (k_auth_x, y_pos), auth_target, w=170, h=38, font_size=12)

    # ================= 6. RESTAURANT OWNER USE CASES (Top Right) =================
    owner_bubbles = [
        ("Owner / Staff Login", 190, True),
        ("View Owner Overview Dashboard", 242, False),
        ("Monitor Live Orders (Read Only)", 294, False),
        ("View Order Item Breakdown", 346, False),
        ("Create New Food Dish", 398, False),
        ("Toggle Dish In-Stock Availability", 450, False),
        ("Manage Dining Tables & Generate QR", 502, False),
        ("Moderate Customer Reviews & Reply", 554, False),
        ("Create Staff User Account", 606, False),
        ("View Sales & Macro Analytics", 658, False),
    ]

    o_x = 1350
    o_auth_x = 1750
    for text, y_pos, has_auth in owner_bubbles:
        draw_ellipse_uc(draw, (o_x, y_pos), text, w=330, h=40, font_size=13)
        draw.line([(o_x + 165, y_pos), owner_pt], fill="#000000", width=2)
        if has_auth:
            draw_dashed_include(draw, (o_x + 165, y_pos), (o_auth_x - 85, y_pos), label="<<include>>")
            draw_ellipse_uc(draw, (o_auth_x, y_pos), "Authenticate User", w=170, h=38, font_size=12)
            draw.line([(o_auth_x + 85, y_pos), owner_pt], fill="#000000", width=2)

    # ================= 7. MANAGER USE CASES (Middle Right) =================
    mgr_bubbles = [
        ("Manager Login", 740, True),
        ("Monitor Live Orders (Read Only)", 792, False),
        ("View Order Item Breakdown", 844, False),
        ("Menu Management (Add / Update / Toggle)", 896, False),
        ("Manage Dining Tables & Generate QR", 948, False),
        ("View Customer Reviews", 1000, False),
        ("View Sales & Macro Analytics", 1052, False),
    ]

    m_x = 1350
    m_auth_x = 1750
    for text, y_pos, has_auth in mgr_bubbles:
        draw_ellipse_uc(draw, (m_x, y_pos), text, w=330, h=40, font_size=13)
        draw.line([(m_x + 165, y_pos), manager_pt], fill="#000000", width=2)
        if has_auth:
            draw_dashed_include(draw, (m_x + 165, y_pos), (m_auth_x - 85, y_pos), label="<<include>>")
            draw_ellipse_uc(draw, (m_auth_x, y_pos), "Authenticate User", w=170, h=38, font_size=12)
            draw.line([(m_auth_x + 85, y_pos), manager_pt], fill="#000000", width=2)

    # ================= 8. SUPER ADMIN USE CASES (Bottom Right) =================
    admin_bubbles = [
        ("Super Admin Login", 1150, True),
        ("Moderate Restaurant Tenant Lifecycle\n(Approve / Suspend)", 1215, False),
        ("Inspect Restaurant Tenant Portal", 1285, False),
        ("Platform-Wide User Administration", 1345, False),
    ]

    a_x = 1350
    a_auth_x = 1750
    for text, y_pos, has_auth in admin_bubbles:
        h_box = 50 if "\n" in text else 42
        draw_ellipse_uc(draw, (a_x, y_pos), text, w=350, h=h_box, font_size=13)
        draw.line([(a_x + 175, y_pos), admin_pt], fill="#000000", width=2)
        if has_auth:
            draw_dashed_include(draw, (a_x + 175, y_pos), (a_auth_x - 85, y_pos), label="<<include>>")
            draw_ellipse_uc(draw, (a_auth_x, y_pos), "Authenticate User", w=170, h=38, font_size=12)
            draw.line([(a_auth_x + 85, y_pos), admin_pt], fill="#000000", width=2)

    out1 = r"d:\NEW healthy bite\diagrams\Healthy_Bite_Use_Case_BW.png"
    out2 = r"d:\NEW healthy bite\diagrams\use case diagaram\Healthy Bite Use Case Diagram.png"
    img.save(out1, "PNG", dpi=(300, 300))
    img.save(out2, "PNG", dpi=(300, 300))
    print(f"Generated Clean B&W Use Case Diagram: {out1} & {out2} ({w}x{h})")

if __name__ == "__main__":
    generate()
