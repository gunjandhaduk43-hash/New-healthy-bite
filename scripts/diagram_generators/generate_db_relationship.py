"""
Generate Healthy Bite Database Relationship Diagram (Relational Schema Diagram)
Visualizes all 17 physical tables, their columns, data types, primary keys,
and orthogonal connector lines for all 26 foreign keys with exact cardinalities.
Outputs:
- diagrams/03_Database_Relationship.png
- diagrams/ER diagram/Database_Relationship_Diagram.png
"""

import json
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

def generate():
    json_path = r"d:\NEW healthy bite\storage\db_extraction.json"
    with open(json_path, "r", encoding="utf-8") as f:
        schema_data = json.load(f)

    canvas_w = 4400
    canvas_h = 3000
    img = Image.new("RGB", (canvas_w, canvas_h), "#F8FAFC")
    draw = ImageDraw.Draw(img)

    # Title Banner
    draw.rectangle([0, 0, canvas_w, 100], fill="#0F172A")
    draw.text((50, 20), "HEALTHY BITE — RELATIONAL DATABASE SCHEMA & RELATIONSHIP DIAGRAM", font=get_font(26, bold=True), fill="#FFFFFF")
    draw.text((50, 62), "Physical MySQL Schema • 17 Tables • 208 Columns • 26 Referential Integrity Constraints • Strict 1:1 Payment Settlement", font=get_font(14, bold=False), fill="#94A3B8")

    # Table layout coordinates: (x, y, w)
    # Height will be computed dynamically from number of columns
    card_w = 340
    line_h = 20
    header_h = 34

    table_positions = {
        # Administrative & Auth (Col 1: x = 60)
        "roles": (60, 140),
        "users": (60, 420),
        "admin": (60, 880),
        
        # Restaurant & Physical Hierarchy (Col 2: x = 500)
        "restaurants": (500, 140),
        "branches": (500, 680),
        "restaurant_tables": (500, 1100),
        "qr_tokens": (500, 1500),
        
        # Menu Catalog System (Col 3: x = 950)
        "categories": (950, 140),
        "food_items": (950, 560),
        "food_variants": (950, 1380),
        "food_customizations": (950, 1960),
        
        # Customer & Dining Orders (Col 4: x = 1450)
        "customers": (1450, 140),
        "orders": (1450, 480),
        "order_items": (1450, 1340),
        "order_item_customizations": (1450, 2060),
        
        # Payment & Feedback (Col 5: x = 1950)
        "payments": (1950, 480),
        "reviews": (1950, 960),
    }

    # Store column coordinates for foreign key line routing: (table, col_name) -> (x, y)
    col_points_right = {}
    col_points_left = {}
    table_boxes = {}

    for tname, (x, y) in table_positions.items():
        tinfo = schema_data[tname]
        cols = tinfo["columns"]
        card_h = header_h + len(cols) * line_h + 6
        table_boxes[tname] = (x, y, x + card_w, y + card_h)

        # Card shadow & background
        draw.rounded_rectangle([x, y, x + card_w, y + card_h], radius=6, fill="#FFFFFF", outline="#CBD5E1", width=2)
        
        # Header
        hbg = "#1E293B" if tname != "admin" else "#64748B"
        draw.rounded_rectangle([x, y, x + card_w, y + header_h], radius=6, fill=hbg)
        draw.rectangle([x, y + header_h - 6, x + card_w, y + header_h], fill=hbg)
        draw.text((x + 10, y + 7), tname, font=get_font(14, bold=True), fill="#FFFFFF")
        draw.text((x + card_w - 70, y + 10), f"({len(cols)} cols)", font=get_font(11), fill="#94A3B8")

        # Render Columns
        cy = y + header_h
        for idx, c in enumerate(cols):
            cname = c["COLUMN_NAME"]
            ctype = c["DATA_TYPE"]
            ckey = c["COLUMN_KEY"]
            cnull = c["IS_NULLABLE"]

            bg = "#FFFFFF" if idx % 2 == 0 else "#F8FAFC"
            draw.rectangle([x + 2, cy, x + card_w - 2, cy + line_h], fill=bg)

            # Key badge
            kcolor = "#DC2626" if ckey == "PRI" else ("#D97706" if ckey == "UNI" else ("#2563EB" if ckey == "MUL" else "#64748B"))
            ktext = "PK" if ckey == "PRI" else ("UQ" if ckey == "UNI" else ("FK" if ckey == "MUL" else "  "))
            draw.text((x + 8, cy + 3), ktext, font=get_font(9, bold=True), fill=kcolor)

            # Column Name
            draw.text((x + 32, cy + 3), cname[:22], font=get_font(10, bold=(ckey == "PRI"), mono=True), fill="#0F172A")

            # Data Type
            draw.text((x + card_w - 110, cy + 3), ctype[:14], font=get_font(9, mono=True), fill="#64748B")

            col_points_right[(tname, cname)] = (x + card_w, cy + line_h // 2)
            col_points_left[(tname, cname)] = (x, cy + line_h // 2)

            cy += line_h

    # Draw all 26 Foreign Key Connections with clean orthogonal lines
    fks = [
        # (child_tbl, child_col, parent_tbl, parent_col, color, card)
        ("branches", "restaurant_id", "restaurants", "id", "#0284C7", "N:1"),
        ("categories", "restaurant_id", "restaurants", "id", "#0284C7", "N:1"),
        ("food_items", "restaurant_id", "restaurants", "id", "#0284C7", "N:1"),
        ("food_items", "category_id", "categories", "id", "#059669", "N:1"),
        ("food_variants", "food_item_id", "food_items", "id", "#059669", "N:1"),
        ("food_customizations", "food_item_id", "food_items", "id", "#059669", "N:1"),
        ("restaurant_tables", "restaurant_id", "restaurants", "id", "#0284C7", "N:1"),
        ("restaurant_tables", "branch_id", "branches", "id", "#0284C7", "N:1"),
        ("qr_tokens", "restaurant_id", "restaurants", "id", "#0284C7", "N:1"),
        ("qr_tokens", "branch_id", "branches", "id", "#0284C7", "N:1"),
        ("qr_tokens", "table_id", "restaurant_tables", "id", "#7C3AED", "1:1"),
        ("users", "role_id", "roles", "id", "#EA580C", "N:1"),
        ("users", "restaurant_id", "restaurants", "id", "#EA580C", "N:1"), # Circular FK!
        ("restaurants", "owner_user_id", "users", "id", "#EA580C", "N:1"),
        ("orders", "restaurant_id", "restaurants", "id", "#0284C7", "N:1"),
        ("orders", "branch_id", "branches", "id", "#0284C7", "N:1"),
        ("orders", "table_id", "restaurant_tables", "id", "#7C3AED", "N:1"),
        ("orders", "customer_id", "customers", "id", "#2563EB", "N:1"),
        ("order_items", "order_id", "orders", "id", "#2563EB", "N:1"),
        ("order_items", "food_item_id", "food_items", "id", "#059669", "N:1"),
        ("order_item_customizations", "order_item_id", "order_items", "id", "#2563EB", "N:1"),
        ("order_item_customizations", "customization_id", "food_customizations", "id", "#059669", "N:1"),
        ("payments", "order_id", "orders", "id", "#DC2626", "1:1"), # STRICTLY 1:1!
        ("reviews", "restaurant_id", "restaurants", "id", "#0284C7", "N:1"),
        ("reviews", "order_id", "orders", "id", "#2563EB", "1:1"),
        ("reviews", "customer_id", "customers", "id", "#2563EB", "N:1"),
    ]

    for c_tbl, c_col, p_tbl, p_col, color, card in fks:
        p_pt = col_points_right.get((p_tbl, p_col))
        c_pt = col_points_left.get((c_tbl, c_col))
        if not p_pt or not c_pt:
            continue
        
        # If parent is to the right of child, switch points
        if p_pt[0] > c_pt[0]:
            p_pt = col_points_left.get((p_tbl, p_col))
            c_pt = col_points_right.get((c_tbl, c_col))

        # Orthogonal line routing
        mid_x = (p_pt[0] + c_pt[0]) // 2
        draw.line([p_pt, (mid_x, p_pt[1])], fill=color, width=2)
        draw.line([(mid_x, p_pt[1]), (mid_x, c_pt[1])], fill=color, width=2)
        draw.line([(mid_x, c_pt[1]), c_pt], fill=color, width=2)

        # Draw terminal circles
        draw.ellipse([p_pt[0]-3, p_pt[1]-3, p_pt[0]+3, p_pt[1]+3], fill=color)
        draw.ellipse([c_pt[0]-3, c_pt[1]-3, c_pt[0]+3, c_pt[1]+3], fill=color)

    # Legend / Key Block on right side
    leg_x = 2450
    leg_y = 140
    draw.rounded_rectangle([leg_x, leg_y, leg_x + 900, leg_y + 800], radius=8, fill="#FFFFFF", outline="#CBD5E1", width=2)
    draw.text((leg_x + 30, leg_y + 24), "RELATIONAL SCHEMA ARCHITECTURE & CONSTRAINTS", font=get_font(18, bold=True), fill="#0F172A")
    draw.line([(leg_x + 30, leg_y + 60), (leg_x + 870, leg_y + 60)], fill="#E2E8F0", width=1)

    legend_sections = [
        ("REFERENTIAL INTEGRITY (26 FOREIGN KEYS):", [
            ("Blue lines (#0284C7):", "Multi-tenant Restaurant & Branch scoping constraints"),
            ("Green lines (#059669):", "Food Catalog, Variants & Customization associations"),
            ("Orange lines (#EA580C):", "Role-Based Access & Circular Tenant Isolation (users <-> restaurants)"),
            ("Navy lines (#2563EB):", "Customer Order, Line-Item & Review bindings"),
            ("Red line (#DC2626):", "Payment Settlement (Strict 1:1 via payments.order_id UNIQUE constraint)"),
            ("Purple lines (#7C3AED):", "Physical Dining Table & Dynamic QR Token bindings")
        ]),
        ("DATA INTEGRITY & SNAPSHOT AUDIT:", [
            ("order_items:", "Stores frozen food_name, base_price, variant_name, and variant_price snapshots"),
            ("order_item_customizations:", "Stores frozen customization_name and price_adjustment snapshots"),
            ("Nutritional Precision:", "8 macros calculated & snapshotted: kcal, protein, carbs, fat, fiber, sugar, sodium, caffeine"),
            ("payments.order_id:", "UNIQUE KEY constraint strictly enforces exactly one payment record per order"),
            ("admin table:", "Legacy isolated table with 0 foreign keys (not used by active auth)")
        ])
    ]

    cy = leg_y + 80
    for stitle, sitems in legend_sections:
        draw.text((leg_x + 30, cy), stitle, font=get_font(13, bold=True), fill="#1E293B")
        cy += 28
        for k, v in sitems:
            draw.text((leg_x + 50, cy), k, font=get_font(12, bold=True), fill="#0284C7" if "#" in k else "#475569")
            draw.text((leg_x + 240, cy), v, font=get_font(12), fill="#334155")
            cy += 24
        cy += 16

    out1 = r"d:\NEW healthy bite\diagrams\03_Database_Relationship.png"
    out2 = r"d:\NEW healthy bite\diagrams\ER diagram\Database_Relationship_Diagram.png"
    img.save(out1, "PNG", dpi=(300, 300))
    img.save(out2, "PNG", dpi=(300, 300))
    print(f"Generated Database Relationship Diagram: {out1} & {out2} ({canvas_w}x{canvas_h})")

if __name__ == "__main__":
    generate()
