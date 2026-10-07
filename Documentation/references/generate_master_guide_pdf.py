import os
import sys
from reportlab.lib.pagesizes import letter
from reportlab.lib import colors
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak, KeepTogether, HRFlowable
)
from reportlab.pdfgen import canvas

class NumberedCanvas(canvas.Canvas):
    def __init__(self, *args, **kwargs):
        super(NumberedCanvas, self).__init__(*args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self.draw_page_decorations(num_pages)
            super(NumberedCanvas, self).showPage()
        super(NumberedCanvas, self).save()

    def draw_page_decorations(self, page_count):
        self.saveState()
        self.setFont("Helvetica", 8)
        self.setFillColor(colors.HexColor("#555555"))
        
        # Header (pages > 1)
        if self._pageNumber > 1:
            self.drawString(54, 755, "Healthy Bite — Complete Project Understanding & Beginner Technical Guide")
            self.setStrokeColor(colors.HexColor("#D0D7DE"))
            self.setLineWidth(0.5)
            self.line(54, 747, 558, 747)
            
        # Footer
        self.setStrokeColor(colors.HexColor("#D0D7DE"))
        self.setLineWidth(0.5)
        self.line(54, 45, 558, 45)
        self.drawString(54, 32, "Confidential — Healthy Bite Digital Restaurant Menu & Food Ordering System")
        self.drawRightString(558, 32, f"Page {self._pageNumber} of {page_count}")
        self.restoreState()

def build_pdf():
    pdf_path = r"d:\NEW healthy bite\Documentation\HEALTHY_BITE_BEGINNER_MASTER_GUIDE.pdf"
    doc = SimpleDocTemplate(
        pdf_path,
        pagesize=letter,
        leftMargin=54,
        rightMargin=54,
        topMargin=54,
        bottomMargin=54
    )
    
    styles = getSampleStyleSheet()
    
    # Custom palette
    PRIMARY = colors.HexColor("#1A5632")   # Healthy Bite Emerald Green
    SECONDARY = colors.HexColor("#2D7A46") # Mid Forest Green
    ACCENT = colors.HexColor("#E8F5E9")    # Light Mint
    TEXT_DARK = colors.HexColor("#1C1E21")
    TEXT_MUTED = colors.HexColor("#555555")
    BORDER_COLOR = colors.HexColor("#D0D7DE")
    
    title_style = ParagraphStyle(
        'DocTitle',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=24,
        leading=30,
        textColor=PRIMARY,
        alignment=1, # Center
        spaceAfter=10
    )
    
    subtitle_style = ParagraphStyle(
        'DocSubtitle',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=12,
        leading=16,
        textColor=TEXT_MUTED,
        alignment=1,
        spaceAfter=25
    )
    
    h1_style = ParagraphStyle(
        'SectionH1',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=14,
        leading=18,
        textColor=PRIMARY,
        spaceBefore=14,
        spaceAfter=8,
        keepWithNext=True
    )
    
    h2_style = ParagraphStyle(
        'SectionH2',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=11,
        leading=15,
        textColor=SECONDARY,
        spaceBefore=10,
        spaceAfter=5,
        keepWithNext=True
    )
    
    body_style = ParagraphStyle(
        'DocBody',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9.5,
        leading=13.5,
        textColor=TEXT_DARK,
        spaceAfter=6
    )

    code_style = ParagraphStyle(
        'CodeSnippet',
        parent=styles['Normal'],
        fontName='Courier',
        fontSize=8,
        leading=10.5,
        textColor=colors.HexColor("#0f3d1e"),
        backColor=colors.HexColor("#F6F8FA"),
        borderColor=BORDER_COLOR,
        borderWidth=0.5,
        borderPadding=6,
        spaceBefore=4,
        spaceAfter=6
    )
    
    callout_style = ParagraphStyle(
        'Callout',
        parent=styles['Normal'],
        fontName='Helvetica-Oblique',
        fontSize=9,
        leading=13,
        textColor=PRIMARY,
        backColor=ACCENT,
        borderColor=SECONDARY,
        borderWidth=0.5,
        borderPadding=8,
        spaceBefore=6,
        spaceAfter=8
    )

    story = []
    
    # Title Cover Elements
    story.append(Spacer(1, 20))
    story.append(Paragraph("HEALTHY BITE", title_style))
    story.append(Paragraph("Digital Restaurant Menu & Food Ordering System", ParagraphStyle('SubHead1', parent=subtitle_style, fontSize=14, leading=18, textColor=SECONDARY)))
    story.append(Paragraph("Complete Project Understanding, Architecture, File Guide, Local Execution & Viva Preparation Guide", subtitle_style))
    story.append(HRFlowable(width="100%", thickness=1.5, color=PRIMARY, spaceAfter=20))
    
    # Overview Callout
    story.append(Paragraph(
        "<b>Executive Summary for Beginners:</b> This comprehensive technical manual is generated directly from the Healthy Bite production codebase. It details the custom Model-View-Controller (MVC) architecture, the zero-dependency PSR-4 autoloader, the 17-table relational MySQL schema, the real-time nutrition calculation engine, QR token security validation, local XAMPP execution, and step-by-step viva voce defense.",
        callout_style
    ))
    story.append(Spacer(1, 10))

    # Section 1: System Overview & Tech Stack
    story.append(Paragraph("1. Technology Stack & Core Identity", h1_style))
    tech_data = [
        [Paragraph("<b>Component</b>", body_style), Paragraph("<b>Technology / Implementation</b>", body_style), Paragraph("<b>Architectural Role in Healthy Bite</b>", body_style)],
        [Paragraph("<b>Backend Core</b>", body_style), Paragraph("PHP >= 8.0 (Strict Typing)", body_style), Paragraph("Server-side request processing, security guards, session management.", body_style)],
        [Paragraph("<b>Database</b>", body_style), Paragraph("MySQL / MariaDB (InnoDB, utf8mb4)", body_style), Paragraph("Stores 17 relational tables with foreign keys and cascade integrity.", body_style)],
        [Paragraph("<b>Data Layer</b>", body_style), Paragraph("PDO (PHP Data Objects)", body_style), Paragraph("Singleton connection, prepared statements, SQL injection immunity.", body_style)],
        [Paragraph("<b>Architecture</b>", body_style), Paragraph("Custom Lightweight MVC", body_style), Paragraph("Front Controller, dynamic regex router, repository pattern.", body_style)],
        [Paragraph("<b>Autoloading</b>", body_style), Paragraph("Custom PSR-4 Fallback Autoloader", body_style), Paragraph("Zero external dependencies. Runs standalone without Composer vendor.", body_style)],
        [Paragraph("<b>Frontend UI</b>", body_style), Paragraph("HTML5 + Modular CSS3 + ES6 JS", body_style), Paragraph("Fast, responsive customer menu, live modals, and floating cart.", body_style)],
        [Paragraph("<b>Auth Engine</b>", body_style), Paragraph("Bcrypt (Cost 12) + PHP Sessions", body_style), Paragraph("Session fixation regeneration, role-based isolation (Owner/Staff/Admin).", body_style)]
    ]
    t_tech = Table(tech_data, colWidths=[110, 160, 234])
    t_tech.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), ACCENT),
        ('GRID', (0,0), (-1,-1), 0.5, BORDER_COLOR),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
    ]))
    story.append(t_tech)
    story.append(Spacer(1, 14))

    # Section 2: Complete Architecture Flow
    story.append(Paragraph("2. System Architecture & Request Lifecycle", h1_style))
    story.append(Paragraph("Every HTTP request entering Healthy Bite travels through a clean, deterministic pipeline:", body_style))
    
    flow_steps = (
        "1. Web Browser (Diner scans QR or Owner logs in)\n"
        "2. Apache / Web Server receives request -> .htaccess routes silently to public/index.php\n"
        "3. public/index.php initializes App\\Core\\App -> Session::start() -> PSR-4 Autoloader\n"
        "4. App\\Core\\Router matches URL regex against routes/web.php or routes/api.php\n"
        "5. Controller executes -> delegates business rules to Domain Services (Pricing, Nutrition, QR)\n"
        "6. Repositories execute safe parameterized PDO queries against MySQL\n"
        "7. Controller renders HTML view template (wrapped in Layout) or returns JSON API response\n"
        "8. Browser receives response -> JavaScript handles live updates without full reloads"
    )
    story.append(Paragraph(flow_steps.replace("\n", "<br/>"), code_style))
    story.append(Spacer(1, 14))

    # Section 3: Folder Structure
    story.append(Paragraph("3. Directory Structure & Key Responsibilities", h1_style))
    dir_data = [
        [Paragraph("<b>Directory</b>", body_style), Paragraph("<b>Key Responsibilities & Contents</b>", body_style), Paragraph("<b>Safety Level</b>", body_style)],
        [Paragraph("<b>app/Core/</b>", body_style), Paragraph("The engine: App.php, Router.php, Database.php, Request.php, Response.php, Session.php.", body_style), Paragraph("<font color='#B30000'><b>DO NOT TOUCH</b></font>", body_style)],
        [Paragraph("<b>app/Controllers/</b>", body_style), Paragraph("Request handlers: MenuController, OrderController, Owner/*, Admin/*.", body_style), Paragraph("<font color='#D97706'><b>EDIT CAREFULLY</b></font>", body_style)],
        [Paragraph("<b>app/Repositories/</b>", body_style), Paragraph("Database query layer: FoodRepository, OrderRepository, RestaurantRepository.", body_style), Paragraph("<font color='#D97706'><b>EDIT CAREFULLY</b></font>", body_style)],
        [Paragraph("<b>app/Services/</b>", body_style), Paragraph("Pure business calculations: NutritionService, PricingService, QrService, CartService.", body_style), Paragraph("<font color='#D97706'><b>EDIT CAREFULLY</b></font>", body_style)],
        [Paragraph("<b>config/</b>", body_style), Paragraph("Global application constants, tax rates, role IDs, and PDO database configs.", body_style), Paragraph("<font color='#B30000'><b>DO NOT TOUCH</b></font>", body_style)],
        [Paragraph("<b>public/</b>", body_style), Paragraph("Web root: index.php (entry point) + assets/ (CSS stylesheets, JS logic, images).", body_style), Paragraph("<font color='#1A5632'><b>SAFE (Assets)</b></font>", body_style)],
        [Paragraph("<b>resources/views/</b>", body_style), Paragraph("HTML presentation: customer/menu.php, owner/kitchen_orders.php, layouts/.", body_style), Paragraph("<font color='#1A5632'><b>SAFE TO EDIT</b></font>", body_style)],
        [Paragraph("<b>routes/</b>", body_style), Paragraph("URL route registries: web.php (browser endpoints) and api.php (JSON endpoints).", body_style), Paragraph("<font color='#D97706'><b>EDIT CAREFULLY</b></font>", body_style)]
    ]
    t_dir = Table(dir_data, colWidths=[90, 314, 100])
    t_dir.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), ACCENT),
        ('GRID', (0,0), (-1,-1), 0.5, BORDER_COLOR),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
    ]))
    story.append(t_dir)
    story.append(Spacer(1, 14))

    # Page Break for Database & Core Features
    story.append(PageBreak())

    # Section 4: Database Schema
    story.append(Paragraph("4. Relational Database Schema (All 17 Tables)", h1_style))
    story.append(Paragraph("Healthy Bite utilizes a strictly normalized MariaDB/MySQL database schema containing 17 relational tables:", body_style))

    schema_data = [
        [Paragraph("<b>Table Name</b>", body_style), Paragraph("<b>Primary Key</b>", body_style), Paragraph("<b>Key Foreign Keys & Relational Purpose</b>", body_style)],
        [Paragraph("<b>restaurants</b>", body_style), Paragraph("id", body_style), Paragraph("Master entity for dining establishments; status (pending, approved, rejected).", body_style)],
        [Paragraph("<b>branches</b>", body_style), Paragraph("id", body_style), Paragraph("FK: restaurant_id. Multi-branch physical locations.", body_style)],
        [Paragraph("<b>restaurant_tables</b>", body_style), Paragraph("id", body_style), Paragraph("FK: branch_id. Physical dining tables (T-01, T-02) with capacity and status.", body_style)],
        [Paragraph("<b>qr_tokens</b>", body_style), Paragraph("id", body_style), Paragraph("FK: table_id. Cryptographic tokens (e.g., hb_greenhouse_downtown_t1_sec).", body_style)],
        [Paragraph("<b>categories</b>", body_style), Paragraph("id", body_style), Paragraph("FK: restaurant_id. Food groupings (Bowls, Smoothies, Warm Plates).", body_style)],
        [Paragraph("<b>food_items</b>", body_style), Paragraph("id", body_style), Paragraph("FK: restaurant_id, category_id. Base price, calories, protein, carbs, fat, fiber.", body_style)],
        [Paragraph("<b>food_variants</b>", body_style), Paragraph("id", body_style), Paragraph("FK: food_item_id. Portion adjustments (Regular vs Large Athlete Portion).", body_style)],
        [Paragraph("<b>food_customizations</b>", body_style), Paragraph("id", body_style), Paragraph("FK: food_item_id. Add-ons (Extra Grilled Tofu, Olive Oil Dressing).", body_style)],
        [Paragraph("<b>customers</b>", body_style), Paragraph("id", body_style), Paragraph("Customer contact ledger (Name, Mobile, Email).", body_style)],
        [Paragraph("<b>orders</b>", body_style), Paragraph("id", body_style), Paragraph("FK: restaurant_id, branch_id, table_id, customer_id. Order number, totals, status.", body_style)],
        [Paragraph("<b>order_items</b>", body_style), Paragraph("id", body_style), Paragraph("FK: order_id, food_item_id. <b>Frozen historical snapshots</b> of price & macros.", body_style)],
        [Paragraph("<b>order_item_customizations</b>", body_style), Paragraph("id", body_style), Paragraph("FK: order_item_id, customization_id. Snapshot of selected add-ons.", body_style)],
        [Paragraph("<b>payments</b>", body_style), Paragraph("id", body_style), Paragraph("FK: order_id. Payment records (card, upi, cash, net_banking) & status.", body_style)],
        [Paragraph("<b>reviews</b>", body_style), Paragraph("id", body_style), Paragraph("FK: restaurant_id, order_id. Ratings (1-5) and feedback.", body_style)],
        [Paragraph("<b>roles</b>", body_style), Paragraph("id", body_style), Paragraph("Role definitions (1: Super Admin, 2: Owner, 3: Manager, 4: Staff).", body_style)],
        [Paragraph("<b>users</b>", body_style), Paragraph("id", body_style), Paragraph("FK: role_id, restaurant_id. Staff and Owner login accounts with Bcrypt.", body_style)],
        [Paragraph("<b>admin</b>", body_style), Paragraph("id", body_style), Paragraph("System administration and platform governance entity.", body_style)]
    ]
    t_schema = Table(schema_data, colWidths=[120, 54, 330])
    t_schema.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), ACCENT),
        ('GRID', (0,0), (-1,-1), 0.5, BORDER_COLOR),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
    ]))
    story.append(t_schema)
    story.append(Spacer(1, 14))

    # Section 5: Nutrition & Pricing Engines
    story.append(Paragraph("5. Domain Calculation Engines", h1_style))
    story.append(Paragraph("<b>The Nutrition Calculation Engine (NutritionService.php):</b>", h2_style))
    story.append(Paragraph(
        "Nutritional metrics are computed using a strict mathematical formula across 8 nutrient fields: "
        "<i>calories, protein, carbs, fat, fiber, sugar, sodium, and caffeine</i>.<br/>"
        "$$\\text{Final Nutrient} = \\text{Base Nutrient} + \\text{Variant Adjustment} + \\sum (\\text{Customization Adjustment} \\times \\text{Quantity})$$<br/>"
        "<b>NULL Data Integrity Rule:</b> If a nutrient value was never provided by the restaurant, the system strictly preserves <code>NULL</code> rather than converting it to a deceptive '0'. Calories are rounded to integer units, while macronutrients are rounded to 2 decimal places.",
        body_style
    ))
    story.append(Spacer(1, 6))

    story.append(Paragraph("<b>The Dynamic Pricing Engine (PricingService.php):</b>", h2_style))
    story.append(Paragraph(
        "Financial billing calculations follow a deterministic 3-tier calculation pipeline:<br/>"
        "1. <b>Single Configured Item Price:</b> $\\text{Base Price} + \\text{Variant Price Adjustment} + \\sum (\\text{Customization Price} \\times \\text{Qty})$<br/>"
        "2. <b>Line Total:</b> $\\text{Single Item Price} \\times \\text{Food Quantity}$<br/>"
        "3. <b>Order Bill Totals:</b> $\\text{Subtotal} + \\text{Tax (5\\%)} + \\text{Service Charge (0\\%)} = \\text{Total Amount Payable}$",
        body_style
    ))
    story.append(Spacer(1, 14))

    # Page Break for Operations, Kitchen KDS & Testing
    story.append(PageBreak())

    # Section 6: Kitchen Kanban & Order Status
    story.append(Paragraph("6. Operational Workflows: Kitchen Live Orders (KDS Kanban)", h1_style))
    story.append(Paragraph(
        "Healthy Bite includes a real-time Kitchen Display System (KDS) located at <code>/owner/kitchen-orders</code>. "
        "Orders advance across a synchronized Kanban board:",
        body_style
    ))
    
    kds_data = [
        [Paragraph("<b>Status Stage</b>", body_style), Paragraph("<b>Kitchen Meaning</b>", body_style), Paragraph("<b>Trigger / Transition</b>", body_style)],
        [Paragraph("<b>placed</b>", body_style), Paragraph("New order received from customer table QR.", body_style), Paragraph("Chef clicks 'Accept Order' button.", body_style)],
        [Paragraph("<b>preparing</b>", body_style), Paragraph("Kitchen staff actively cooking the meal.", body_style), Paragraph("Chef clicks 'Mark as Ready' button.", body_style)],
        [Paragraph("<b>ready</b>", body_style), Paragraph("Meal plated; waiting for service to table.", body_style), Paragraph("Service staff serves meal, clicks 'Complete'.", body_style)],
        [Paragraph("<b>completed</b>", body_style), Paragraph("Diner served; order archived into daily sales.", body_style), Paragraph("Settled and finalized.", body_style)]
    ]
    t_kds = Table(kds_data, colWidths=[80, 224, 200])
    t_kds.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), ACCENT),
        ('GRID', (0,0), (-1,-1), 0.5, BORDER_COLOR),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
    ]))
    story.append(t_kds)
    story.append(Spacer(1, 14))

    # Section 7: Localhost Setup Guide
    story.append(Paragraph("7. Localhost Execution & Testing Guide", h1_style))
    story.append(Paragraph(
        "<b>Prerequisites:</b> XAMPP (Apache + MariaDB/MySQL) + PHP >= 8.0.<br/>"
        "<b>Step 1:</b> Start Apache and MySQL in XAMPP Control Panel (verify green status).<br/>"
        "<b>Step 2:</b> Open <code>http://localhost/phpmyadmin/</code>, create database <code>healthy_bite</code>, and import <code>database/healthy_bite_infinityfree_dump.sql</code>.<br/>"
        "<b>Step 3:</b> Configure <code>.env</code> with <code>DB_HOST=127.0.0.1</code>, <code>DB_DATABASE=healthy_bite</code>, and <code>DB_USERNAME=root</code>.<br/>"
        "<b>Step 4:</b> Start the built-in server in project root: <code>php -S localhost:8000 -t public</code>.<br/>"
        "<b>Step 5:</b> Open <code>http://localhost:8000/qr/hb_greenhouse_downtown_t1_sec</code> to test customer QR ordering.",
        body_style
    ))
    story.append(Spacer(1, 10))

    # Demo Credentials Table
    story.append(Paragraph("<b>Demo Credentials (All Passwords: Password@123):</b>", h2_style))
    creds_data = [
        [Paragraph("<b>Portal Role</b>", body_style), Paragraph("<b>Login URL</b>", body_style), Paragraph("<b>Demo Email</b>", body_style), Paragraph("<b>Password</b>", body_style)],
        [Paragraph("Restaurant Owner", body_style), Paragraph("/owner/login", body_style), Paragraph("owner@greenhousekitchen.com", body_style), Paragraph("Password@123", body_style)],
        [Paragraph("Kitchen Staff", body_style), Paragraph("/owner/login", body_style), Paragraph("aarav@greenhouse.in", body_style), Paragraph("Password@123", body_style)],
        [Paragraph("Platform Super Admin", body_style), Paragraph("/admin/login", body_style), Paragraph("admin@healthybite.com", body_style), Paragraph("Password@123", body_style)]
    ]
    t_creds = Table(creds_data, colWidths=[110, 110, 204, 80])
    t_creds.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), ACCENT),
        ('GRID', (0,0), (-1,-1), 0.5, BORDER_COLOR),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
    ]))
    story.append(t_creds)
    story.append(Spacer(1, 14))

    # Section 8: Viva Preparation
    story.append(Paragraph("8. Viva Voce Examination Defense", h1_style))
    viva_qa = [
        ("Why custom MVC instead of Laravel?", "Demonstrates full mastery of foundational computer science principles: the Front Controller pattern, PSR-4 autoloading, regular expression routing, and PDO abstraction without framework bloat."),
        ("How is SQL Injection prevented?", "By using PDO prepared statements with strict parameter binding (:id, :token) across all Repository classes. User inputs are never concatenated directly into SQL queries."),
        ("How does the QR token prevent tampering?", "The QR code embeds a random, high-entropy token (/qr/{token}) resolved via multi-table inner joins on the server. The server overrides client parameters with verified database context."),
        ("Why store snapshots in order items?", "To preserve financial and nutritional history. If an owner later updates dish prices or ingredients, past receipts and tax audits remain 100% accurate.")
    ]
    for q, a in viva_qa:
        story.append(Paragraph(f"<b>Q: {q}</b>", h2_style))
        story.append(Paragraph(f"<b>A:</b> {a}", body_style))
        story.append(Spacer(1, 3))

    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"Successfully generated PDF at: {pdf_path}")

if __name__ == '__main__':
    build_pdf()
