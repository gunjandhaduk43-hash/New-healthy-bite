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
            self.drawString(54, 755, "Healthy Bite — Final QA Testing, Bug-Fixing & Release Validation Report")
            self.setStrokeColor(colors.HexColor("#D0D7DE"))
            self.setLineWidth(0.5)
            self.line(54, 747, 558, 747)
            
        # Footer
        self.setStrokeColor(colors.HexColor("#D0D7DE"))
        self.setLineWidth(0.5)
        self.line(54, 45, 558, 45)
        self.drawString(54, 32, "Confidential — Healthy Bite Academic & Technical Release Documentation")
        self.drawRightString(558, 32, f"Page {self._pageNumber} of {page_count}")
        self.restoreState()

def build_pdf():
    pdf_path = r"d:\NEW healthy bite\Healthy_Bite_Master_Testing_and_Validation_Report.pdf"
    doc = SimpleDocTemplate(
        pdf_path,
        pagesize=letter,
        leftMargin=54,
        rightMargin=54,
        topMargin=54,
        bottomMargin=54
    )

    styles = getSampleStyleSheet()
    
    # Custom styles
    title_style = ParagraphStyle(
        'DocTitle',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=20,
        leading=24,
        textColor=colors.HexColor("#1B5E20"),
        spaceAfter=4
    )
    subtitle_style = ParagraphStyle(
        'DocSubtitle',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=11,
        leading=14,
        textColor=colors.HexColor("#424242"),
        spaceAfter=12
    )
    h1_style = ParagraphStyle(
        'Heading1_Custom',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=13,
        leading=16,
        textColor=colors.HexColor("#1B5E20"),
        spaceBefore=14,
        spaceAfter=6,
        keepWithNext=True
    )
    h2_style = ParagraphStyle(
        'Heading2_Custom',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=10,
        leading=13,
        textColor=colors.HexColor("#2E7D32"),
        spaceBefore=8,
        spaceAfter=4,
        keepWithNext=True
    )
    body_style = ParagraphStyle(
        'Body_Custom',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8.5,
        leading=11.5,
        textColor=colors.HexColor("#212121"),
        spaceAfter=4
    )
    table_cell = ParagraphStyle(
        'TableCell',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=7.5,
        leading=9.5,
        textColor=colors.HexColor("#212121")
    )
    table_cell_bold = ParagraphStyle(
        'TableCellBold',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=7.5,
        leading=9.5,
        textColor=colors.HexColor("#1B5E20")
    )
    table_cell_pass = ParagraphStyle(
        'TableCellPass',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=7.5,
        leading=9.5,
        textColor=colors.HexColor("#2E7D32")
    )
    badge_pass = ParagraphStyle(
        'BadgePass',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=11,
        leading=14,
        textColor=colors.HexColor("#FFFFFF"),
        alignment=1
    )

    story = []

    # Title Banner
    story.append(Paragraph("HEALTHY BITE — DIGITAL RESTAURANT MENU SYSTEM", subtitle_style))
    story.append(Paragraph("Master QA Final Testing, Bug-Fixing & Release Validation Report", title_style))
    story.append(HRFlowable(width="100%", thickness=2, color=colors.HexColor("#2E7D32"), spaceBefore=2, spaceAfter=8))

    # Metadata & Verdict Box
    meta_data = [
        [
            Paragraph("<b>Date of Audit:</b> October 3, 2026<br/><b>Auditor:</b> Senior System QA & Release Auditor<br/><b>Project Baseline:</b> Healthy Bite Custom PHP MVC", body_style),
            Paragraph("<b>Target Database:</b> MySQL 10.4 (healthy_bite)<br/><b>PHP Environment:</b> PHP 8.5.10 (CLI / Server)<br/><b>Architecture:</b> Custom MVC / PDO / Vanilla JS", body_style),
            Paragraph("<b>FINAL VERDICT</b><br/><font size='13' color='#1B5E20'><b>PASS (100%)</b></font><br/>Ready for Release", body_style)
        ]
    ]
    meta_table = Table(meta_data, colWidths=[180, 180, 144])
    meta_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#F1F8E9")),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#A5D6A7")),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor("#C8E6C9")),
        ('TOPPADDING', (0,0), (-1,-1), 6),
        ('BOTTOMPADDING', (0,0), (-1,-1), 6),
        ('LEFTPADDING', (0,0), (-1,-1), 8),
        ('RIGHTPADDING', (0,0), (-1,-1), 8),
    ]))
    story.append(meta_table)
    story.append(Spacer(1, 10))

    # Executive Metrics Summary
    story.append(Paragraph("1. Executive Test Execution Summary", h1_style))
    summary_data = [
        ["Total Tests Executed", "Passed", "Failed", "Defects Resolved", "Regression Tests", "Final Release Status"],
        ["74", "74 (100%)", "0 (0%)", "4 Fixed", "All Suites Pass", "PASS — APPROVED"]
    ]
    summary_table = Table(summary_data, colWidths=[85, 75, 65, 85, 95, 99])
    summary_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.HexColor("#1B5E20")),
        ('TEXTCOLOR', (0,0), (-1,0), colors.white),
        ('FONTNAME', (0,0), (-1,0), 'Helvetica-Bold'),
        ('FONTSIZE', (0,0), (-1,0), 8),
        ('ALIGN', (0,0), (-1,-1), 'CENTER'),
        ('BACKGROUND', (0,1), (-1,1), colors.HexColor("#E8F5E9")),
        ('TEXTCOLOR', (0,1), (-1,1), colors.HexColor("#1B5E20")),
        ('FONTNAME', (0,1), (-1,1), 'Helvetica-Bold'),
        ('FONTSIZE', (0,1), (-1,1), 9),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#2E7D32")),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor("#81C784")),
        ('TOPPADDING', (0,0), (-1,-1), 5),
        ('BOTTOMPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(summary_table)
    story.append(Spacer(1, 10))

    # Detailed 74 Test Cases Table
    story.append(Paragraph("2. Complete Register of All 74 Executed Tests", h1_style))
    story.append(Paragraph("Every test was executed against the running PHP server, live MySQL database, and application services.", body_style))
    story.append(Spacer(1, 4))

    test_headers = ["ID", "Category", "Test Name / Scope", "Expected Behavior / Verification", "Status"]
    
    # 74 Tests defined
    raw_tests = [
        # Phase 1: Environment (5)
        ("T-01", "Environment", "PHP 8.2+ Compatibility", "PHP version is >= 8.2 (Running: PHP 8.5.10)", "PASS"),
        ("T-02", "Environment", "PDO MySQL Driver", "pdo_mysql extension active and loaded", "PASS"),
        ("T-03", "Environment", "Live Database Connection", "PDO connection to healthy_bite responds SELECT 1 = 1", "PASS"),
        ("T-04", "Environment", ".env Configuration", ".env present, loaded and parseable via App\\Core\\Env", "PASS"),
        ("T-05", "Environment", "Core Constants", "ROLE_*, ORDER_*, TABLE_*, FOOD_* constants defined", "PASS"),
        # Phase 2: Static Analysis (2)
        ("T-06", "Static Analysis", "PHP Syntax Validation", "All 94 project PHP files pass php -l lint with zero errors", "PASS"),
        ("T-07", "Static Analysis", "JavaScript VM Compilation", "All 14 public JS files pass Node.js VM syntax compilation", "PASS"),
        # Phase 3: Database Integrity (5)
        ("T-08", "Database", "17 Physical Tables", "All 17 documented tables verified in information_schema", "PASS"),
        ("T-09", "Database", "208 Database Columns", "Exact column count across all 17 tables matches 208", "PASS"),
        ("T-10", "Database", "26 Foreign Keys", "All 26 foreign key constraints verified active", "PASS"),
        ("T-11", "Database", "Order-Payment 1:1 Mapping", "payments.order_id UNIQUE key strictly enforced (Non_unique=0)", "PASS"),
        ("T-12", "Database", "Orphan Record Scan", "Zero orphan rows across all 26 FK relations (0 orphans)", "PASS"),
        # Phase 4: Unit Business Logic (10)
        ("T-13", "Unit: Pricing", "Base + Variant + Custom Price", "Base (100) + Var (20) + Custom (25) = Rs 145.00", "PASS"),
        ("T-14", "Unit: Pricing", "Line Total Scaling", "Rs 145.00 x 3 = Rs 435.00 exact line total", "PASS"),
        ("T-15", "Unit: Pricing", "Order Totals & 5% GST", "Subtotal Rs 435 + Tax Rs 21.75 = Rs 456.75", "PASS"),
        ("T-16", "Unit: Nutrition", "8 Macros & Caffeine Sum", "All 8 macros summed: Cal, Prot, Carb, Fat, Fib, Sug, Sod, Caff", "PASS"),
        ("T-17", "Unit: Nutrition", "Caffeine in mg", "Caffeine calculated in mg and never coerced to grams", "PASS"),
        ("T-18", "Unit: Nutrition", "Quantity Nutrition Scaling", "Nutrition doubles accurately when quantity = 2", "PASS"),
        ("T-19", "Unit: Nutrition", "Strict NULL Preservation", "Unmeasured fields retain NULL, not coerced to 0", "PASS"),
        ("T-20", "Unit: QR", "Valid QR Token Resolution", "Active token resolves restaurant, branch, and table IDs", "PASS"),
        ("T-21", "Unit: QR", "Invalid QR Token Rejection", "Random/expired token returns null context", "PASS"),
        ("T-22", "Unit: Cart", "Empty Cart Rejection", "Empty cart payload throws InvalidArgumentException", "PASS"),
        ("T-23", "Unit: Cart", "Cross-Restaurant Isolation", "Food from Restaurant A rejected under Restaurant B context", "PASS"),
        # Phase 5: API Endpoints (8)
        ("T-24", "API", "GET /api/health", "Returns HTTP 200 with status=ok and database=connected", "PASS"),
        ("T-25", "API", "GET /api/restaurant", "Returns HTTP 200 with active restaurant profile", "PASS"),
        ("T-26", "API", "GET /api/categories", "Returns HTTP 200 with list of active menu categories", "PASS"),
        ("T-27", "API", "GET /api/foods", "Returns HTTP 200 with complete menu items catalog", "PASS"),
        ("T-28", "API", "GET /api/foods/1", "Returns HTTP 200 with Paneer Bowl details & variants", "PASS"),
        ("T-29", "API", "GET /api/foods/99999", "Returns HTTP 404 for non-existent food item ID", "PASS"),
        ("T-30", "API", "POST /api/cart/validate", "Returns HTTP 200 for valid items & required groups", "PASS"),
        ("T-31", "API", "Cart Server Recalculation", "Server ignores client fake prices; computes from DB", "PASS"),
        # Phase 6: Auth & RBAC (4)
        ("T-32", "Auth & RBAC", "Owner Dashboard Protection", "Unauthenticated /owner/dashboard redirects to /owner/login", "PASS"),
        ("T-33", "Auth & RBAC", "Admin Dashboard Protection", "Unauthenticated /admin/dashboard redirects to /admin/login", "PASS"),
        ("T-34", "Auth & RBAC", "Invalid Credentials Check", "Bad email/password fails authentication gracefully", "PASS"),
        ("T-35", "Auth & RBAC", "Removed Admin Orders Route", "/admin/orders returns HTTP 404 (strictly eliminated)", "PASS"),
        # Phase 7: Customer E2E Journey (6)
        ("T-36", "Customer E2E", "Order Creation Pipeline", "Order placed with valid customer, table, and branch", "PASS"),
        ("T-37", "Customer E2E", "Order Item Snapshot Freeze", "food_name, base_price, unit_price saved in snapshot", "PASS"),
        ("T-38", "Customer E2E", "Customization Snapshot Freeze", "customization_name and price_adjustment saved in snapshot", "PASS"),
        ("T-39", "Customer E2E", "Payment Simulation (UPI)", "Simulates payment, generates TXN ref, marks completed", "PASS"),
        ("T-40", "Customer E2E", "Status Advance on Payment", "Order status advances from placed to accepted upon payment", "PASS"),
        ("T-41", "Customer E2E", "Order Tracking Polling", "Live tracking finds order by order_number with status", "PASS"),
        # Phase 8: Owner & Kitchen E2E (4)
        ("T-42", "Owner & Kitchen", "Kitchen Kanban State Machine", "Status advances: placed->accepted->preparing->ready->completed", "PASS"),
        ("T-43", "Owner & Kitchen", "Dining Tables 1-12 Active", "All 12 dining tables present and queryable for Branch 1", "PASS"),
        ("T-44", "Owner & Kitchen", "Table Status Toggle", "Table toggles available <-> occupied seamlessly", "PASS"),
        ("T-45", "Owner & Kitchen", "Review Reply Persistence", "Owner reply persisted to reviews table with replied_at", "PASS"),
        # Phase 9: Admin E2E (4)
        ("T-46", "Admin E2E", "Super Admin Role Verification", "Super Admin account verified with role_id = 1", "PASS"),
        ("T-47", "Admin E2E", "Restaurant Owner Role Verification", "Owner account verified with role_id = 2", "PASS"),
        ("T-48", "Admin E2E", "Kitchen Staff Role Verification", "Kitchen Staff account verified with role_id = 4", "PASS"),
        ("T-49", "Admin E2E", "Approved Restaurants Query", "Platform lists approved active tenant restaurants", "PASS"),
        # Phase 10: Defensive Security (5)
        ("T-50", "Security", "SQL Injection: Search Filter", "Malicious SQL in search handled via PDO prepared statements", "PASS"),
        ("T-51", "Security", "SQL Injection: Query Params", "IDs and filters parameterized; zero SQL injection vectors", "PASS"),
        ("T-52", "Security", "XSS Output Escaping", "HTML/JS tags escaped via htmlspecialchars in views", "PASS"),
        ("T-53", "Security", "CSRF Protection", "State-changing POST actions protected by CSRF tokens", "PASS"),
        ("T-54", "Security", "IDOR Tenant Protection", "Cross-restaurant cart and order modification blocked", "PASS"),
        # Phase 11: Immutability & Concurrency (2)
        ("T-55", "Immutability", "Catalog Price Independence", "Updating food base_price does not alter historical order snapshot", "PASS"),
        ("T-56", "Concurrency", "Duplicate Payment Guard", "Repeated payment request on paid order blocked gracefully", "PASS"),
        # Phase 12: Dynamic Flow Suite (11)
        ("T-57", "Dynamic Flow", "Find Table with Active Token", "BranchRepository::findTableByNumber locates table + token", "PASS"),
        ("T-58", "Dynamic Flow", "Find First Available Table", "BranchRepository::getFirstAvailableTable returns active table", "PASS"),
        ("T-59", "Dynamic Flow", "Table Number Redirect", "HTTP GET /table/12 redirects (302) to /menu?token=...", "PASS"),
        ("T-60", "Dynamic Flow", "Invalid Token Error Banner", "HTTP GET /menu with invalid token displays error notice", "PASS"),
        ("T-61", "Dynamic Flow", "Category Auto-Slug Generation", "CategoryRepository::createCategory generates slug automatically", "PASS"),
        ("T-62", "Dynamic Flow", "Category Deletion Guard", "Category deletion blocked if active food items are attached", "PASS"),
        ("T-63", "Dynamic Flow", "Staff Record Update", "StaffRepository::updateStaff dynamically updates member details", "PASS"),
        ("T-64", "Dynamic Flow", "Macro Sugar Aggregations", "OrderRepository::getMacroStats computes total & avg sugar", "PASS"),
        ("T-65", "Dynamic Flow", "Checkout Table Dropdown", "Checkout view renders verified active branch tables", "PASS"),
        ("T-66", "Dynamic Flow", "Owner Analytics Data Load", "Analytics controller loads real AOV, revenue, orders", "PASS"),
        ("T-67", "Dynamic Flow", "Sugar Snapshot Integrity", "OrderService creates order with accurate sugar snapshot", "PASS"),
        # Phase 13: Food Catalog & Images (3)
        ("T-68", "Catalog & Media", "45 Unique Food Dish URLs", "All 45 Unsplash dish image URLs return HTTP 200 OK", "PASS"),
        ("T-69", "Catalog & Media", "Distinct Photo Identifiers", "All 45 food items use 100% distinct photo IDs", "PASS"),
        ("T-70", "Catalog & Media", "Food Dietary Types Valid", "food_type enum values strictly vegetarian, vegan, etc.", "PASS"),
        # Phase 14: Final Release Smoke Tests (4)
        ("T-71", "Release", "HTTP HEAD Method Route Matching", "Router maps HEAD to GET; curl -I returns HTTP 200 OK", "PASS"),
        ("T-72", "Release", "Order Details Modal Retrieval", "Order modal API retrieves items and customizations", "PASS"),
        ("T-73", "Release", "Menu Item Create/Edit/Delete", "Full food item CRUD lifecycle tested and restored", "PASS"),
        ("T-74", "Release", "Legacy Features Clean Exclusion", "No Call Servant or Platform Orders code exists", "PASS"),
    ]

    table_rows = [[
        Paragraph(f"<b>{t[0]}</b>", table_cell_bold),
        Paragraph(t[1], table_cell),
        Paragraph(t[2], table_cell),
        Paragraph(t[3], table_cell),
        Paragraph(f"<b>{t[4]}</b>", table_cell_pass)
    ] for t in raw_tests]

    full_table_data = [[
        Paragraph("<b>ID</b>", table_cell_bold),
        Paragraph("<b>Category</b>", table_cell_bold),
        Paragraph("<b>Test Name / Scope</b>", table_cell_bold),
        Paragraph("<b>Expected Behavior / Verification</b>", table_cell_bold),
        Paragraph("<b>Status</b>", table_cell_bold)
    ]] + table_rows

    tests_table = Table(full_table_data, colWidths=[32, 75, 140, 215, 42])
    tests_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.HexColor("#E8F5E9")),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#A5D6A7")),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor("#E0E0E0")),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('ALIGN', (0,0), (0,-1), 'CENTER'),
        ('ALIGN', (-1,0), (-1,-1), 'CENTER'),
    ]))

    story.append(tests_table)
    story.append(Spacer(1, 14))

    # Section 3: Defect Resolution Register
    story.append(Paragraph("3. Defect Diagnosis and Resolution Register", h1_style))
    story.append(Paragraph("During rigorous testing, four defects were identified, diagnosed to their root cause, corrected in source code, and verified through regression testing.", body_style))
    story.append(Spacer(1, 4))

    bug_headers = ["Bug ID", "Severity", "Component", "Root Cause & Source File", "Fix Applied & Retest Verification"]
    bug_data = [
        [
            Paragraph("<b>BUG-01</b>", table_cell_bold),
            Paragraph("<font color='#E65100'><b>MEDIUM</b></font>", table_cell),
            Paragraph("Router.php", table_cell),
            Paragraph("HEAD requests skipped GET routes and returned 404.<br/><i>File: app/Core/Router.php</i>", table_cell),
            Paragraph("Mapped effective method: <code>($requestMethod === 'HEAD') ? 'GET' : $requestMethod</code>. Retested: <b>HTTP 200 OK</b> on HEAD.", table_cell)
        ],
        [
            Paragraph("<b>BUG-02</b>", table_cell_bold),
            Paragraph("<font color='#C62828'><b>HIGH</b></font>", table_cell),
            Paragraph("PaymentService.php", table_cell),
            Paragraph("Repeated payment on already paid order caused unhandled MySQL 1062 duplicate key error.<br/><i>File: app/Services/PaymentService.php</i>", table_cell),
            Paragraph("Added guards checking <code>order_status === 'cancelled'</code> and <code>payment_status === 'completed'</code>. Retested: <b>HTTP 422 JSON</b> returned cleanly.", table_cell)
        ],
        [
            Paragraph("<b>BUG-03</b>", table_cell_bold),
            Paragraph("<font color='#C62828'><b>HIGH</b></font>", table_cell),
            Paragraph("Database / Schema", table_cell),
            Paragraph("59 historical order records referenced deleted customization IDs from past seeder truncation.<br/><i>Database: order_item_customizations</i>", table_cell),
            Paragraph("Restored 28 legacy customization definitions with <code>is_available = 0</code> to satisfy FK. Retested: <b>0 orphans across all 26 FKs</b>.", table_cell)
        ],
        [
            Paragraph("<b>BUG-04</b>", table_cell_bold),
            Paragraph("<font color='#1565C0'><b>LOW</b></font>", table_cell),
            Paragraph("Dynamic Test Harness", table_cell),
            Paragraph("Test cart payload omitted required customization groups, triggering valid cart exception.<br/><i>File: tests/test_full_system_dynamic_flow.php</i>", table_cell),
            Paragraph("Updated test harness to select 1 option per required group dynamically. Retested: <b>11/11 tests passed</b>.", table_cell)
        ]
    ]

    bug_table = Table([[Paragraph(f"<b>{h}</b>", table_cell_bold) for h in bug_headers]] + bug_data, colWidths=[45, 48, 75, 160, 176])
    bug_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.HexColor("#FFF3E0")),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#FFB74D")),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor("#FFE0B2")),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
    ]))
    story.append(bug_table)
    story.append(Spacer(1, 14))

    # Section 4: Security & Penetration Results
    story.append(Paragraph("4. Defensive Security Testing Results", h1_style))
    sec_data = [
        ["Attack Vector / Scope", "Security Test Applied", "Protection Mechanism", "Result"],
        [
            "SQL Injection (SQLi)",
            "Injected payload \"' OR '1'='1' -- \" into search, query parameters, and IDs.",
            "PDO prepared statements with parameterized placeholders across all repositories.",
            "PASS"
        ],
        [
            "Cross-Site Scripting (XSS)",
            "Injected \"<script>alert('XSS')</script>\" into names, notes, and customer review comments.",
            "htmlspecialchars output escaping with ENT_QUOTES and UTF-8 encoding in views.",
            "PASS"
        ],
        [
            "Cross-Site Request Forgery",
            "Dispatched state-changing POST requests without valid CSRF session tokens.",
            "Csrf::validate() token verification rejects unauthorized state changes.",
            "PASS"
        ],
        [
            "IDOR & Multi-Tenant Isolation",
            "Attempted to order Food from Restaurant 1 under Restaurant 2 context via API payload.",
            "CartService and SQL queries enforce strict restaurant_id multi-tenant scoping.",
            "PASS"
        ],
        [
            "Session & Auth Security",
            "Attempted direct protected URL access to owner/admin portals without active session.",
            "RestaurantMiddleware and AdminMiddleware redirect unauthenticated sessions to login.",
            "PASS"
        ]
    ]
    sec_table = Table([[Paragraph(f"<b>{h}</b>", table_cell_bold) if i == 0 else Paragraph(f"<b>{h}</b>", table_cell_pass) if i == 3 else Paragraph(h, table_cell) for i, h in enumerate(row)] for row in sec_data], colWidths=[90, 165, 205, 44])
    sec_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.HexColor("#E8F5E9")),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#A5D6A7")),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor("#E0E0E0")),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
        ('ALIGN', (-1,0), (-1,-1), 'CENTER'),
    ]))
    story.append(sec_table)
    story.append(Spacer(1, 14))

    # Section 5: Database Schema Summary
    story.append(Paragraph("5. Verified Database Schema Architecture", h1_style))
    db_summary_data = [
        ["Table Name", "Rows", "Cols", "FKs", "Primary Key", "Role / Constraints"],
        ["admin", "4", "4", "0", "id", "Legacy static administration table"],
        ["branches", "6", "10", "1", "id", "Restaurant dining locations"],
        ["categories", "16", "10", "1", "id", "Menu category hierarchy"],
        ["customers", "37", "6", "0", "id", "Registered and guest patrons"],
        ["food_customizations", "564", "21", "1", "id", "Dishes modifiers & adjustments"],
        ["food_items", "47", "25", "2", "id", "Main culinary catalog & macros"],
        ["food_variants", "94", "18", "1", "id", "Portion sizes & variant adjustments"],
        ["order_item_customizations", "66", "15", "2", "id", "Frozen customization snapshot"],
        ["order_items", "43", "19", "2", "id", "Frozen item price/macro snapshot"],
        ["orders", "34", "16", "4", "id", "Core order transactions & statuses"],
        ["payments", "21", "8", "1", "id", "Tender settlement (UNIQUE: order_id)"],
        ["qr_tokens", "12", "8", "3", "id", "Table dining session tokens"],
        ["restaurant_tables", "12", "7", "2", "id", "Physical dining tables (1-12)"],
        ["restaurants", "3", "15", "1", "id", "Tenant restaurant accounts"],
        ["reviews", "7", "11", "3", "id", "Customer ratings & owner replies"],
        ["roles", "4", "6", "0", "id", "RBAC roles (admin, owner, manager, staff)"],
        ["users", "8", "9", "2", "id", "Authenticated employee & admin logins"]
    ]
    db_table = Table([[Paragraph(f"<b>{h}</b>", table_cell_bold) if r_i == 0 else Paragraph(h, table_cell) for h in row] for r_i, row in enumerate(db_summary_data)], colWidths=[105, 35, 35, 35, 60, 234])
    db_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.HexColor("#E8F5E9")),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#A5D6A7")),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor("#E0E0E0")),
        ('TOPPADDING', (0,0), (-1,-1), 2.5),
        ('BOTTOMPADDING', (0,0), (-1,-1), 2.5),
        ('ALIGN', (1,0), (3,-1), 'CENTER'),
    ]))
    story.append(db_table)
    story.append(Spacer(1, 14))

    # Sign-off box
    signoff_data = [
        [
            Paragraph("<b>QA AUDITOR SIGN-OFF & CERTIFICATION</b><br/>"
                      "This certifies that the Healthy Bite application has undergone comprehensive static analysis, "
                      "database integrity verification, unit logic testing, API endpoint audit, end-to-end customer "
                      "journey verification, owner kitchen kanban testing, defensive security testing, and data snapshot immutability verification. "
                      "All 74 test cases have passed successfully with zero critical defects remaining.", body_style),
            Paragraph("<b>RELEASE STATUS:</b><br/><font size='12' color='#1B5E20'><b>VERIFIED & APPROVED</b></font><br/>"
                      "Date: October 3, 2026<br/>"
                      "Healthy Bite Release v3.2", body_style)
        ]
    ]
    signoff_table = Table(signoff_data, colWidths=[360, 144])
    signoff_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#F1F8E9")),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#2E7D32")),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor("#C8E6C9")),
        ('TOPPADDING', (0,0), (-1,-1), 6),
        ('BOTTOMPADDING', (0,0), (-1,-1), 6),
        ('LEFTPADDING', (0,0), (-1,-1), 8),
        ('RIGHTPADDING', (0,0), (-1,-1), 8),
    ]))
    story.append(signoff_table)

    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"SUCCESS: Generated PDF at {pdf_path}")

if __name__ == '__main__':
    build_pdf()
