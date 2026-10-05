"""
Healthy Bite Diagram Rendering Engine
Provides clean, anti-aliased drawing primitives for UML, ERD, DFD, and Flowcharts
using Pillow (PIL) and TrueType fonts.
"""

import math
from PIL import Image, ImageDraw, ImageFont

# Font paths
FONT_REGULAR = r"C:\Windows\Fonts\segoeui.ttf"
FONT_BOLD = r"C:\Windows\Fonts\segoeuib.ttf"
FONT_SEMI = r"C:\Windows\Fonts\segoeuisl.ttf"
FONT_MONO = r"C:\Windows\Fonts\consola.ttf"

def get_font(size=14, bold=False, mono=False):
    fpath = FONT_MONO if mono else (FONT_BOLD if bold else FONT_REGULAR)
    try:
        return ImageFont.truetype(fpath, size)
    except Exception:
        return ImageFont.load_default()

def draw_rounded_rect(draw, box, radius=10, fill=None, outline="#000000", width=2):
    x1, y1, x2, y2 = box
    draw.rounded_rectangle([x1, y1, x2, y2], radius=radius, fill=fill, outline=outline, width=width)

def draw_oval(draw, box, fill=None, outline="#000000", width=2):
    x1, y1, x2, y2 = box
    draw.ellipse([x1, y1, x2, y2], fill=fill, outline=outline, width=width)

def draw_diamond(draw, center, w, h, fill=None, outline="#000000", width=2):
    cx, cy = center
    hw, hh = w // 2, h // 2
    points = [(cx, cy - hh), (cx + hw, cy), (cx, cy + hh), (cx - hw, cy)]
    draw.polygon(points, fill=fill, outline=outline)
    if width > 1:
        draw.line(points + [points[0]], fill=outline, width=width)

def draw_arrow(draw, p1, p2, fill="#000000", width=2, arrow_size=10, dashed=False):
    x1, y1 = p1
    x2, y2 = p2
    
    if dashed:
        # Draw dashed line
        dist = math.hypot(x2 - x1, y2 - y1)
        if dist > 0:
            dash_len = 8
            num_dashes = int(dist // (dash_len * 2))
            dx = (x2 - x1) / dist
            dy = (y2 - y1) / dist
            for i in range(num_dashes):
                sx = x1 + (2 * i * dash_len) * dx
                sy = y1 + (2 * i * dash_len) * dy
                ex = x1 + ((2 * i + 1) * dash_len) * dx
                ey = y1 + ((2 * i + 1) * dash_len) * dy
                draw.line([(sx, sy), (ex, ey)], fill=fill, width=width)
    else:
        draw.line([p1, p2], fill=fill, width=width)

    # Arrow head
    angle = math.atan2(y2 - y1, x2 - x1)
    angle1 = angle + math.pi * 5 / 6
    angle2 = angle - math.pi * 5 / 6
    
    head1 = (x2 + arrow_size * math.cos(angle1), y2 + arrow_size * math.sin(angle1))
    head2 = (x2 + arrow_size * math.cos(angle2), y2 + arrow_size * math.sin(angle2))
    
    draw.polygon([p2, head1, head2], fill=fill)

def draw_orthogonal_arrow(draw, points, fill="#000000", width=2, arrow_size=10, dashed=False):
    for i in range(len(points) - 2):
        draw.line([points[i], points[i+1]], fill=fill, width=width)
    if len(points) >= 2:
        draw_arrow(draw, points[-2], points[-1], fill=fill, width=width, arrow_size=arrow_size, dashed=dashed)

def draw_bullseye(draw, center, radius=14, ring_color="#000000", dot_color="#000000", width=2):
    cx, cy = center
    # Outer ring
    draw.ellipse([cx - radius, cy - radius, cx + radius, cy + radius], fill="#FFFFFF", outline=ring_color, width=width)
    # Inner solid dot
    inner_r = radius - 4
    draw.ellipse([cx - inner_r, cy - inner_r, cx + inner_r, cy + inner_r], fill=dot_color, outline=None)

def draw_start_node(draw, center, radius=12, fill="#000000"):
    cx, cy = center
    draw.ellipse([cx - radius, cy - radius, cx + radius, cy + radius], fill=fill, outline=None)

def draw_text_centered(draw, text, center, font, fill="#000000"):
    cx, cy = center
    bbox = draw.textbbox((0, 0), text, font=font)
    tw = bbox[2] - bbox[0]
    th = bbox[3] - bbox[1]
    draw.text((cx - tw // 2, cy - th // 2 - bbox[1]), text, font=font, fill=fill)

def draw_multiline_centered(draw, lines, center, font, fill="#000000", line_gap=4):
    cx, cy = center
    total_h = 0
    line_metrics = []
    for l in lines:
        bb = draw.textbbox((0, 0), l, font=font)
        w = bb[2] - bb[0]
        h = bb[3] - bb[1]
        line_metrics.append((l, w, h, bb[1]))
        total_h += h + line_gap
    total_h -= line_gap
    
    curr_y = cy - total_h // 2
    for l, w, h, top_off in line_metrics:
        draw.text((cx - w // 2, curr_y - top_off), l, font=font, fill=fill)
        curr_y += h + line_gap

def draw_header_banner(draw, title, subtitle, width, height=90, bg_color="#1E293B", title_color="#FFFFFF", sub_color="#94A3B8"):
    draw.rectangle([0, 0, width, height], fill=bg_color)
    tfont = get_font(24, bold=True)
    sfont = get_font(13, bold=False)
    
    t_bb = draw.textbbox((0, 0), title, font=tfont)
    draw.text((30, 20), title, font=tfont, fill=title_color)
    draw.text((30, 54), subtitle, font=sfont, fill=sub_color)

def draw_dfd_process(draw, center, r, p_num, p_name, fill="#F8FAFC", outline="#0284C7", width=2):
    cx, cy = center
    draw.ellipse([cx - r, cy - r, cx + r, cy + r], fill=fill, outline=outline, width=width)
    # Dividing chord or clean process number
    nfont = get_font(13, bold=True)
    draw_text_centered(draw, p_num, (cx, cy - r // 2), nfont, fill="#0369A1")
    draw.line([(cx - int(r * 0.8), cy - r // 4), (cx + int(r * 0.8), cy - r // 4)], fill=outline, width=1)
    
    # Process text
    tfont = get_font(12, bold=False)
    words = p_name.split(" ")
    lines = []
    curr = ""
    for w in words:
        if len(curr + " " + w) > 16:
            lines.append(curr.strip())
            curr = w
        else:
            curr += " " + w
    if curr.strip():
        lines.append(curr.strip())
    draw_multiline_centered(draw, lines, (cx, cy + r // 4 + 4), tfont, fill="#0F172A", line_gap=2)

def draw_data_store(draw, box, store_id, store_name, fill="#F1F5F9", outline="#334155", width=2):
    x1, y1, x2, y2 = box
    draw.rectangle([x1, y1, x2, y2], fill=fill, outline=None)
    # Open ended rectangle (top and bottom lines, left line, open right)
    draw.line([(x1, y1), (x2, y1)], fill=outline, width=width)
    draw.line([(x1, y2), (x2, y2)], fill=outline, width=width)
    draw.line([(x1, y1), (x1, y2)], fill=outline, width=width)
    
    # Dividing line for ID
    id_w = 46
    draw.line([(x1 + id_w, y1), (x1 + id_w, y2)], fill=outline, width=width)
    
    id_font = get_font(12, bold=True)
    draw_text_centered(draw, store_id, (x1 + id_w // 2, (y1 + y2) // 2), id_font, fill="#1E293B")
    
    name_font = get_font(12, bold=True)
    draw_text_centered(draw, store_name, (x1 + id_w + (x2 - x1 - id_w) // 2, (y1 + y2) // 2), name_font, fill="#0F172A")

def draw_actor_stickman(draw, center, label, color="#0F172A", font=None):
    cx, cy = center
    if font is None:
        font = get_font(12, bold=True)
    
    # Head
    head_r = 14
    head_cy = cy - 24
    draw.ellipse([cx - head_r, head_cy - head_r, cx + head_r, head_cy + head_r], outline=color, width=2, fill="#FFFFFF")
    
    # Spine
    neck_y = head_cy + head_r
    pelvis_y = neck_y + 26
    draw.line([(cx, neck_y), (cx, pelvis_y)], fill=color, width=2)
    
    # Arms
    arm_y = neck_y + 10
    draw.line([(cx - 20, arm_y + 4), (cx, arm_y), (cx + 20, arm_y + 4)], fill=color, width=2)
    
    # Legs
    leg_len = 24
    draw.line([(cx, pelvis_y), (cx - 16, pelvis_y + leg_len)], fill=color, width=2)
    draw.line([(cx, pelvis_y), (cx + 16, pelvis_y + leg_len)], fill=color, width=2)
    
    # Label
    draw_text_centered(draw, label, (cx, pelvis_y + leg_len + 14), font, fill=color)
