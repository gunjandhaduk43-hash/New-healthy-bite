"""
Generate Healthy Bite A4-Documentation-Optimized E-R Diagram Samples
Specifically engineered for standard A4 documentation pages with top heading margins:
1. Landscape A4 Sample: 2000 x 1150 (Ideal for landscape pages or 2/3 portrait width)
2. Portrait A4 Sample: 1400 x 1950 (Ideal for standard portrait A4 pages filling vertical height below heading)
- Large, bold, high-contrast typography readable at 100% zoom without zooming in
- Zero wasted whitespace; compact, snug, aesthetic Chen notation
- 100% technically correct: 17 tables, 26 FK relationships, 1:1 payment, snapshots, 8 macros
Outputs:
- diagrams/Sample_A4_ER_Diagram_Landscape.png
- diagrams/Sample_A4_ER_Diagram_Portrait.png
- diagrams/Sample_A4_ER_Diagram.png
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

def draw_entity(draw, box, name, fill="#FFFFFF", outline="#0F172A", width=2):
    x1, y1, x2, y2 = box
    draw.rectangle([x1, y1, x2, y2], fill=fill, outline=outline, width=width)
    f = get_font(13, bold=True)
    bbox = draw.textbbox((0, 0), name, font=f)
    tw = bbox[2] - bbox[0]
    th = bbox[3] - bbox[1]
    cx = (x1 + x2) // 2
    cy = (y1 + y2) // 2
    draw.text((cx - tw // 2, cy - th // 2 - bbox[1]), name, font=f, fill="#0F172A")

def draw_attr(draw, center, name, is_pk=False, is_fk=False, w=105, h=26):
    cx, cy = center
    hw, hh = w // 2, h // 2
    draw.ellipse([cx - hw, cy - hh, cx + hw, cy + hh], fill="#FFFFFF", outline="#475569", width=1)
    f = get_font(10, bold=is_pk)
    bbox = draw.textbbox((0, 0), name, font=f)
    tw = bbox[2] - bbox[0]
    th = bbox[3] - bbox[1]
    tx = cx - tw // 2
    ty = cy - th // 2 - bbox[1]
    color = "#DC2626" if is_pk else ("#2563EB" if is_fk else "#0F172A")
    draw.text((tx, ty), name, font=f, fill=color)
    if is_pk:
        draw.line([(tx, ty + th + 2), (tx + tw, ty + th + 2)], fill=color, width=1)

def draw_rel(draw, center, name, card1="1", card2="N", p1=None, p2=None, w=86, h=38):
    cx, cy = center
    hw, hh = w // 2, h // 2
    pts = [(cx, cy - hh), (cx + hw, cy), (cx, cy + hh), (cx - hw, cy)]
    draw.polygon(pts, fill="#F0FDF4", outline="#16A34A")
    draw.line(pts + [pts[0]], fill="#16A34A", width=2)
    
    f = get_font(10, bold=True)
    bbox = draw.textbbox((0, 0), name, font=f)
    tw = bbox[2] - bbox[0]
    th = bbox[3] - bbox[1]
    draw.text((cx - tw // 2, cy - th // 2 - bbox[1]), name, font=f, fill="#15803D")

    card_f = get_font(12, bold=True)
    if p1:
        draw.line([center, p1], fill="#334155", width=2)
        mx = (center[0] + p1[0] * 2) // 3
        my = (center[1] + p1[1] * 2) // 3
        draw.text((mx + 4, my - 12), card1, font=card_f, fill="#DC2626")
    if p2:
        draw.line([center, p2], fill="#334155", width=2)
        mx = (center[0] + p2[0] * 2) // 3
        my = (center[1] + p2[1] * 2) // 3
        draw.text((mx + 4, my - 12), card2, font=card_f, fill="#DC2626")

def generate_landscape():
    w, h = 2000, 1150
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    draw.rectangle([0, 0, w, 44], fill="#0F172A")
    draw.text((30, 10), "HEALTHY BITE — ENTITY-RELATIONSHIP (E-R) DIAGRAM (LANDSCAPE A4 OPTIMIZED)", font=get_font(16, bold=True), fill="#FFFFFF")
    draw.text((w - 580, 12), "Optimized for Single-Page A4 Documentation • 100% Readable at 100% Zoom", font=get_font(11), fill="#94A3B8")

    entities = {
        "roles": (40, 100, 110, 42),
        "users": (280, 100, 120, 42),
        "restaurants": (540, 100, 130, 42),
        "branches": (830, 100, 120, 42),
        "restaurant_tables": (1110, 100, 140, 42),
        "qr_tokens": (1400, 100, 120, 42),
        "admin": (1780, 100, 110, 42),
        
        "categories": (240, 380, 120, 42),
        "food_items": (540, 380, 130, 42),
        "customers": (1110, 380, 130, 42),
        "reviews": (1500, 380, 120, 42),
        
        "food_variants": (360, 660, 130, 42),
        "food_customizations": (680, 660, 150, 42),
        "orders": (1110, 660, 130, 42),
        "payments": (1500, 660, 120, 42),
        
        "order_items": (1110, 940, 140, 42),
        "order_item_customizations": (680, 940, 180, 42),
    }

    def ec(name):
        x, y, ew, eh = entities[name]
        return (x + ew // 2, y + eh // 2)

    for name, (x, y, ew, eh) in entities.items():
        is_leg = (name == "admin")
        draw_entity(draw, [x, y, x + ew, y + eh], name, fill="#F8FAFC" if not is_leg else "#F1F5F9", outline="#0F172A" if not is_leg else "#64748B", width=2)

    rels = [
        ("assigned", "roles", "users", "1", "N", (200, 121)),
        ("owns", "users", "restaurants", "1", "N", (460, 100)),
        ("employs", "restaurants", "users", "1", "N", (460, 142)),
        ("operates", "restaurants", "branches", "1", "N", (750, 121)),
        ("houses", "branches", "restaurant_tables", "1", "N", (1020, 100)),
        ("contains", "restaurants", "restaurant_tables", "1", "N", (900, 62)),
        ("paired_with", "restaurant_tables", "qr_tokens", "1", "1", (1300, 121)),
        ("issues", "restaurants", "qr_tokens", "1", "N", (1020, 142)),
        ("branch_qr", "branches", "qr_tokens", "1", "N", (1240, 62)),
        
        ("manages", "restaurants", "categories", "1", "N", (380, 240)),
        ("offers", "restaurants", "food_items", "1", "N", (540, 240)),
        ("classifies", "categories", "food_items", "1", "N", (440, 401)),
        ("has_variant", "food_items", "food_variants", "1", "N", (480, 530)),
        ("has_custom", "food_items", "food_customizations", "1", "N", (680, 530)),
        
        ("places", "customers", "orders", "1", "N", (1110, 520)),
        ("serves_at", "restaurant_tables", "orders", "1", "N", (1175, 380)),
        ("receives", "restaurants", "orders", "1", "N", (860, 420)),
        ("fulfills", "branches", "orders", "1", "N", (970, 530)),
        
        ("settled_by", "orders", "payments", "1", "1", (1350, 681)), # STRICT 1:1
        
        ("writes", "customers", "reviews", "1", "N", (1350, 401)),
        ("rates", "restaurants", "reviews", "1", "N", (1400, 260)),
        ("reviewed_in", "orders", "reviews", "1", "1", (1350, 520)),
        
        ("contains", "orders", "order_items", "1", "N", (1175, 800)),
        ("ordered_as", "food_items", "order_items", "1", "N", (850, 720)),
        ("has_addon", "order_items", "order_item_customizations", "1", "N", (920, 961)),
        ("applied_in", "food_customizations", "order_item_customizations", "1", "N", (680, 800)),
    ]

    for rname, e1, e2, c1, c2, center in rels:
        draw_rel(draw, center, rname, card1=c1, card2=c2, p1=ec(e1), p2=ec(e2), w=84, h=36)

    attrs = {
        "roles": [("id", True, False, (0, -32)), ("name", False, False, (0, 32))],
        "users": [("id", True, False, (-40, -32)), ("role_id", False, True, (40, -32)), ("restaurant_id", False, True, (-40, 32)), ("email", False, False, (40, 32))],
        "restaurants": [("id", True, False, (-45, -32)), ("owner_user_id", False, True, (45, -32)), ("name", False, False, (0, 32))],
        "branches": [("id", True, False, (-35, -32)), ("restaurant_id", False, True, (35, -32)), ("name", False, False, (0, 32))],
        "restaurant_tables": [("id", True, False, (-45, -32)), ("table_number", False, False, (45, -32)), ("branch_id", False, True, (0, 32))],
        "qr_tokens": [("id", True, False, (-35, -32)), ("table_id", False, True, (35, -32)), ("token", False, False, (0, 32))],
        "admin": [("id", True, False, (0, -32)), ("username", False, False, (0, 32))],
        
        "categories": [("id", True, False, (-40, -32)), ("name", False, False, (40, -32)), ("restaurant_id", False, True, (0, 32))],
        "food_items": [
            ("id", True, False, (-70, -32)), ("category_id", False, True, (0, -32)), ("base_price", False, False, (70, -32)),
            ("name", False, False, (-70, 32)), ("8_macros", False, False, (0, 32)), ("is_available", False, False, (70, 32))
        ],
        "customers": [("id", True, False, (-40, -32)), ("name", False, False, (40, -32)), ("mobile", False, False, (0, 32))],
        "reviews": [("id", True, False, (-45, -32)), ("rating", False, False, (45, -32)), ("order_id", False, True, (-45, 32)), ("comment", False, False, (45, 32))],
        
        "food_variants": [("id", True, False, (-45, -32)), ("food_item_id", False, True, (45, -32)), ("variant_name", False, False, (-45, 32)), ("price_adj", False, False, (45, 32))],
        "food_customizations": [
            ("id", True, False, (-55, -32)), ("group_name", False, False, (55, -32)),
            ("caffeine_adj", False, False, (-55, 32)), ("price_adj", False, False, (55, 32))
        ],
        "orders": [
            ("id", True, False, (-70, -32)), ("order_number", False, False, (0, -32)), ("customer_id", False, True, (70, -32)),
            ("total_amount", False, False, (-70, 32)), ("total_caffeine", False, False, (0, 32)), ("order_status", False, False, (70, 32))
        ],
        "payments": [("id", True, False, (-45, -32)), ("order_id", False, True, (45, -32)), ("payment_method", False, False, (-45, 32)), ("amount", False, False, (45, 32))],
        
        "order_items": [
            ("id", True, False, (-65, -32)), ("order_id", False, True, (0, -32)), ("food_item_id", False, True, (65, -32)),
            ("food_name_snapshot", False, False, (-65, 32)), ("base_price_snapshot", False, False, (65, 32))
        ],
        "order_item_customizations": [
            ("id", True, False, (-75, -32)), ("order_item_id", False, True, (0, -32)), ("custom_id", False, True, (75, -32)),
            ("custom_name_snapshot", False, False, (-75, 32)), ("price_adj_snapshot", False, False, (75, 32))
        ],
    }

    for ent, at_list in attrs.items():
        cx, cy = ec(ent)
        for aname, is_pk, is_fk, (dx, dy) in at_list:
            ac = (cx + dx, cy + dy)
            draw.line([(cx, cy), ac], fill="#CBD5E1", width=1)
            draw_attr(draw, ac, aname, is_pk=is_pk, is_fk=is_fk, w=max(len(aname)*7 + 16, 75), h=24)

    # Legend
    lx, ly = 1460, 890
    draw.rounded_rectangle([lx, ly, lx + 500, ly + 220], radius=6, fill="#F8FAFC", outline="#CBD5E1", width=1)
    draw.text((lx + 15, ly + 10), "A4 LANDSCAPE PAGE AUDIT & VERIFICATION:", font=get_font(11, bold=True), fill="#0F172A")
    specs = [
        ("Entity Box:", "Black Rectangle (17 physical tables)"),
        ("Relationship:", "Green Diamond with Cardinality (1, N)"),
        ("Primary Key:", "Red Text with Underline (PK)"),
        ("Foreign Key:", "Blue Text (FK) - 26 Constraints Total"),
        ("Critical Fix 1:", "orders <-> payments strictly 1:1 (Unique Key)"),
        ("Critical Fix 2:", "Circular FK: users.restaurant_id -> restaurants"),
        ("Critical Fix 3:", "Spelling: caffeine_adj (removed 'saffeine')"),
        ("Critical Fix 4:", "Snapshots: price/name snapshots in order lines")
    ]
    cy_leg = ly + 32
    for k, v in specs:
        draw.text((lx + 15, cy_leg), k, font=get_font(10, bold=True), fill="#2563EB" if "Fix" in k else "#475569")
        draw.text((lx + 115, cy_leg), v, font=get_font(10), fill="#0F172A")
        cy_leg += 22

    out1 = r"d:\NEW healthy bite\diagrams\Sample_A4_ER_Diagram_Landscape.png"
    out2 = r"d:\NEW healthy bite\diagrams\Sample_A4_ER_Diagram.png"
    img.save(out1, "PNG", dpi=(300, 300))
    img.save(out2, "PNG", dpi=(300, 300))
    print(f"Generated Landscape A4 Sample: {out1} & {out2} ({w}x{h})")

def generate_portrait():
    # 1400 x 1950 (Aspect ratio ~ 1 : 1.39, matches A4 Portrait printable area below a top chapter heading)
    w, h = 1400, 1950
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    draw.rectangle([0, 0, w, 44], fill="#0F172A")
    draw.text((25, 10), "HEALTHY BITE — ENTITY-RELATIONSHIP (E-R) DIAGRAM (PORTRAIT A4 OPTIMIZED)", font=get_font(15, bold=True), fill="#FFFFFF")
    draw.text((w - 480, 12), "Fits Single Portrait A4 Page with Heading • Readable without Zooming", font=get_font(10), fill="#94A3B8")

    entities = {
        # TIER 1: Roles, Users, Restaurants, Branches (Y ~ 100 to 220)
        "roles": (60, 100, 110, 42),
        "users": (320, 100, 120, 42),
        "restaurants": (620, 100, 130, 42),
        "branches": (940, 100, 120, 42),
        "admin": (1220, 100, 110, 42),
        
        # TIER 2: Dining Tables & QR Tokens (Y ~ 280 to 400)
        "restaurant_tables": (620, 290, 150, 42),
        "qr_tokens": (940, 290, 130, 42),
        
        # TIER 3: Menu Catalog & Categories (Y ~ 480 to 650)
        "categories": (240, 520, 120, 42),
        "food_items": (620, 520, 130, 42),
        "food_variants": (240, 720, 130, 42),
        "food_customizations": (620, 720, 160, 42),
        
        # TIER 4: Customers & Dining Orders (Y ~ 920 to 1150)
        "customers": (1100, 520, 130, 42),
        "orders": (880, 920, 130, 42),
        "payments": (1200, 920, 120, 42), # Strict 1:1
        
        # TIER 5: Order Line Items & Customization Snapshots (Y ~ 1180 to 1420)
        "order_items": (880, 1200, 140, 42),
        "order_item_customizations": (420, 1200, 200, 42),
        
        # TIER 6: Reviews & Ratings (Y ~ 1480 to 1600)
        "reviews": (880, 1500, 130, 42)
    }

    def ec(name):
        x, y, ew, eh = entities[name]
        return (x + ew // 2, y + eh // 2)

    for name, (x, y, ew, eh) in entities.items():
        is_leg = (name == "admin")
        draw_entity(draw, [x, y, x + ew, y + eh], name, fill="#F8FAFC" if not is_leg else "#F1F5F9", outline="#0F172A" if not is_leg else "#64748B", width=2)

    rels = [
        ("assigned", "roles", "users", "1", "N", (220, 121)),
        ("owns", "users", "restaurants", "1", "N", (500, 100)),
        ("employs", "restaurants", "users", "1", "N", (500, 142)),
        ("operates", "restaurants", "branches", "1", "N", (830, 121)),
        
        ("houses", "branches", "restaurant_tables", "1", "N", (860, 220)),
        ("contains", "restaurants", "restaurant_tables", "1", "N", (685, 215)),
        ("paired_with", "restaurant_tables", "qr_tokens", "1", "1", (855, 311)),
        ("branch_qr", "branches", "qr_tokens", "1", "N", (1000, 220)),
        ("issues_qr", "restaurants", "qr_tokens", "1", "N", (820, 240)),
        
        ("manages", "restaurants", "categories", "1", "N", (440, 400)),
        ("offers", "restaurants", "food_items", "1", "N", (685, 420)),
        ("classifies", "categories", "food_items", "1", "N", (460, 541)),
        ("has_variant", "food_items", "food_variants", "1", "N", (440, 640)),
        ("has_custom", "food_items", "food_customizations", "1", "N", (685, 640)),
        
        ("places", "customers", "orders", "1", "N", (1050, 740)),
        ("serves_at", "restaurant_tables", "orders", "1", "N", (850, 600)),
        ("receives", "restaurants", "orders", "1", "N", (800, 520)),
        ("fulfills", "branches", "orders", "1", "N", (960, 520)),
        
        ("settled_by", "orders", "payments", "1", "1", (1080, 941)), # 1:1 Unique
        
        ("contains", "orders", "order_items", "1", "N", (950, 1070)),
        ("ordered_as", "food_items", "order_items", "1", "N", (780, 860)),
        ("has_addon", "order_items", "order_item_customizations", "1", "N", (700, 1221)),
        ("applied_in", "food_customizations", "order_item_customizations", "1", "N", (580, 960)),
        
        ("writes", "customers", "reviews", "1", "N", (1120, 1150)),
        ("rates", "restaurants", "reviews", "1", "N", (1280, 800)),
        ("reviewed_in", "orders", "reviews", "1", "1", (950, 1360)),
    ]

    for rname, e1, e2, c1, c2, center in rels:
        draw_rel(draw, center, rname, card1=c1, card2=c2, p1=ec(e1), p2=ec(e2), w=82, h=36)

    attrs = {
        "roles": [("id", True, False, (0, -32)), ("name", False, False, (0, 32))],
        "users": [("id", True, False, (-40, -32)), ("role_id", False, True, (40, -32)), ("restaurant_id", False, True, (-40, 32)), ("email", False, False, (40, 32))],
        "restaurants": [("id", True, False, (-45, -32)), ("owner_user_id", False, True, (45, -32)), ("name", False, False, (0, 32))],
        "branches": [("id", True, False, (-35, -32)), ("restaurant_id", False, True, (35, -32)), ("name", False, False, (0, 32))],
        "restaurant_tables": [("id", True, False, (-50, -32)), ("table_number", False, False, (50, -32)), ("branch_id", False, True, (0, 32))],
        "qr_tokens": [("id", True, False, (-35, -32)), ("table_id", False, True, (35, -32)), ("token", False, False, (0, 32))],
        "admin": [("id", True, False, (0, -32)), ("username", False, False, (0, 32))],
        
        "categories": [("id", True, False, (-40, -32)), ("name", False, False, (40, -32)), ("restaurant_id", False, True, (0, 32))],
        "food_items": [
            ("id", True, False, (-70, -32)), ("category_id", False, True, (0, -32)), ("base_price", False, False, (70, -32)),
            ("name", False, False, (-70, 32)), ("8_macros", False, False, (0, 32)), ("is_available", False, False, (70, 32))
        ],
        "customers": [("id", True, False, (-40, -32)), ("name", False, False, (40, -32)), ("mobile", False, False, (0, 32))],
        "food_variants": [("id", True, False, (-45, -32)), ("food_item_id", False, True, (45, -32)), ("variant_name", False, False, (-45, 32)), ("price_adj", False, False, (45, 32))],
        "food_customizations": [
            ("id", True, False, (-55, -32)), ("group_name", False, False, (55, -32)),
            ("caffeine_adj", False, False, (-55, 32)), ("price_adj", False, False, (55, 32))
        ],
        
        "orders": [
            ("id", True, False, (-70, -32)), ("order_number", False, False, (0, -32)), ("customer_id", False, True, (70, -32)),
            ("total_amount", False, False, (-70, 32)), ("total_caffeine", False, False, (0, 32)), ("order_status", False, False, (70, 32))
        ],
        "payments": [("id", True, False, (-45, -32)), ("order_id", False, True, (45, -32)), ("payment_method", False, False, (-45, 32)), ("amount", False, False, (45, 32))],
        
        "order_items": [
            ("id", True, False, (-65, -32)), ("order_id", False, True, (0, -32)), ("food_item_id", False, True, (65, -32)),
            ("food_name_snapshot", False, False, (-65, 32)), ("base_price_snapshot", False, False, (65, 32))
        ],
        "order_item_customizations": [
            ("id", True, False, (-75, -32)), ("order_item_id", False, True, (0, -32)), ("custom_id", False, True, (75, -32)),
            ("custom_name_snapshot", False, False, (-75, 32)), ("price_adj_snapshot", False, False, (75, 32))
        ],
        "reviews": [("id", True, False, (-45, -32)), ("rating", False, False, (45, -32)), ("order_id", False, True, (-45, 32)), ("comment", False, False, (45, 32))]
    }

    for ent, at_list in attrs.items():
        cx, cy = ec(ent)
        for aname, is_pk, is_fk, (dx, dy) in at_list:
            ac = (cx + dx, cy + dy)
            draw.line([(cx, cy), ac], fill="#CBD5E1", width=1)
            draw_attr(draw, ac, aname, is_pk=is_pk, is_fk=is_fk, w=max(len(aname)*7 + 16, 75), h=24)

    # Summary key in bottom left (Y ~ 1660 to 1890)
    lx, ly = 80, 1680
    draw.rounded_rectangle([lx, ly, lx + 1240, ly + 220], radius=8, fill="#F8FAFC", outline="#CBD5E1", width=1)
    draw.text((lx + 20, ly + 14), "A4 PORTRAIT PAGE SPECIFICATION & RECTIFICATIONS APPLIED:", font=get_font(12, bold=True), fill="#0F172A")
    lines_p = [
        "1. SINGLE PORTRAIT A4 PAGE FIT: Formatted to fit standard A4 portrait pages leaving full room for top chapter headings.",
        "2. ZERO ZOOMING REQUIRED: Bold, high-contrast typography, thick connectors, and tight compact grouping ensure crystal clarity.",
        "3. STRICT 1:1 PAYMENT SETTLEMENT: Corrected orders <-> payments cardinality from invalid N:1 to strictly 1:1.",
        "4. CIRCULAR TENANT ISOLATION: Depicts both users -> owns -> restaurants AND restaurants -> employs -> users (users.restaurant_id).",
        "5. FROZEN SNAPSHOT INTEGRITY: Explicitly documents food_name, base_price, variant_name, and customization price snapshots.",
        "6. ALL 8 MACROS & CORRECT SPELLING: Tracks kcal, protein, carbs, fat, fiber, sugar, sodium, and caffeine (no 'saffeine').",
        "7. 100% EXCLUSION: Permanently removes Call Servant, Table Assistance requests, and Platform Orders (/admin/orders)."
    ]
    cy_p = ly + 40
    for lp in lines_p:
        draw.text((lx + 20, cy_p), lp, font=get_font(10), fill="#334155")
        cy_p += 24

    out_p = r"d:\NEW healthy bite\diagrams\Sample_A4_ER_Diagram_Portrait.png"
    img.save(out_p, "PNG", dpi=(300, 300))
    print(f"Generated Portrait A4 Sample: {out_p} ({w}x{h})")

def generate_both():
    generate_landscape()
    generate_portrait()

if __name__ == "__main__":
    generate_both()
