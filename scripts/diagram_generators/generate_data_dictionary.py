"""
Generate Healthy Bite Comprehensive Data Dictionary Image
Covers all 17 physical tables in healthy_bite database.
Outputs: diagrams/01_Data_Dictionary.png
"""

import os
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

    # Order of tables for logical presentation
    table_order = [
        "roles", "users", "restaurants", "branches", "restaurant_tables", "qr_tokens",
        "categories", "food_items", "food_variants", "food_customizations",
        "customers", "orders", "order_items", "order_item_customizations",
        "payments", "reviews", "admin"
    ]

    table_descriptions = {
        "roles": "RBAC user roles (super_admin, restaurant_owner, restaurant_manager, kitchen_staff)",
        "users": "Platform & tenant accounts with password hashes and restaurant scoping",
        "restaurants": "Multi-tenant restaurant business entities, settings, and ownership",
        "branches": "Physical restaurant outlets / branch locations",
        "restaurant_tables": "Physical dining tables equipped with QR codes",
        "qr_tokens": "Cryptographically secure table-dining session tokens with expiry",
        "categories": "Food catalog categories (Appetizers, Mains, Bowls, Beverages)",
        "food_items": "Master food dishes with 8 base nutritional macros & pricing",
        "food_variants": "Portion size variants with differential pricing & macro scaling",
        "food_customizations": "Add-ons, ingredients & dietary choices with macro adjustments",
        "customers": "Dining customer profiles captured during digital checkout",
        "orders": "Dining table orders with 5-stage status lifecycle & cumulative macros",
        "order_items": "Immutable line-item snapshots preserving price & nutrition at order time",
        "order_item_customizations": "Snapshot records of selected customizations & applied macro deltas",
        "payments": "Simulated payment settlements (Cash, UPI, Card) linked 1:1 with orders",
        "reviews": "Customer ratings, comments, and persisted owner replies per order",
        "admin": "Legacy isolated admin table (retained for backward compatibility)"
    }

    # We will lay out tables in 2 major vertical columns to keep the image compact and readable
    canvas_w = 3400
    # Calculate required height
    col_w = 1620
    margin = 50
    gap = 60
    
    # We will compute height dynamically
    line_h = 24
    header_h = 32
    tbl_title_h = 42

    col1_tables = table_order[:9]
    col2_tables = table_order[9:]

    def get_tbl_height(tname):
        cols = schema_data[tname]["columns"]
        return tbl_title_h + header_h + len(cols) * line_h + 30

    h1 = sum(get_tbl_height(t) for t in col1_tables)
    h2 = sum(get_tbl_height(t) for t in col2_tables)
    canvas_h = max(h1, h2) + 200

    img = Image.new("RGB", (canvas_w, canvas_h), "#F8FAFC")
    draw = ImageDraw.Draw(img)

    # Main Header
    draw.rectangle([0, 0, canvas_w, 110], fill="#0F172A")
    draw.text((margin, 22), "HEALTHY BITE — COMPLETE PHYSICAL DATA DICTIONARY", font=get_font(28, bold=True), fill="#FFFFFF")
    draw.text((margin, 65), "MySQL Schema `healthy_bite` • 17 Physical Tables • 208 Columns • 26 Foreign Key Constraints • 8 Tracked Macros", font=get_font(15, bold=False), fill="#94A3B8")

    col_widths = [240, 220, 80, 80, 140, 860] # Name, Type, Null, Key, Default, Description/Constraint

    def render_column(tables, start_x, start_y):
        curr_y = start_y
        for tname in tables:
            tinfo = schema_data[tname]
            cols = tinfo["columns"]
            row_count = tinfo.get("row_count", 0)
            desc = table_descriptions.get(tname, "")

            tbl_box_w = col_w
            tbl_box_h = tbl_title_h + header_h + len(cols) * line_h + 8

            # Table Card background
            draw.rounded_rectangle([start_x, curr_y, start_x + tbl_box_w, curr_y + tbl_box_h], radius=8, fill="#FFFFFF", outline="#CBD5E1", width=2)

            # Table Title Bar
            title_bg = "#1E293B" if tname != "admin" else "#64748B"
            draw.rounded_rectangle([start_x, curr_y, start_x + tbl_box_w, curr_y + tbl_title_h], radius=8, fill=title_bg)
            draw.rectangle([start_x, curr_y + tbl_title_h - 8, start_x + tbl_box_w, curr_y + tbl_title_h], fill=title_bg)

            draw.text((start_x + 16, curr_y + 8), f"Table: {tname}", font=get_font(17, bold=True), fill="#FFFFFF")
            badge_text = f"({len(cols)} columns, {row_count} rows)"
            draw.text((start_x + 300, curr_y + 11), badge_text, font=get_font(13, bold=False), fill="#94A3B8")
            draw.text((start_x + 480, curr_y + 11), f"—  {desc}", font=get_font(13, bold=False), fill="#E2E8F0")

            # Column Header Row
            hdr_y = curr_y + tbl_title_h
            draw.rectangle([start_x, hdr_y, start_x + tbl_box_w, hdr_y + header_h], fill="#F1F5F9")
            draw.line([(start_x, hdr_y + header_h), (start_x + tbl_box_w, hdr_y + header_h)], fill="#CBD5E1", width=1)

            headers = ["Field Name", "Data Type", "Null", "Key", "Default", "Constraints / Referential Rules"]
            hx = start_x + 12
            for i, h in enumerate(headers):
                draw.text((hx, hdr_y + 7), h, font=get_font(12, bold=True), fill="#334155")
                hx += col_widths[i]

            # Columns Rows
            row_y = hdr_y + header_h
            for idx, c in enumerate(cols):
                bg_color = "#FFFFFF" if idx % 2 == 0 else "#F8FAFC"
                draw.rectangle([start_x + 2, row_y, start_x + tbl_box_w - 2, row_y + line_h], fill=bg_color)

                cname = c["COLUMN_NAME"]
                ctype = c["COLUMN_TYPE"]
                cnull = c["IS_NULLABLE"]
                ckey = c["COLUMN_KEY"]
                cdef = str(c["COLUMN_DEFAULT"]) if c["COLUMN_DEFAULT"] is not None else "NULL"
                if c["EXTRA"] == "auto_increment":
                    cdef = "AUTO_INC"

                # Build constraint info
                extra_info = ""
                if ckey == "PRI":
                    extra_info = "[PRIMARY KEY]"
                elif ckey == "UNI":
                    extra_info = "[UNIQUE KEY]"

                # Check if FK
                for fk in tinfo.get("foreign_keys", []):
                    if fk["COLUMN_NAME"] == cname:
                        extra_info += f" -> {fk['REFERENCED_TABLE_NAME']}({fk['REFERENCED_COLUMN_NAME']})"

                if "adjustment" in cname:
                    extra_info += " (Scales nutritional macros)"

                rx = start_x + 12
                # Field Name
                draw.text((rx, row_y + 4), cname, font=get_font(11, bold=True, mono=True), fill="#0F172A")
                rx += col_widths[0]

                # Data Type
                draw.text((rx, row_y + 4), ctype, font=get_font(11, bold=False, mono=True), fill="#0369A1")
                rx += col_widths[1]

                # Nullable
                draw.text((rx, row_y + 4), cnull, font=get_font(11, bold=False), fill="#475569")
                rx += col_widths[2]

                # Key Badge
                kcolor = "#DC2626" if ckey == "PRI" else ("#D97706" if ckey == "UNI" else ("#2563EB" if ckey == "MUL" else "#64748B"))
                ktext = "PK" if ckey == "PRI" else ("UNI" if ckey == "UNI" else ("FK" if ckey == "MUL" else "-"))
                draw.text((rx, row_y + 4), ktext, font=get_font(11, bold=True), fill=kcolor)
                rx += col_widths[3]

                # Default
                draw.text((rx, row_y + 4), cdef[:14], font=get_font(11, bold=False, mono=True), fill="#64748B")
                rx += col_widths[4]

                # Constraint / Notes
                draw.text((rx, row_y + 4), extra_info[:90], font=get_font(11, bold=False), fill="#059669" if "->" in extra_info else "#334155")

                row_y += line_h

            curr_y += tbl_box_h + 24

    render_column(col1_tables, margin, 140)
    render_column(col2_tables, margin + col_w + gap, 140)

    out_path = r"d:\NEW healthy bite\diagrams\01_Data_Dictionary.png"
    img.save(out_path, "PNG", dpi=(300, 300))
    print(f"Generated Data Dictionary: {out_path} ({canvas_w}x{canvas_h})")

if __name__ == "__main__":
    generate()
