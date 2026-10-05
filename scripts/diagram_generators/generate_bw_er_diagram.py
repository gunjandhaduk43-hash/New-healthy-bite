r"""
Generate Healthy Bite Clean Black & White E-R Diagram (Chen Notation)
Matches exact college visual format from d:\NEW healthy bite\old format diagram:
- Pure Black & White (No colors, no pastel fills)
- Classic typography: Serif Header + Pill Subtitle
- Standard Chen Notation: Rectangles for Entities, Diamonds for Relationships, Ovals for Attributes
- Underlined Primary Keys
- Corrected logic: strict 1:1 payment cardinality, circular FK (users.restaurant_id), snapshots, all 17 tables
Outputs:
- diagrams/Healthy_Bite_ER_Diagram_BW.png
- diagrams/ER diagram/ER diagram.png
"""

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

def draw_entity_box(draw, box, name):
    x1, y1, x2, y2 = box
    draw.rectangle([x1, y1, x2, y2], fill="#FFFFFF", outline="#000000", width=2)
    f = get_sans_font(13, bold=True)
    bbox = draw.textbbox((0, 0), name, font=f)
    tw = bbox[2] - bbox[0]
    th = bbox[3] - bbox[1]
    cx = (x1 + x2) // 2
    cy = (y1 + y2) // 2
    draw.text((cx - tw // 2, cy - th // 2 - bbox[1]), name, font=f, fill="#000000")

def draw_oval_attr(draw, center, name, is_pk=False, is_fk=False, w=100, h=26):
    cx, cy = center
    hw, hh = w // 2, h // 2
    draw.ellipse([cx - hw, cy - hh, cx + hw, cy + hh], fill="#FFFFFF", outline="#000000", width=1)
    f = get_sans_font(10, bold=is_pk)
    bbox = draw.textbbox((0, 0), name, font=f)
    tw = bbox[2] - bbox[0]
    th = bbox[3] - bbox[1]
    tx = cx - tw // 2
    ty = cy - th // 2 - bbox[1]
    draw.text((tx, ty), name, font=f, fill="#000000")
    if is_pk:
        draw.line([(tx, ty + th + 2), (tx + tw, ty + th + 2)], fill="#000000", width=1)

def draw_diamond_rel(draw, center, name, card1="1", card2="N", p1=None, p2=None, w=84, h=38):
    cx, cy = center
    hw, hh = w // 2, h // 2
    pts = [(cx, cy - hh), (cx + hw, cy), (cx, cy + hh), (cx - hw, cy)]
    draw.polygon(pts, fill="#FFFFFF", outline="#000000")
    draw.line(pts + [pts[0]], fill="#000000", width=2)
    
    f = get_sans_font(10, bold=True)
    bbox = draw.textbbox((0, 0), name, font=f)
    tw = bbox[2] - bbox[0]
    th = bbox[3] - bbox[1]
    draw.text((cx - tw // 2, cy - th // 2 - bbox[1]), name, font=f, fill="#000000")

    card_f = get_sans_font(12, bold=True)
    if p1:
        draw.line([center, p1], fill="#000000", width=1)
        mx = (center[0] + p1[0] * 2) // 3
        my = (center[1] + p1[1] * 2) // 3
        draw.text((mx + 4, my - 12), card1, font=card_f, fill="#000000")
    if p2:
        draw.line([center, p2], fill="#000000", width=1)
        mx = (center[0] + p2[0] * 2) // 3
        my = (center[1] + p2[1] * 2) // 3
        draw.text((mx + 4, my - 12), card2, font=card_f, fill="#000000")

def generate():
    w, h = 2100, 1200
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    # 1. Title at top
    f_title1 = get_serif_font(34)
    bbox1 = draw.textbbox((0, 0), "HEALTHY BITE", font=f_title1)
    draw.text((w // 2 - (bbox1[2] - bbox1[0]) // 2, 16), "HEALTHY BITE", font=f_title1, fill="#000000")

    f_title2 = get_serif_font(30)
    bbox2 = draw.textbbox((0, 0), "ENTITY-RELATIONSHIP (E-R) DIAGRAM", font=f_title2)
    draw.text((w // 2 - (bbox2[2] - bbox2[0]) // 2, 58), "ENTITY-RELATIONSHIP (E-R) DIAGRAM", font=f_title2, fill="#000000")

    pill_w = 440
    pill_h = 32
    pill_x1 = w // 2 - pill_w // 2
    pill_y1 = 100
    draw.rounded_rectangle([pill_x1, pill_y1, pill_x1 + pill_w, pill_y1 + pill_h], radius=16, fill="#FFFFFF", outline="#000000", width=2)
    f_sub = get_sans_font(15, bold=True)
    bbox_sub = draw.textbbox((0, 0), "Digital Restaurant & Food Ordering System", font=f_sub)
    draw.text((w // 2 - (bbox_sub[2] - bbox_sub[0]) // 2, pill_y1 + 6), "Digital Restaurant & Food Ordering System", font=f_sub, fill="#000000")

    # 2. Outer Boundary Box (Black, rounded)
    bx1, by1 = 50, 116
    bx2, by2 = 2050, 1160
    draw.rounded_rectangle([bx1, by1, bx2, by2], radius=20, fill=None, outline="#000000", width=2)
    # Redraw pill over top line
    draw.rounded_rectangle([pill_x1, pill_y1, pill_x1 + pill_w, pill_y1 + pill_h], radius=16, fill="#FFFFFF", outline="#000000", width=2)
    draw.text((w // 2 - (bbox_sub[2] - bbox_sub[0]) // 2, pill_y1 + 6), "Digital Restaurant & Food Ordering System", font=f_sub, fill="#000000")

    # 3. 17 Entities (x, y, w, h)
    entities = {
        # ROW 1: RBAC, Users, Restaurant, Branches, Tables, QR, Admin (Y ~ 170 to 220)
        "roles": (90, 180, 100, 40),
        "users": (330, 180, 110, 40),
        "restaurants": (600, 180, 120, 40),
        "branches": (880, 180, 110, 40),
        "restaurant_tables": (1160, 180, 140, 40),
        "qr_tokens": (1460, 180, 110, 40),
        "admin": (1840, 180, 100, 40),
        
        # ROW 2: Categories, Food Items, Customers, Reviews (Y ~ 440 to 490)
        "categories": (270, 450, 120, 40),
        "food_items": (600, 450, 120, 40),
        "customers": (1160, 450, 120, 40),
        "reviews": (1550, 450, 110, 40),
        
        # ROW 3: Variants, Customizations, Orders, Payments (Y ~ 720 to 770)
        "food_variants": (400, 730, 130, 40),
        "food_customizations": (720, 730, 160, 40),
        "orders": (1160, 730, 120, 40),
        "payments": (1550, 730, 110, 40),
        
        # ROW 4: Order Items & Customizations (Y ~ 990 to 1040)
        "order_items": (1160, 1000, 130, 40),
        "order_item_customizations": (720, 1000, 180, 40),
    }

    def ec(name):
        x, y, ew, eh = entities[name]
        return (x + ew // 2, y + eh // 2)

    for name, (x, y, ew, eh) in entities.items():
        draw_entity_box(draw, [x, y, x + ew, y + eh], name)

    # 4. Chen Relationships (Diamonds)
    rels = [
        # Row 1
        ("assigned", "roles", "users", "1", "N", (240, 200)),
        ("owns", "users", "restaurants", "1", "N", (520, 175)),
        ("employs", "restaurants", "users", "1", "N", (520, 225)), # Circular FK
        ("operates", "restaurants", "branches", "1", "N", (800, 200)),
        ("houses", "branches", "restaurant_tables", "1", "N", (1070, 175)),
        ("contains", "restaurants", "restaurant_tables", "1", "N", (950, 140)),
        ("paired_with", "restaurant_tables", "qr_tokens", "1", "1", (1360, 200)),
        ("issues", "restaurants", "qr_tokens", "1", "N", (1070, 225)),
        ("branch_qr", "branches", "qr_tokens", "1", "N", (1280, 140)),
        
        # Catalog & Food
        ("manages", "restaurants", "categories", "1", "N", (440, 320)),
        ("offers", "restaurants", "food_items", "1", "N", (600, 320)),
        ("classifies", "categories", "food_items", "1", "N", (490, 470)),
        ("has_variant", "food_items", "food_variants", "1", "N", (520, 590)),
        ("has_custom", "food_items", "food_customizations", "1", "N", (720, 590)),
        
        # Customers & Orders
        ("places", "customers", "orders", "1", "N", (1160, 590)),
        ("serves_at", "restaurant_tables", "orders", "1", "N", (1230, 450)),
        ("receives", "restaurants", "orders", "1", "N", (900, 450)),
        ("fulfills", "branches", "orders", "1", "N", (1010, 590)),
        
        # Payment: STRICT 1:1
        ("settled_by", "orders", "payments", "1", "1", (1410, 750)),
        
        # Reviews
        ("writes", "customers", "reviews", "1", "N", (1410, 470)),
        ("rates", "restaurants", "reviews", "1", "N", (1460, 320)),
        ("reviewed_in", "orders", "reviews", "1", "1", (1410, 590)),
        
        # Order items
        ("contains", "orders", "order_items", "1", "N", (1225, 870)),
        ("ordered_as", "food_items", "order_items", "1", "N", (900, 750)),
        ("has_addon", "order_items", "order_item_customizations", "1", "N", (960, 1020)),
        ("applied_in", "food_customizations", "order_item_customizations", "1", "N", (720, 870)),
    ]

    for rname, e1, e2, c1, c2, center in rels:
        draw_diamond_rel(draw, center, rname, card1=c1, card2=c2, p1=ec(e1), p2=ec(e2), w=84, h=36)

    # 5. Attributes (Pure Black & White Ovals)
    attrs = {
        "roles": [("id", True, False, (0, -30)), ("name", False, False, (0, 30))],
        "users": [("id", True, False, (-35, -30)), ("role_id", False, True, (35, -30)), ("restaurant_id", False, True, (-35, 30)), ("email", False, False, (35, 30))],
        "restaurants": [("id", True, False, (-40, -30)), ("owner_user_id", False, True, (40, -30)), ("name", False, False, (0, 30))],
        "branches": [("id", True, False, (-30, -30)), ("restaurant_id", False, True, (30, -30)), ("name", False, False, (0, 30))],
        "restaurant_tables": [("id", True, False, (-40, -30)), ("table_number", False, False, (40, -30)), ("branch_id", False, True, (0, 30))],
        "qr_tokens": [("id", True, False, (-30, -30)), ("table_id", False, True, (30, -30)), ("token", False, False, (0, 30))],
        "admin": [("id", True, False, (0, -30)), ("username", False, False, (0, 30))],
        
        "categories": [("id", True, False, (-35, -30)), ("name", False, False, (35, -30)), ("restaurant_id", False, True, (0, 30))],
        "food_items": [
            ("id", True, False, (-65, -30)), ("category_id", False, True, (0, -30)), ("base_price", False, False, (65, -30)),
            ("name", False, False, (-65, 30)), ("8_macros", False, False, (0, 30)), ("is_available", False, False, (65, 30))
        ],
        "customers": [("id", True, False, (-35, -30)), ("name", False, False, (35, -30)), ("mobile", False, False, (0, 30))],
        "reviews": [("id", True, False, (-40, -30)), ("rating", False, False, (40, -30)), ("order_id", False, True, (-40, 30)), ("comment", False, False, (40, 30))],
        
        "food_variants": [("id", True, False, (-40, -30)), ("food_item_id", False, True, (40, -30)), ("variant_name", False, False, (-40, 30)), ("price_adj", False, False, (40, 30))],
        "food_customizations": [
            ("id", True, False, (-50, -30)), ("group_name", False, False, (50, -30)),
            ("caffeine_adj", False, False, (-50, 30)), ("price_adj", False, False, (50, 30))
        ],
        "orders": [
            ("id", True, False, (-65, -30)), ("order_number", False, False, (0, -30)), ("customer_id", False, True, (65, -30)),
            ("total_amount", False, False, (-65, 30)), ("total_caffeine", False, False, (0, 30)), ("order_status", False, False, (65, 30))
        ],
        "payments": [("id", True, False, (-40, -30)), ("order_id", False, True, (40, -30)), ("payment_method", False, False, (-40, 30)), ("amount", False, False, (40, 30))],
        
        "order_items": [
            ("id", True, False, (-60, -30)), ("order_id", False, True, (0, -30)), ("food_item_id", False, True, (60, -30)),
            ("food_name_snapshot", False, False, (-60, 30)), ("base_price_snapshot", False, False, (60, 30))
        ],
        "order_item_customizations": [
            ("id", True, False, (-70, -30)), ("order_item_id", False, True, (0, -30)), ("custom_id", False, True, (70, -30)),
            ("custom_name_snapshot", False, False, (-70, 30)), ("price_adj_snapshot", False, False, (70, 30))
        ],
    }

    for ent, at_list in attrs.items():
        cx, cy = ec(ent)
        for aname, is_pk, is_fk, (dx, dy) in at_list:
            ac = (cx + dx, cy + dy)
            draw.line([(cx, cy), ac], fill="#000000", width=1)
            draw_oval_attr(draw, ac, aname, is_pk=is_pk, is_fk=is_fk, w=max(len(aname)*7 + 14, 70), h=24)

    out1 = r"d:\NEW healthy bite\diagrams\Healthy_Bite_ER_Diagram_BW.png"
    out2 = r"d:\NEW healthy bite\diagrams\ER diagram\ER diagram.png"
    img.save(out1, "PNG", dpi=(300, 300))
    img.save(out2, "PNG", dpi=(300, 300))
    print(f"Generated Clean B&W ER Diagram: {out1} & {out2} ({w}x{h})")

if __name__ == "__main__":
    generate()
